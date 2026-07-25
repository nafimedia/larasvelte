<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            // Content Management
            [
                'key' => 'posts',
                'name' => 'Artikel & Blog',
                'group' => 'content',
                'description' => 'Kelola publikasi artikel, blog, penulis, dan revisi berkala.',
                'icon' => 'Newspaper',
                'is_active' => true,
                'is_system' => false,
                'order' => 1,
            ],
            [
                'key' => 'pages',
                'name' => 'Halaman Web',
                'group' => 'content',
                'description' => 'Kelola halaman statis dan struktur konten website.',
                'icon' => 'FileText',
                'is_active' => true,
                'is_system' => false,
                'order' => 2,
            ],
            [
                'key' => 'categories',
                'name' => 'Kategori & Tag',
                'group' => 'content',
                'description' => 'Taksi taksonomi pengelompokan konten artikel.',
                'icon' => 'Folder',
                'is_active' => true,
                'is_system' => false,
                'order' => 3,
            ],
            [
                'key' => 'comments',
                'name' => 'Moderasi Komentar',
                'group' => 'content',
                'description' => 'Moderasi masukan dan tanggapan pengunjung artikel.',
                'icon' => 'MessageSquare',
                'is_active' => true,
                'is_system' => false,
                'order' => 4,
            ],
            [
                'key' => 'calendar',
                'name' => 'Kalender Publikasi',
                'group' => 'content',
                'description' => 'Penjadwalan rilis dan kalender konten publikasi.',
                'icon' => 'Calendar',
                'is_active' => true,
                'is_system' => false,
                'order' => 5,
            ],

            // Website Builder
            [
                'key' => 'landing_builder',
                'name' => 'Visual Landing Builder Studio',
                'group' => 'builder',
                'description' => 'Editor visual tata letak halaman depan tanpa koding.',
                'icon' => 'Sparkles',
                'is_active' => true,
                'is_system' => false,
                'order' => 6,
            ],
            [
                'key' => 'menus',
                'name' => 'Menu Navigasi',
                'group' => 'builder',
                'description' => 'Pengaturan struktur menu navigasi header dan footer.',
                'icon' => 'Menu',
                'is_active' => true,
                'is_system' => false,
                'order' => 7,
            ],
            [
                'key' => 'forms',
                'name' => 'Dynamic Form Builder',
                'group' => 'builder',
                'description' => 'Pembuat formulir kontak/survei dinamis & inbox pesan.',
                'icon' => 'FileSpreadsheet',
                'is_active' => true,
                'is_system' => false,
                'order' => 8,
            ],

            // Media
            [
                'key' => 'media',
                'name' => 'Media Library (Asset Manager)',
                'group' => 'media',
                'description' => 'Penyimpanan terpusat gambar, dokumen, dan aset media.',
                'icon' => 'Image',
                'is_active' => true,
                'is_system' => false,
                'order' => 9,
            ],

            // SEO & Optimization
            [
                'key' => 'redirects',
                'name' => 'Redirect Manager (301/302)',
                'group' => 'seo',
                'description' => 'Pengalihan alamat URL untuk menjaga reputasi SEO.',
                'icon' => 'ArrowRightLeft',
                'is_active' => true,
                'is_system' => false,
                'order' => 10,
            ],
            [
                'key' => 'analytics',
                'name' => 'CMS Analitik',
                'group' => 'seo',
                'description' => 'Laporan grafik pengunjung dan statistik lalu lintas web.',
                'icon' => 'BarChart3',
                'is_active' => true,
                'is_system' => false,
                'order' => 11,
            ],

            // System Management (Core)
            [
                'key' => 'activity_logs',
                'name' => 'Log Aktivitas Sistem',
                'group' => 'system',
                'description' => 'Catatan riwayat tindakan administrator & pengguna.',
                'icon' => 'Activity',
                'is_active' => true,
                'is_system' => false,
                'order' => 12,
            ],
            [
                'key' => 'users',
                'name' => 'Manajemen Pengguna',
                'group' => 'system',
                'description' => 'Pengelolaan akun pengguna dan hak akses.',
                'icon' => 'Users',
                'is_active' => true,
                'is_system' => true, // Core system
                'order' => 13,
            ],
            [
                'key' => 'roles',
                'name' => 'Role & Izin (RBAC)',
                'group' => 'system',
                'description' => 'Matriks hak akses dan peran pengguna.',
                'icon' => 'ShieldCheck',
                'is_active' => true,
                'is_system' => true, // Core system
                'order' => 14,
            ],
            [
                'key' => 'settings',
                'name' => 'Pengaturan Situs & Branding',
                'group' => 'system',
                'description' => 'Pengaturan identitas global dan logo situs.',
                'icon' => 'Settings',
                'is_active' => true,
                'is_system' => true, // Core system
                'order' => 15,
            ],
            [
                'key' => 'modules',
                'name' => 'Manajemen Modul System',
                'group' => 'system',
                'description' => 'Pengaturan sakelar aktif/nonaktif fitur aplikasi.',
                'icon' => 'Layers',
                'is_active' => true,
                'is_system' => true, // Core system
                'order' => 16,
            ],
        ];

        foreach ($modules as $mod) {
            Module::updateOrCreate(['key' => $mod['key']], $mod);
        }

        Module::clearCache();
    }
}
