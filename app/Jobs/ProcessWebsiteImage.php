<?php

namespace App\Jobs;

use App\Actions\Website\ProcessImage;
use App\Models\MediaAsset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Throwable;

class ProcessWebsiteImage implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(public int $assetId) {}

    /** @return array<WithoutOverlapping> */
    public function middleware(): array
    {
        return [(new WithoutOverlapping('website-image:'.$this->assetId))->releaseAfter(30)->expireAfter(180)];
    }

    public function handle(ProcessImage $processor): void
    {
        $asset = MediaAsset::query()->find($this->assetId);
        if (! $asset) {
            return;
        }
        try {
            $processor->handle($asset);
        } catch (Throwable $exception) {
            $asset->forceFill(['processing_status' => 'failed'])->save();
            throw $exception;
        }
    }
}
