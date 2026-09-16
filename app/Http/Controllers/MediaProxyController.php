<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

/**
 * Same-origin proxy for remote catalog product photos.
 * Lets the customizer export canvases without CORS tainting,
 * without forcing crossOrigin on the live preview <img>.
 */
class MediaProxyController extends Controller
{
    private const ALLOWED_HOSTS = [
        'www.fullcollection.com',
        'fullcollection.com',
        'cdn.fullcollection.com',
    ];

    public function __invoke(Request $request): Response
    {
        $url = trim((string) $request->query('url', ''));
        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            abort(400, 'Invalid URL');
        }

        $parts = parse_url($url);
        $host = strtolower($parts['host'] ?? '');
        $scheme = strtolower($parts['scheme'] ?? '');

        if (! in_array($scheme, ['http', 'https'], true) || ! $this->hostAllowed($host)) {
            abort(403, 'Host not allowed');
        }

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'Accept' => 'image/*,*/*;q=0.8',
                    'User-Agent' => 'ThreadsEmbroideryMediaProxy/1.0',
                ])
                ->get($url);
        } catch (\Throwable) {
            abort(502, 'Upstream fetch failed');
        }

        if (! $response->successful()) {
            abort(502, 'Upstream returned '.$response->status());
        }

        $contentType = $response->header('Content-Type') ?: 'image/jpeg';
        if (! str_starts_with(strtolower($contentType), 'image/')) {
            abort(415, 'Not an image');
        }

        return response($response->body(), 200, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    private function hostAllowed(string $host): bool
    {
        foreach (self::ALLOWED_HOSTS as $allowed) {
            if ($host === $allowed || str_ends_with($host, '.'.$allowed)) {
                return true;
            }
        }

        return false;
    }
}
