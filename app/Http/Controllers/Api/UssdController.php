<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UssdSession;
use App\Services\UssdFlowService;
use App\Support\PhoneNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * POST /api/ussd/callback  — public, hit by Ontech's USSD gateway.
 *
 * Response contract per Ontech docs:
 *   {"response_string": "...", "continue_session": true|false}
 *
 * MUST always return HTTP 200 with valid JSON (any other response shows
 * "Service error" to the subscriber). Even on internal error we return a
 * courteous end-of-session message.
 */
class UssdController extends Controller
{
    public function callback(Request $request, UssdFlowService $flow)
    {
        try {
            // Log EVERY hit up-front (before any validation) so we can spot
            // probes with unexpected field names — the payload is small and
            // this is critical for debugging aggregator integration issues.
            Log::channel('bot_api')->info('USSD raw inbound', [
                'ip'         => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 80),
                'body_keys'  => array_keys($request->all()),
                'body'       => $request->all(),
            ]);

            // Ontech's live gateway sends snake_case field names (session_id,
            // user_input, is_new_request) — NOT the camelCase names in their
            // integration guide. Accept both plus a few other common variants
            // so we're resilient to gateway config changes.
            $sessionId = trim((string) ($request->input('sessionID')
                                     ?? $request->input('session_id')
                                     ?? $request->input('sessionId') ?? ''));
            $msisdnRaw = trim((string) ($request->input('msisdn')
                                     ?? $request->input('phone_number')
                                     ?? $request->input('phone')
                                     ?? $request->input('phoneNumber') ?? ''));
            $input     = (string) ($request->input('user_input')
                                ?? $request->input('input')
                                ?? $request->input('userInput')
                                ?? $request->input('text')
                                ?? '');
            // is_new_request can arrive as a JSON boolean OR a string. Cover
            // both — anything truthy counts as "new session".
            $isNewRaw  = $request->input('is_new_request',
                        $request->input('isnewrequest',
                        $request->input('isNewRequest',
                        $request->input('newRequest'))));
            $isNew     = filter_var($isNewRaw, FILTER_VALIDATE_BOOLEAN,
                                    FILTER_NULL_ON_FAILURE) === true;

            // Belt-and-braces: if the input equals the dialed shortcode
            // (with or without leading *), treat it as a new session even if
            // the isNew flag was missing. Prevents "keystroke lost" scenarios
            // when their gateway config drifts.
            $shortcode = trim((string) ($request->input('shortcode') ?? ''));
            $stripped  = ltrim(rtrim($input, '#'), '*');
            if ($shortcode !== '' && $stripped === ltrim(rtrim($shortcode, '#'), '*')) {
                $isNew = true;
            }

            if ($sessionId === '' || $msisdnRaw === '') {
                Log::channel('bot_api')->warning('USSD missing required fields', [
                    'ip'          => $request->ip(),
                    'has_session' => $sessionId !== '',
                    'has_msisdn'  => $msisdnRaw !== '',
                    'body_keys'   => array_keys($request->all()),
                ]);
                return $this->reply('Service error. Please try again.', false);
            }

            $msisdn = PhoneNormalizer::digits($msisdnRaw) ?: preg_replace('/\D+/', '', $msisdnRaw);
            if (! $msisdn) {
                return $this->reply('Invalid caller number. Please try again.', false);
            }

            // Session: load-or-create. On isNew we always reset to START.
            $session = UssdSession::firstOrNew(['session_id' => $sessionId]);
            if (! $session->exists || $isNew) {
                $session->fill([
                    'session_id' => $sessionId,
                    'msisdn'     => $msisdn,
                    'state'      => 'START',
                    'data'       => [],
                    'transcript' => [],
                    'ended'      => false,
                ]);
                if ($isNew) {
                    // Refresh transcript on redial to keep the session log tidy.
                    $session->transcript = [];
                    $session->data       = [];
                    $session->state      = 'START';
                    $session->ended      = false;
                }
                $session->shortcode = $isNew ? $input : $session->shortcode;
            }

            // Record the inbound keypress (skip the initial dialed code — it's
            // just the shortcode, e.g. "388*10").
            if (! $isNew) {
                $session->appendTranscript('in', $input);
            }
            $session->last_input_at = now();

            $result = $flow->handle($session, $input, $isNew);

            $session->ended = ! ($result['continue'] ?? false);
            $session->save();

            Log::channel('bot_api')->info('USSD hit', [
                'session_id' => $sessionId,
                'msisdn'     => $this->maskPhone($msisdn),
                'state'      => $session->state,
                'is_new'     => $isNew,
                'continue'   => $result['continue'] ?? false,
            ]);

            return $this->reply($result['text'] ?? '', (bool) ($result['continue'] ?? false));
        } catch (\Throwable $e) {
            Log::channel('bot_api')->error('USSD callback crash', ['error' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return $this->reply('Service temporarily unavailable. Please try again in a moment.', false);
        }
    }

    private function reply(string $text, bool $continue)
    {
        return response()->json([
            'response_string'  => $text,
            'continue_session' => $continue,
        ], 200);
    }

    private function maskPhone(?string $p): string
    {
        if (! $p) return '****';
        return substr($p, 0, 5) . '****' . substr($p, -4);
    }
}
