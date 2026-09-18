<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PageView;

class TrackPageViews
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track successful GET requests for public web pages
        if (
            $request->isMethod('GET') &&
            !$request->ajax() &&
            !$request->pjax() &&
            !$request->expectsJson() &&
            !$request->is('admin*') &&
            !$request->is('api*') &&
            !$request->is('storage*') &&
            !$request->is('up') &&
            !$request->is('robots.txt') &&
            !$request->is('sitemap.xml') &&
            !$request->is('favicon.ico') &&
            !$request->is('currency/*')
        ) {
            try {
                $sessionId = $request->session()->getId();
                if ($sessionId) {
                    $path = '/' . ltrim($request->path(), '/');

                    PageView::create([
                        'url' => substr($path, 0, 255),
                        'session_id' => $sessionId,
                        'ip_address' => $request->ip(),
                        'user_agent' => substr((string)$request->userAgent(), 0, 500),
                        'created_at' => now(),
                    ]);
                }
            } catch (\Throwable $e) {
                // Tracking should never disrupt public user browsing
                report($e);
            }
        }

        return $response;
    }
}
