<?php

namespace App\Http\Controllers;

use App\Actions\Website\WebsiteData;
use App\Models\MediaAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class WebsiteMediaController extends Controller
{
    public function __invoke(Request $request, MediaAsset $asset, string $size, string $version): BinaryFileResponse
    {
        $preview = $request->boolean('preview');
        if ($preview) {
            abort_unless($request->hasValidSignature() && ($request->user('resort')?->is_staff || WebsiteData::localPreview()), 404);
        }
        $variants = $preview ? $asset->variants : ($asset->published['variants'] ?? null);
        $variant = is_array($variants) ? ($variants[$size] ?? null) : null;
        abort_unless(is_array($variant) && hash_equals(substr(hash('sha256', $variant['path']), 0, 16), $version), 404);
        abort_unless(Storage::disk('local')->exists($variant['path']), 404);

        $response = response()->file(Storage::disk('local')->path($variant['path']), [
            'Content-Type' => 'image/webp', 'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => $preview ? 'private, no-store' : 'public, max-age=3600',
        ]);
        if ($preview) {
            $response->setPrivate();
        }

        return $response;
    }
}
