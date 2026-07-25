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
            // Public Website Branding
            [
                'key' => 'public_logo_light',
                'value' => '/images/branding/public_logo_light.svg',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Logo Public (Light Mode)',
                'description' => 'Logo utama website publik untuk latar terang.',
            ],
            [
                'key' => 'public_logo_dark',
                'value' => '/images/branding/public_logo_dark.svg',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Logo Public (Dark Mode)',
                'description' => 'Logo website publik untuk latar gelap.',
            ],
            [
                'key' => 'public_logo_mobile',
                'value' => '/images/branding/public_logo_light.svg',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Logo Public Mobile',
                'description' => 'Logo khusus tampilan mobile/layar kecil.',
            ],
            [
                'key' => 'public_logo_footer',
                'value' => '/images/branding/public_logo_dark.svg',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Logo Public Footer',
                'description' => 'Logo khusus untuk bagian footer website.',
            ],
            [
                'key' => 'public_favicon',
                'value' => '/images/branding/public_favicon.svg',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Favicon Website',
                'description' => 'Icon tab browser (PNG, ICO, SVG).',
            ],
            [
                'key' => 'public_apple_touch_icon',
                'value' => '/images/branding/public_apple_touch_icon.svg',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Apple Touch Icon',
                'description' => 'Icon bookmark layar utama iOS/Mobile (180x180).',
            ],
            // Admin Dashboard Branding
            [
                'key' => 'admin_logo_light',
                'value' => '/images/branding/admin_logo_light.svg',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Logo Admin (Light Mode)',
                'description' => 'Logo sidebar admin panel untuk latar terang.',
            ],
            [
                'key' => 'admin_logo_dark',
                'value' => '/images/branding/admin_logo_dark.svg',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Logo Admin (Dark Mode)',
                'description' => 'Logo sidebar admin panel untuk latar gelap.',
            ],
            [
                'key' => 'admin_logo_collapsed',
                'value' => '/images/branding/admin_logo_collapsed.svg',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Logo Admin Collapsed (Icon Only)',
                'description' => 'Icon ringkas ketika sidebar adminenci/dikecilkan.',
            ],
            [
                'key' => 'admin_favicon',
                'value' => '/images/branding/public_favicon.svg',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Favicon Admin Panel',
                'description' => 'Favicon khusus halaman admin.',
            ],
            [
                'key' => 'admin_login_logo',
                'value' => '/images/branding/public_logo_light.svg',
                'group' => 'branding',
                'type' => 'string',
                'label' => 'Logo Halaman Login Admin',
                'description' => 'Logo yang tampil di atas form login admin.',
            ],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        \Illuminate\Support\Facades\Cache::forget(\App\Http\Controllers\Admin\BrandingController::CACHE_KEY);
    }
}
