<?php

namespace App\Console\Commands;

use App\Models\MessageBroadcast;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Resend the SMS rows on a broadcast that are still `status = failed` and
 * whose failure looks retriable (rate-limit, timeout, network). Rejects
 * from the provider (bad phone number, invalid content) are skipped.
 *
 *   php artisan comms:retry-broadcast 1
 *   php artisan comms:retry-broadcast 1 --limit=50
 *   php artisan comms:retry-broadcast 1 --include-bad-numbers      (retry even 103)
 *
 * Paces at 1.05 s / send to stay under CloudServiceZm's 60 req/min ceiling,
 * and backs off 5 s if the gateway returns a 104 rate-limit response.
 */
class CommsRetryBroadcast extends Command
{
    protected $signature = 'comms:retry-broadcast {id : broadcast id} {--limit=0 : cap the number of rows} {--include-bad-numbers : retry even provider status:103 rejects}';
    protected $description = 'Retry failed SMS on a broadcast (rate-limit / timeout / network only unless --include-bad-numbers).';

    public function handle(): int
    {
        $broadcast = MessageBroadcast::find((int) $this->argument('id'));
        if (! $broadcast) {
            $this->error("Broadcast #{$this->argument('id')} not found.");
            return self::FAILURE;
        }

        $from = $broadcast->created_at->toDateTimeString();
        $to   = now()->toDateTimeString();
        $includeBad = (bool) $this->option('include-bad-numbers');
        $limit = (int) $this->option('limit');

        $query = DB::table('sms_logs')
            ->where('status', 'failed')
            ->whereBetween('created_at', [$from, $to]);

        if (! $includeBad) {
            $query->where(function ($q) {
                $q->whereNull('error_message')
                  ->orWhere('error_message', 'not like', '%"status":103%');
            });
        }
        if ($limit > 0) $query->limit($limit);

        $rows = $query->get();
        $total = $rows->count();
        $this->info("Retrying {$total} failed rows on broadcast #{$broadcast->id} \"{$broadcast->title}\"");

        if ($total === 0) return self::SUCCESS;

        $success = 0; $failed = 0; $spend = 0.0;
        $bar = $this->output->createProgressBar($total);

        foreach ($rows as $row) {
            $formattedPhone = $this->formatPhone((string) $row->recipient);
            $body = $row->message;

            $queryParams = http_build_query([
                'username'  => env('SMS_USERNAME', 'Blessmore'),
                'password'  => env('SMS_PASSWORD', 'Blessmore'),
                'msg'       => str_replace('@', '(at)', $body),
                'shortcode' => env('SMS_SHORTCODE', '2343'),
                'sender_id' => env('SMS_SENDER_ID', 'StFrancis'),
                'phone'     => '+' . $formattedPhone,
                'api_key'   => env('SMS_API_KEY', '121231313213123123'),
            ]);
            $apiUrl = env('SMS_API_URL', 'https://www.cloudservicezm.com/smsservice/httpapi');

            try {
                $resp = Http::withoutVerifying()
                    ->timeout(15)
                    ->connectTimeout(10)
                    ->retry(2, 1000)
                    ->get($apiUrl . '?' . $queryParams);

                $rBody = trim($resp->body());
                $rJson = $resp->json();
                $ok = $resp->successful() && (
                       (is_array($rJson) && (int) ($rJson['status'] ?? 0) === 100)
                    || (is_array($rJson) && strtolower((string) ($rJson['message'] ?? '')) === 'success')
                    || strtolower($rBody) === 'success'
                );

                DB::table('sms_logs')->where('id', $row->id)->update([
                    'status'             => $ok ? 'sent' : 'failed',
                    'provider_reference' => $resp->json('message_id') ?? null,
                    'error_message'      => $ok ? null : $rBody,
                    'updated_at'         => now(),
                ]);

                if ($ok) {
                    $success++;
                    $spend += (float) $row->cost;
                } else {
                    $failed++;
                    if (is_array($rJson) && (int) ($rJson['status'] ?? 0) === 104) {
                        // rate limited — wait for the window to clear
                        sleep(5);
                    }
                }
            } catch (\Throwable $e) {
                $failed++;
                DB::table('sms_logs')->where('id', $row->id)->update([
                    'status'        => 'failed',
                    'error_message' => $e->getMessage(),
                    'updated_at'    => now(),
                ]);
                Log::error('comms:retry-broadcast exception', ['sms_log_id' => $row->id, 'error' => $e->getMessage()]);
            }

            $bar->advance();
            usleep(1050000); // pace under 60 req/min
        }

        $bar->finish();
        $this->newLine();

        // Recount from sms_logs — authoritative
        $counts = DB::selectOne("
            SELECT
              SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS sent,
              SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS failed,
              SUM(CASE WHEN status = ? THEN cost ELSE 0 END) AS spend
              FROM sms_logs
             WHERE created_at BETWEEN ? AND ?
        ", ['sent', 'failed', 'sent', $from, $to]);

        $broadcast->update([
            'sent_count'   => (int) $counts->sent,
            'failed_count' => (int) $counts->failed,
            'total_cost'   => (float) ($counts->spend ?? 0),
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        $this->info("Retry pass: {$success} succeeded · {$failed} still failed · +K" . number_format($spend, 2) . " spent");
        $this->info("Broadcast #{$broadcast->id} now: sent={$broadcast->fresh()->sent_count} · failed={$broadcast->fresh()->failed_count} · cost=K" . number_format($broadcast->fresh()->total_cost, 2));

        return self::SUCCESS;
    }

    private function formatPhone(string $raw): string
    {
        $p = preg_replace('/[^0-9]/', '', $raw);
        if (str_starts_with($p, '260'))            return $p;
        if (str_starts_with($p, '0'))              return '260' . substr($p, 1);
        if (strlen($p) === 9)                      return '260' . $p;
        return $p;
    }
}
