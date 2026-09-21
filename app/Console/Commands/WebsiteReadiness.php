<?php

namespace App\Console\Commands;

use App\Actions\Website\WebsiteData;
use App\Models\Accommodation;
use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Console\Command;

class WebsiteReadiness extends Command
{
    protected $signature = 'website:readiness';

    protected $description = 'Report website launch gates without changing content or contacting guests';

    public function handle(WebsiteData $data): int
    {
        $profile = $data->publishedProfile();
        $checks = [
            'Approved bilingual profile' => filled($profile),
            'Published accommodation catalogue' => Accommodation::query()->whereNotNull('published_at')->exists(),
            'Approved processed photographs' => MediaAsset::query()->whereNotNull('published_at')->exists(),
            'Verified contacts' => $profile['contacts_verified'] ?? false,
            'Exact address and arrival instructions' => filled(data_get($profile, 'address.en')) && filled(data_get($profile, 'arrival.en')),
            'Approved policies' => filled(data_get($profile, 'policies.en')),
            'Approved privacy and retention' => ($profile['privacy_approved'] ?? false) && ($profile['retention_days'] ?? 0) > 0,
            'Staff operating process' => $profile['operations_approved'] ?? false,
            'At least one staff account' => User::query()->where('is_staff', true)->exists(),
            'Requests and notification routing enabled' => $data->acceptsRequests(),
            'Indexing enabled' => (bool) config('website.indexable'),
        ];
        $this->table(['Check', 'Status'], collect($checks)->map(fn (bool $ready, string $label): array => [$label, $ready ? 'Ready' : 'Outstanding'])->values()->all());
        $this->warn('Still verify real email delivery, Arabic review, staff UAT, domain, HTTPS, worker supervision and backup restoration before launch.');

        return in_array(false, $checks, true) ? self::FAILURE : self::SUCCESS;
    }
}
