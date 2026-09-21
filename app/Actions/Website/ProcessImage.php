<?php

namespace App\Actions\Website;

use App\Models\MediaAsset;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ProcessImage
{
    public function handle(MediaAsset $asset): void
    {
        $disk = Storage::disk('local');
        $path = $asset->original_path;
        if (! str_starts_with($path, 'website/originals/') || ! $disk->exists($path) || $disk->size($path) > 12 * 1024 * 1024) {
            throw new RuntimeException('The image source is not valid.');
        }
        $bytes = $disk->get($path);
        $info = $bytes !== null ? @getimagesizefromstring($bytes) : false;
        if (! $info || ! in_array($info['mime'], ['image/jpeg', 'image/png', 'image/webp'], true) || $info[0] * $info[1] > 40000000) {
            throw new RuntimeException('Choose a JPEG, PNG or WebP image under 40 megapixels.');
        }
        $source = @imagecreatefromstring($bytes);
        if (! $source) {
            throw new RuntimeException('The image could not be decoded.');
        }
        $variants = [];
        $revision = (string) Str::uuid();
        foreach ([480, 960, 1600] as $size) {
            $width = max(1, min($size, $info[0]));
            $height = max(1, (int) round($info[1] * $width / $info[0]));
            $target = imagecreatetruecolor($width, $height);
            if (! $target) {
                throw new RuntimeException('Image processing failed.');
            }
            $white = imagecolorallocate($target, 255, 255, 255);
            if ($white === false) {
                throw new RuntimeException('Image processing failed.');
            }
            imagefill($target, 0, 0, $white);
            imagecopyresampled($target, $source, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
            ob_start();
            imagewebp($target, null, 82);
            $encoded = ob_get_clean();
            $destination = 'website/variants/'.$revision.'/'.$size.'.webp';
            if (! is_string($encoded) || ! $disk->put($destination, $encoded)) {
                throw new RuntimeException('The processed image could not be stored.');
            }
            $variants[(string) $size] = ['path' => $destination, 'width' => $width, 'height' => $height];
        }
        MediaAsset::query()->whereKey($asset->id)->where('original_path', $path)->update(['variants' => $variants, 'processing_status' => 'ready']);
        $asset->refresh();
    }
}
