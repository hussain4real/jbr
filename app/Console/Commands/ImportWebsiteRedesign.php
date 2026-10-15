<?php

namespace App\Console\Commands;

use App\Actions\Website\ProcessImage;
use App\Actions\Website\WebsiteRedesign;
use App\Models\Accommodation;
use App\Models\MediaAsset;
use App\Models\ResortProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportWebsiteRedesign extends Command
{
    protected $signature = 'website:import-redesign {--reprocess : Rebuild draft image variants while preserving published images and copy} {--fill-missing : Fill empty resort information from supplied copy without replacing existing text}';

    protected $description = 'Import the supplied v2 copy and photographs as reviewable drafts, preserving edits and published content';

    public function handle(ProcessImage $processor): int
    {
        $source = WebsiteRedesign::source();
        $mediaIds = [];
        foreach ($source['media'] as $key => $image) {
            $sourcePath = resource_path('images/resort/v2/'.$image['filename']);
            if (! is_file($sourcePath) || hash_file('sha256', $sourcePath) !== $image['sha256']) {
                $this->error('Missing or changed supplied asset: '.$image['filename']);

                return self::FAILURE;
            }
        }
        foreach ($source['media'] as $mediaKey => $image) {
            $path = 'website/originals/v2-'.$source['revision'].'/'.$image['sha256'].'.'.pathinfo($image['filename'], PATHINFO_EXTENSION);
            $asset = MediaAsset::query()->where('original_path', $path)->first();
            if (! $asset) {
                $bytes = file_get_contents(resource_path('images/resort/v2/'.$image['filename']));
                if (! is_string($bytes) || ! Storage::disk('local')->put($path, $bytes)) {
                    $this->error('Could not store supplied asset: '.$image['filename']);

                    return self::FAILURE;
                }
                $asset = MediaAsset::withoutEvents(fn (): MediaAsset => MediaAsset::query()->create([
                    'original_path' => $path,
                    'draft' => [
                        'caption' => $image['caption'], 'alt' => $image['alt'],
                        'category' => $image['category'], 'gallery_visible' => $image['gallery_visible'],
                        'source_key' => $mediaKey, 'sort_order' => count($mediaIds), 'focal_x' => 50, 'focal_y' => 50,
                        'rights_confirmed' => false, 'property_verified' => false, 'arabic_reviewed' => false,
                    ],
                ]));
            }
            if ($this->option('reprocess') || $asset->processing_status !== 'ready') {
                $processor->handle($asset);
            }
            $mediaIds[$mediaKey] = $asset->id;
        }
        foreach (['oman-villa', 'majan-villa', 'salalah-villa', 'mazon-villa', 'luxury-1-br-villa', 'standard-room'] as $order => $slug) {
            $stay = $source['accommodations'][$slug];
            $occupancy = $source['approved_occupancy'][$slug];
            $record = Accommodation::query()->firstOrCreate(['slug' => $slug], ['draft' => [
                'title' => $stay['title'], 'description' => $stay['overview'], 'inclusions' => $stay['inclusions'],
                'capacity' => $occupancy['capacity'], 'capacity_basis' => $occupancy['basis'], 'media_ids' => [$mediaIds[$stay['image_key']]], 'sort_order' => $order,
                'facts_approved' => false, 'arabic_reviewed' => false,
            ]]);
            if ($this->option('fill-missing') && blank($record->draft['capacity'] ?? null)) {
                $record->update(['draft' => [...$record->draft, 'capacity' => $occupancy['capacity'], 'capacity_basis' => $occupancy['basis'], 'facts_approved' => false]]);
            }
            if (! isset($record->draft['redesign'])) {
                $record->update(['draft' => [...$record->draft,
                    'redesign' => [...array_intersect_key($stay, array_flip(['page', 'title', 'description', 'overview', 'inclusions', 'badge', 'facts', 'categories'])), 'media_ids' => [$mediaIds[$stay['image_key']]]],
                    'facts_approved' => false, 'arabic_reviewed' => false,
                ]]);
            }
        }
        $profile = ResortProfile::query()->firstOrCreate(['key' => 'main'], ['draft' => [
            'introduction' => ['en' => '', 'ar' => ''], 'arabic_reviewed' => false,
        ]]);
        $isNewRevision = ! isset($profile->draft['redesign']);
        if ($isNewRevision || $this->option('fill-missing')) {
            $draft = $profile->draft;
            $changed = false;
            foreach ($source['profile'] as $field => $content) {
                foreach (is_array($content) ? $content : [$content] as $locale => $value) {
                    $path = is_array($content) ? $field.'.'.$locale : $field;
                    if (blank(data_get($draft, $path))) {
                        data_set($draft, $path, $value);
                        $changed = true;
                        if (in_array($field, ['phone', 'email'], true)) {
                            $draft['contacts_verified'] = false;
                        }
                        if ($field === 'whatsapp_number') {
                            $draft['whatsapp_verified'] = false;
                        }
                    }
                }
            }
            if ($changed) {
                if (! $isNewRevision) {
                    $draft['redesign']['facts_approved'] = false;
                    $draft['redesign']['arabic_reviewed'] = false;
                }
                $profile->update(['draft' => $draft]);
            }
        }
        if ($isNewRevision) {
            $profile->update(['draft' => [...$profile->draft, 'redesign' => [
                'revision' => $source['revision'], 'facts_approved' => false, 'arabic_reviewed' => false,
                'pages' => array_diff_key($source['pages'], array_flip(array_column($source['accommodations'], 'page'))), 'media' => $mediaIds,
                'gallery_media_ids' => array_values(array_unique(array_map(fn (string $key): int => $mediaIds[$key], $source['gallery']))),
            ]]]);
        }
        $this->info('V2 content and '.count(array_unique($mediaIds)).' photographs imported as drafts. Existing edits, capacities, published content and request settings are preserved. Review and publish through Website content, Accommodations and Media Assets.');

        return self::SUCCESS;
    }
}
