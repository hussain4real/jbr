<?php

namespace App\Filament;

use Filament\Auth\MultiFactor\App\AppAuthentication as FilamentAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Facades\Filament;
use SensitiveParameter;

class AppAuthentication extends FilamentAppAuthentication
{
    public function generateQrCodeDataUri(#[SensitiveParameter] string $secret): string
    {
        /** @var HasAppAuthentication $user */
        $user = Filament::auth()->user();

        // PragmaRX 4 already returns a data URI, including for SVG without Imagick.
        return $this->google2FA->getQRCodeInline(
            $this->getBrandName(),
            $this->getHolderName($user),
            $secret,
        );
    }
}
