<?php

namespace App\Http\Controllers;

use App\Actions\Website\WebsiteData;
use App\Models\Accommodation;
use App\Models\Faq;
use App\Models\MediaAsset;
use App\Models\ResortProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class WebsiteController extends Controller
{
    public function page(Request $request, WebsiteData $data): Response|RedirectResponse
    {
        return $this->renderPage($request, $data);
    }

    public function preview(Request $request, WebsiteData $data): Response|RedirectResponse
    {
        abort_unless($request->user()?->is_staff, 403);
        $model = match ($request->route('type')) {
            'profile' => ResortProfile::class, 'accommodation' => Accommodation::class,
            'media' => MediaAsset::class, 'faq' => Faq::class, default => abort(404),
        };
        $record = $model::query()->whereKey($request->route('record'))->firstOrFail();

        return $this->renderPage($request, $data, $record);
    }

    private function renderPage(Request $request, WebsiteData $data, ?Model $preview = null): Response|RedirectResponse
    {
        $locale = (string) $request->route('locale');
        $page = (string) ($request->route('page') ?? 'home');
        if ($preview) {
            $page = match (true) {
                $preview instanceof Accommodation => 'stay', $preview instanceof MediaAsset => 'gallery', $preview instanceof Faq => 'plan', default => 'home'
            };
        }
        $props = $data->forLocale($locale, $preview);
        if ($page === 'accommodation') {
            abort_if(count($props['accommodations']) === 0, 404);
        }
        if ($page === 'gallery') {
            abort_if(count($props['media']) === 0, 404);
        }
        if ($page === 'privacy') {
            abort_if(blank($props['profile']['privacy']), 404);
        }
        $stay = null;
        if ($page === 'stay') {
            $slug = $preview instanceof Accommodation ? $preview->slug : $request->route('slug');
            $stay = null;
            foreach ($props['accommodations'] as $candidate) {
                if ($candidate['slug'] === $slug) {
                    $stay = $candidate;
                    break;
                }
            }
            abort_if(! $stay, 404);
        }
        $receipt = $page === 'received' ? $request->session()->get('website_receipt') : null;
        if ($page === 'received' && ! $receipt) {
            return to_route('website.home', ['locale' => $locale]);
        }
        $alternateLocale = $locale === 'ar' ? 'en' : 'ar';
        $routeName = $preview ? 'website.preview' : (string) $request->route()?->getName();
        $routeParameters = ['locale' => $alternateLocale];
        if ($page === 'booking' && $request->filled('accommodation')) {
            $routeParameters['accommodation'] = $request->query('accommodation');
        }
        if ($page === 'stay' && ! $preview) {
            $routeParameters['slug'] = $stay['slug'];
        }
        if ($preview) {
            $routeParameters += ['type' => $request->route('type'), 'record' => $preview->getKey()];
        }
        $alternateUrl = $preview ? URL::temporarySignedRoute($routeName, now()->addMinutes(30), $routeParameters) : route($routeName, $routeParameters);
        $token = null;
        if (in_array($page, ['booking', 'contact'], true)) {
            $token = Crypt::encryptString(json_encode(['id' => (string) Str::uuid(), 'session' => hash('sha256', $request->session()->getId()), 'kind' => $page === 'booking' ? 'booking' : 'enquiry', 'expires' => now()->addHours(2)->timestamp], JSON_THROW_ON_ERROR));
        }

        return Inertia::render('public/Website', [...$props,
            'page' => $page, 'stay' => $stay, 'receipt' => $receipt, 'alternateUrl' => $alternateUrl,
            'canonicalUrl' => $request->url(), 'submissionToken' => $token,
            'selectedAccommodation' => $request->query('accommodation'), 'today' => now(config('website.timezone'))->toDateString(),
        ]);
    }

    public function sitemap(WebsiteData $data): HttpResponse
    {
        $paths = [];
        if ($data->isIndexable()) {
            foreach (['en', 'ar'] as $locale) {
                $content = $data->forLocale($locale);
                foreach (['home', 'plan', 'contact', 'booking'] as $page) {
                    $paths[] = route('website.'.$page, ['locale' => $locale]);
                }
                if ($content['media']) {
                    $paths[] = route('website.gallery', ['locale' => $locale]);
                }
                if ($content['profile']['privacy']) {
                    $paths[] = route('website.privacy', ['locale' => $locale]);
                }
                if ($content['accommodations']) {
                    $paths[] = route('website.accommodations', ['locale' => $locale]);
                }
                foreach ($content['accommodations'] as $stay) {
                    $paths[] = route('website.accommodation', ['locale' => $locale, 'slug' => $stay['slug']]);
                }
            }
        }
        $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($paths as $path) {
            $xml .= '<url><loc>'.htmlspecialchars($path, ENT_XML1, 'UTF-8').'</loc></url>';
        }

        return response($xml.'</urlset>')->header('Content-Type', 'application/xml');
    }
}
