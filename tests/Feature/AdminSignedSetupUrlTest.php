<?php

use App\Actions\ManageResortUsers;
use App\Providers\Filament\AdminPanelProvider;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\PanelRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

test('owner setup is signed for the configured admin host and rejects host substitution', function () {
    config(['website.admin_domain' => 'preview.example.invalid']);
    app(PanelRegistry::class)->register((new AdminPanelProvider(app()))->panel(Panel::make()));
    require base_path('vendor/filament/filament/routes/web.php');
    Route::getRoutes()->refreshNameLookups();
    URL::setRoutes(Route::getRoutes());
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    URL::forceScheme('https');
    $url = '';
    app(ManageResortUsers::class)->prepareOwnerSetup('owner@example.invalid', 'Owner', true, function (string $link) use (&$url): void {
        $url = $link;
    });
    expect(parse_url($url, PHP_URL_HOST))->toBe('preview.example.invalid');
    expect(URL::hasValidSignature(Request::create($url)))->toBeTrue();
    $this->get($url)->assertOk()->assertSee('Reset password');
    $changedHost = str_replace('preview.example.invalid', 'public.example.invalid', $url);
    expect(URL::hasValidSignature(Request::create($changedHost)))->toBeFalse();
});
