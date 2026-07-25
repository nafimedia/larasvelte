<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'LaraSvelte Starterkit',
                'group' => 'general',
                'type' => 'string',
                'label' => 'Nama Aplikasi',
                'description' => 'Nama utama aplikasi yang ditampilkan di header dan title tag.',
            ],
            [
                'key' => 'site_description',
                'value' => 'Starter kit modern Laravel 13 + Svelte 5 + Tailwind CSS v4 dengan fitur RBAC, Activity Log & API Ready.',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Deskripsi Aplikasi',
                'description' => 'Deskripsi meta aplikasi untuk SEO.',
            ],
            [
                'key' => 'contact_email',
                'value' => 'support@example.com',
                'group' => 'contact',
                'type' => 'string',
                'label' => 'Email Kontak',
                'description' => 'Alamat email resmi dukungan pengguna.',
            ],
            [
                'key' => 'enable_registration',
                'value' => 'true',
                'group' => 'system',
                'type' => 'boolean',
                'label' => 'Izinkan Pendaftaran Pengguna',
                'description' => 'Aktifkan jika pengguna baru diperbolehkan mendaftar mandiri.',
            ],
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'group' => 'system',
                'type' => 'boolean',
                'label' => 'Mode Pemeliharaan',
                'description' => 'Tampilkan halaman maintenance untuk pengguna non-admin.',
            ],
        ];

        foreach ($settings as $setting) {
            SiteSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
