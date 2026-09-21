<?php

namespace App\Console\Commands;

use App\Actions\Website\ProcessImage;
use App\Models\Accommodation;
use App\Models\MediaAsset;
use App\Models\ResortProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportResortPhotographs extends Command
{
    protected $signature = 'website:import-photographs';

    protected $description = 'Import the supplied resort photographs as private, unapproved drafts without overwriting edits';

    public function handle(ProcessImage $processor): int
    {
        $photos = [
            ['Oman Villa', 'فيلا عُمان', 'Indoor pool and seating shown in the supplied Oman Villa photograph.', 'مسبح داخلي ومنطقة جلوس في الصورة المقدمة لفيلا عُمان.'],
            ['Majan Villa', 'فيلا مجان', 'Living room with sofas and a pool visible through the windows.', 'غرفة جلوس بأرائك ومسبح ظاهر عبر النوافذ.'],
            ['Salalah Villa', 'فيلا صلالة', 'Pool interior with a swing seat beside large windows.', 'مسبح داخلي مع مقعد أرجوحة بجانب نوافذ كبيرة.'],
            ['Mazon Villa', 'فيلا مازون', 'Bedroom with several beds and a pool visible through the glazing.', 'غرفة نوم بعدة أسرّة ومسبح ظاهر عبر الواجهات الزجاجية.'],
            ['Luxury 1 BR Villa', 'فيلا فاخرة بغرفة نوم واحدة', 'Bedroom with a large bed and windows overlooking greenery.', 'غرفة نوم بسرير كبير ونوافذ تطل على مساحات خضراء.'],
            ['Standard Room', 'غرفة قياسية', 'Room with two beds and a window.', 'غرفة بسريرين ونافذة.'],
        ];
        $hero = null;
        foreach ($photos as $index => [$title, $arabic, $alt, $altAr]) {
            $slug = Str::slug($title);
            $asset = $this->importPhotograph($processor, $title.'.png', $slug.'.png', $title, $arabic, $alt, $altAr, $index);
            if (! $asset) {
                return self::FAILURE;
            }
            $hero ??= $asset->id;
            Accommodation::query()->firstOrCreate(['slug' => $slug], ['draft' => [
                'title' => ['en' => $title, 'ar' => $arabic], 'description' => ['en' => '', 'ar' => ''],
                'inclusions' => ['en' => '', 'ar' => ''], 'capacity' => null, 'media_ids' => [$asset->id],
                'sort_order' => $index, 'facts_approved' => false, 'arabic_reviewed' => false,
            ]]);
        }
        $galleryPhotos = [
            ['23.50.54', 'twin-bedroom-wide', 'Twin bedroom', 'غرفة بسريرين', 'Wide view of a bedroom with two beds, a television and wooden furnishings.', 'منظر واسع لغرفة نوم بسريرين وتلفاز وأثاث خشبي.'],
            ['23.52.42', 'twin-bedroom-beds', 'Bedroom detail', 'تفاصيل غرفة النوم', 'Two beds with white bedding, brown cushions and a bedside table between them.', 'سريران بمفروشات بيضاء ووسائد بنية وطاولة جانبية بينهما.'],
            ['23.54.59', 'bathroom-washbasin', 'Bathroom', 'الحمّام', 'Bathroom washbasin, mirror and towels with a shower visible in the reflection.', 'مغسلة ومرآة ومناشف في الحمّام مع ظهور الدش في انعكاس المرآة.'],
            ['23.56.40', 'bedroom-courtyard-view', 'Room and courtyard view', 'إطلالة من الغرفة على الساحة', 'Bedroom with an open doorway overlooking a paved courtyard and lawn.', 'غرفة نوم بباب مفتوح يطل على ساحة مرصوفة ومساحة خضراء.'],
        ];
        foreach ($galleryPhotos as $index => [$time, $slug, $title, $arabic, $alt, $altAr]) {
            if (! $this->importPhotograph($processor, 'WhatsApp Image 2026-09-18 at '.$time.'.jpeg', $slug.'.jpeg', $title, $arabic, $alt, $altAr, count($photos) + $index)) {
                return self::FAILURE;
            }
        }
        ResortProfile::query()->firstOrCreate(['key' => 'main'], ['draft' => [
            'introduction' => ['en' => 'Welcome to Jawharat Bidiyah Resort. Explore our accommodation and get in touch to plan your stay.', 'ar' => 'مرحباً بكم في منتجع جوهرة بدية. استكشفوا خيارات الإقامة وتواصلوا معنا للتخطيط لزيارتكم.'],
            'hero_media_id' => $hero, 'phone' => '+968 90657840', 'email' => 'reservations@jauharat.com',
            'contacts_verified' => false, 'whatsapp_verified' => false, 'privacy_approved' => false, 'operations_approved' => false, 'requests_enabled' => false, 'arabic_reviewed' => false,
        ]]);
        $this->info('Ten photographs available: six accommodation photos and four gallery additions. Existing edits and publications preserved; new photos remain drafts.');

        return self::SUCCESS;
    }

    private function importPhotograph(ProcessImage $processor, string $sourceName, string $storageName, string $title, string $arabic, string $alt, string $altAr, int $order): ?MediaAsset
    {
        $path = 'website/originals/'.$storageName;
        $asset = MediaAsset::query()->where('original_path', $path)->first();
        if (! $asset) {
            $source = resource_path('images/resort/'.$sourceName);
            if (! is_file($source)) {
                $this->error('Missing supplied photograph: '.$sourceName);

                return null;
            }
            $bytes = file_get_contents($source);
            if ($bytes === false || ! Storage::disk('local')->put($path, $bytes)) {
                $this->error('Could not store photograph.');

                return null;
            }
            $asset = MediaAsset::withoutEvents(fn (): MediaAsset => MediaAsset::query()->create(['original_path' => $path, 'draft' => [
                'caption' => ['en' => $title, 'ar' => $arabic], 'alt' => ['en' => $alt, 'ar' => $altAr],
                'sort_order' => $order, 'focal_x' => 50, 'focal_y' => 50,
                'rights_confirmed' => false, 'property_verified' => false, 'arabic_reviewed' => false,
            ]]));
        }
        if ($asset->processing_status !== 'ready') {
            $processor->handle($asset);
        }

        return $asset;
    }
}
