<?php

namespace Database\Seeders;

use App\Models\ResortProfile;
use Illuminate\Database\Seeder;

class ResortProfileSeeder extends Seeder
{
    public function run(): void
    {
        ResortProfile::query()->firstOrCreate(['key' => 'main'], ['draft' => ['introduction' => ['en' => '', 'ar' => ''], 'phone' => '+968 90657840', 'email' => 'reservations@jauharat.com', 'contacts_verified' => false, 'arabic_reviewed' => false, 'requests_enabled' => false, 'privacy_approved' => false, 'operations_approved' => false, 'whatsapp_verified' => false]]);
    }
}
