<?php

namespace App\Http\Controllers;

use App\Actions\Website\WebsiteData;
use App\Models\ResortProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class WebsitePromotionController extends Controller
{
    public function __invoke(Request $request, ResortProfile $profile, string $version): BinaryFileResponse
    {
        $preview = $request->boolean('preview');
        if ($preview) {
            abort_unless($request->hasValidSignature() && ($request->user('resort')?->is_staff || WebsiteData::localPreview()), 404);
        }
        $content = $preview ? $profile->draft : $profile->published;
        $path = $profile->promotionPdfPath($preview);
        abort_unless($profile->key === 'main' && ($content['promotion_enabled'] ?? false) && $path, 404);
        abort_unless(hash_equals(substr(hash('sha256', $path), 0, 16), $version), 404);

        return response()->download(Storage::disk('local')->path($path), 'jawharat-bidiyah-resort-promotion.pdf', [
            'Content-Type' => 'application/pdf', 'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store', 'X-Robots-Tag' => 'noindex',
        ])->setPrivate();
    }
}
