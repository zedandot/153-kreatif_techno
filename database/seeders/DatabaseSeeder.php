<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ... (User dummy kalau dibutuhkan)
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Bikin 3 Main Services (is_main = true)
        $main1 = \App\Models\Service::create([
            'name' => 'Event Management',
            'description' => 'Kami menangani segala jenis acara dari awal konsep hingga pelaksanaan akhir. Tim profesional kami memastikan setiap detail dieksekusi dengan sempurna untuk acara perusahaan, konser, maupun pameran berskala besar.',
            'is_main' => true,
            'order' => 1,
        ]);
        \App\Models\ServiceFeature::create(['service_id' => $main1->id, 'text' => 'Perencanaan & Strategi Acara']);
        \App\Models\ServiceFeature::create(['service_id' => $main1->id, 'text' => 'Manajemen Vendor & Logistik']);
        \App\Models\ServiceFeature::create(['service_id' => $main1->id, 'text' => 'Pengawasan Eksekusi Lapangan (On-site)']);

        $main2 = \App\Models\Service::create([
            'name' => 'Creative & Visual Production',
            'description' => 'Dukungan visual yang memukau adalah kunci dari acara yang berkesan. Kami menyediakan layanan produksi kreatif mulai dari desain panggung, pencahayaan, hingga pembuatan konten multimedia.',
            'is_main' => true,
            'order' => 2,
        ]);
        \App\Models\ServiceFeature::create(['service_id' => $main2->id, 'text' => 'Desain Panggung & Tata Ruang 3D']);
        \App\Models\ServiceFeature::create(['service_id' => $main2->id, 'text' => 'Pembuatan Video & Aset Multimedia']);
        \App\Models\ServiceFeature::create(['service_id' => $main2->id, 'text' => 'Sistem Pencahayaan (Lighting) & Visual LED']);

        $main3 = \App\Models\Service::create([
            'name' => 'Exhibition Organizer',
            'description' => 'Membangun kehadiran brand Anda melalui pameran yang dirancang khusus. Kami merancang dekorasi booth yang interaktif untuk menarik pengunjung dan meningkatkan keterlibatan audiens.',
            'is_main' => true,
            'order' => 3,
        ]);
        \App\Models\ServiceFeature::create(['service_id' => $main3->id, 'text' => 'Desain & Konstruksi Custom Booth']);
        \App\Models\ServiceFeature::create(['service_id' => $main3->id, 'text' => 'Aktivasi Brand (Brand Activation)']);
        \App\Models\ServiceFeature::create(['service_id' => $main3->id, 'text' => 'Penyediaan SPG/SPB & Crew Lapangan']);

        // Bikin bebrapa Supporting Services (is_main = false)
        \App\Models\Service::create(['name' => 'Talent Management', 'description' => '', 'is_main' => false, 'order' => 1]);
        \App\Models\Service::create(['name' => 'Sound System Rental', 'description' => '', 'is_main' => false, 'order' => 2]);
        \App\Models\Service::create(['name' => 'Permit & Licensing', 'description' => '', 'is_main' => false, 'order' => 3]);
        \App\Models\Service::create(['name' => 'Photography Delivery', 'description' => '', 'is_main' => false, 'order' => 4]);
        \App\Models\Service::create(['name' => 'Live Streaming Setup', 'description' => '', 'is_main' => false, 'order' => 5]);
        \App\Models\Service::create(['name' => 'Genset & Daya Listrik', 'description' => '', 'is_main' => false, 'order' => 6]);
    }
}
