<?php

namespace App\Filament\Widgets;

use App\Actions\Website\WebsiteData;
use App\Models\GuestRequest;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RequestOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $responseTimes = GuestRequest::query()->whereNotNull('first_contacted_at')->where('created_at', '>=', now()->subDays(30))->get()->map(fn (GuestRequest $request): float => (float) $request->created_at->diffInMinutes($request->first_contacted_at));

        return [
            Stat::make('Online requests', app(WebsiteData::class)->acceptsRequests() ? 'Enabled' : 'Not open')->description('Run website:readiness for outstanding launch checks'),
            Stat::make('Requests · last 30 days', GuestRequest::query()->where('created_at', '>=', now()->subDays(30))->count()),
            Stat::make('Awaiting first response', GuestRequest::query()->where('status', 'new')->count()),
            Stat::make('Average response · 30 days', $responseTimes->isEmpty() ? 'No data' : round($responseTimes->avg()).' min'),
            Stat::make('Recorded bookings · 30 days', GuestRequest::query()->where('status', 'closed')->where('outcome', 'booked')->where('closed_at', '>=', now()->subDays(30))->count())->description('Staff-confirmed outcomes only'),
        ];
    }
}
