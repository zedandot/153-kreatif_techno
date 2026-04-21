<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use App\Models\ServiceFeature;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── Admin User ──────────────────────────────────────────
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@153kreatif.com')],
            [
                'name'               => 'Admin 153 Kreatif',
                'password'           => Hash::make(env('ADMIN_PASSWORD', 'admin153!')),
                'email_verified_at'  => now(),
            ]
        );

        // ── Main Services (skip jika sudah ada) ─────────────────
        $services = [
            [
                'name'        => 'Event Management',
                'description' => 'Kami menangani segala jenis acara dari awal konsep hingga pelaksanaan akhir. Tim profesional kami memastikan setiap detail dieksekusi dengan sempurna untuk acara perusahaan, konser, maupun pameran berskala besar.',
                'is_main'     => true,
                'order'       => 1,
                'features'    => [
                    'Perencanaan & Strategi Acara',
                    'Manajemen Vendor & Logistik',
                    'Pengawasan Eksekusi Lapangan (On-site)',
                ],
            ],
            [
                'name'        => 'Creative & Visual Production',
                'description' => 'Dukungan visual yang memukau adalah kunci dari acara yang berkesan. Kami menyediakan layanan produksi kreatif mulai dari desain panggung, pencahayaan, hingga pembuatan konten multimedia.',
                'is_main'     => true,
                'order'       => 2,
                'features'    => [
                    'Desain Panggung & Tata Ruang 3D',
                    'Pembuatan Video & Aset Multimedia',
                    'Sistem Pencahayaan (Lighting) & Visual LED',
                ],
            ],
            [
                'name'        => 'Exhibition Organizer',
                'description' => 'Membangun kehadiran brand Anda melalui pameran yang dirancang khusus. Kami merancang dekorasi booth yang interaktif untuk menarik pengunjung dan meningkatkan keterlibatan audiens.',
                'is_main'     => true,
                'order'       => 3,
                'features'    => [
                    'Desain & Konstruksi Custom Booth',
                    'Aktivasi Brand (Brand Activation)',
                    'Penyediaan SPG/SPB & Crew Lapangan',
                ],
            ],
        ];

        foreach ($services as $svcData) {
            $features = $svcData['features'];
            unset($svcData['features']);

            $svc = Service::firstOrCreate(['name' => $svcData['name']], $svcData);

            foreach ($features as $featureText) {
                ServiceFeature::firstOrCreate([
                    'service_id' => $svc->id,
                    'text'       => $featureText,
                ]);
            }
        }

        // ── Supporting Services ──────────────────────────────────
        $supporting = [
            'Talent Management', 'Sound System Rental', 'Permit & Licensing',
            'Photography Delivery', 'Live Streaming Setup', 'Genset & Daya Listrik',
        ];

        foreach ($supporting as $i => $name) {
            Service::firstOrCreate(
                ['name' => $name],
                ['description' => '', 'is_main' => false, 'order' => $i + 1]
            );
        }
    }
}
