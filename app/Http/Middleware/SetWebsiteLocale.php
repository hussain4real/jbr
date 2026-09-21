<?php

namespace App\Http\Middleware;

use App\Actions\Website\WebsiteData;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetWebsiteLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');
        abort_unless(in_array($locale, ['en', 'ar'], true), 404);
        app()->setLocale($locale);
        $response = $next($request);
        $response->headers->set('Content-Language', $locale);
        if (! app(WebsiteData::class)->isIndexable() || $request->routeIs('website.received', 'website.preview')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }
        if ($request->routeIs('website.received', 'website.preview', 'website.booking', 'website.contact')) {
            $response->headers->set('Cache-Control', 'private, no-store');
        }

        return $response;
    }
}
