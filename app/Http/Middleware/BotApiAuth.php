<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate for /api/bot/* — accepts requests with a shared bearer token that
 * matches config('services.whatsapp_bot.token'). Timing-safe compare.
 *
 * If services.whatsapp_bot.allowed_ips is a comma-separated list, the
 * request IP must ALSO fall in the allow-list. Empty list = token only.
 * Docker default bridge is 172.17.0.0/16; the compose network we attach
 * the bot to sits in 172.18/19/20/…/x.0.0/16 depending on Docker's assignment.
 */
class BotApiAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('services.whatsapp_bot.token', '');
        if ($expected === '') {
            return response()->json(['message' => 'Bot API not configured.'], 503);
        }

        $header = (string) $request->bearerToken();
        if ($header === '' || ! hash_equals($expected, $header)) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $allowed = (string) config('services.whatsapp_bot.allowed_ips', '');
        if ($allowed !== '') {
            $ip = $request->ip();
            $ok = false;
            foreach (array_filter(array_map('trim', explode(',', $allowed))) as $cidr) {
                if ($this->ipInRange($ip, $cidr)) {
                    $ok = true;
                    break;
                }
            }
            if (! $ok) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }
        }

        return $next($request);
    }

    private function ipInRange(string $ip, string $cidr): bool
    {
        if (! str_contains($cidr, '/')) {
            return $ip === $cidr;
        }
        [$subnet, $bits] = explode('/', $cidr, 2);
        $bits = (int) $bits;
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ipLong = ip2long($ip);
            $subnetLong = ip2long($subnet);
            $mask = -1 << (32 - $bits);
            return ($ipLong & $mask) === ($subnetLong & $mask);
        }
        return false;
    }
}
