<?php

use App\Filament\AppAuthentication;
use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use Filament\Facades\Filament;
use PragmaRX\Google2FAQRCode\Google2FA;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

test('the configured admin authenticator returns image bytes rather than a nested data URI', function () {
    $this->actingAs(User::factory()->make(['is_staff' => true]), 'resort');
    $provider = Filament::getPanel('admin')->getMultiFactorAuthenticationProviders()['app'];

    $uri = $provider->generateQrCodeDataUri('JBSWY3DPEHPK3PXP');
    [$prefix, $encodedImage] = explode(',', $uri, 2);

    expect($prefix)->toBeIn(['data:image/png;base64', 'data:image/svg+xml;base64']);
    expect(base64_decode($encodedImage, strict: true))->toMatch('/\A(?:<\?xml\b|\x89PNG\r\n\x1a\n)/');
});

test('SVG authenticator images decode into a valid SVG document', function () {
    $this->actingAs(User::factory()->make(['is_staff' => true]), 'resort');
    $provider = new AppAuthentication(new Google2FA(imageBackEnd: new SvgImageBackEnd));

    $uri = $provider->generateQrCodeDataUri('JBSWY3DPEHPK3PXP');

    expect($uri)->toStartWith('data:image/svg+xml;base64,');
    $document = new DOMDocument;
    expect($document->loadXML(base64_decode(explode(',', $uri, 2)[1], strict: true)))->toBeTrue();
    expect($document->documentElement->localName)->toBe('svg');
    expect($document->documentElement->namespaceURI)->toBe('http://www.w3.org/2000/svg');
});
