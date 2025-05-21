<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MaintenanceService;

class MaintenanceServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'الصيانة والسباكة',
                'image_url' => '\assets\images\services\خدمات-صيانة.png'

            ],
            [
                'name' => 'كهرباء وتركيب',
                'image_url' => '\assets\images\services\فحص-الأعطال-الكهربائية.jpg'

               
            ],
            [
                'name' => 'التكييف والتبريد',
                'image_url' => '\assets\images\services\تبريد-تكييف.jpg'


            ],
            [
                'name' => 'مكافحة الحشرات والقوارض',
                'image_url' => '\assets\images\services\رش-مبيدات.png'
            ],
            [
                'name' => 'خدمات التنظيف',
                'image_url' => '\assets\images\services\تنظيف-المنزل.jpg'
            ],
            [
                'name' => 'أعمال الدهانات',
                'image_url' => '\assets\images\services\الدهان.jpg'
            ],
            [
                'name' => 'خدمات التبليط',
                'image_url' => '\assets\images\services\تبليط.png'
            ],
        ];

        foreach ($services as $service) {
            MaintenanceService::create($service);
        }
    }
} 