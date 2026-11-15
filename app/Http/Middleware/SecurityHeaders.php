<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menambahkan HTTP security headers pada setiap response.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $cdn = 'https://cdn.jsdelivr.net';
        $headers = [
            'X-Frame-Options'           => 'DENY',
            'X-Content-Type-Options'    => 'nosniff',
            'Referrer-Policy'           => 'strict-origin-when-cross-origin',
            'Permissions-Policy'        => 'camera=(), microphone=(), geolocation=()',
            'Cross-Origin-Opener-Policy'=> 'same-origin',
            'Content-Security-Policy'   => "default-src 'self'; script-src 'self' 'unsafe-inline' $cdn; "
                . "style-src 'self' 'unsafe-inline' $cdn https://fonts.googleapis.com; "
                . "font-src 'self' data: $cdn https://fonts.gstatic.com; img-src 'self' data: blob:; "
                . "connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'",
        ];

        if ($request->isSecure()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }
        $response->headers->remove('X-Powered-By');
        header_remove('X-Powered-By');

        return $response;
    }
}
