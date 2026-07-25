<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Teknologi', 'description' => 'Inovasi kecerdasan buatan, cloud computing, pengembangan perangkat lunak, dan tren IT terkini.', 'icon' => 'Cpu'],
            ['name' => 'Pendidikan', 'description' => 'Metode pembelajaran modern, kurikulum sains, dan transformasi pendidikan digital.', 'icon' => 'GraduationCap'],
            ['name' => 'Kampus', 'description' => 'Berita kehidupan mahasiswa, pengumuman akademik, dan kegiatan civitas akademika.', 'icon' => 'Building2'],
            ['name' => 'Berita', 'description' => 'Rangkuman kabar terbaru, liputan khusus, dan siaran pers resmi.', 'icon' => 'Newspaper'],
            ['name' => 'Tutorial', 'description' => 'Panduan praktis langkah demi langkah, pemecahan masalah, dan best practice coding.', 'icon' => 'BookOpen'],
            ['name' => 'Bisnis', 'description' => 'Strategi pertumbuhan bisnis, lanskap ekosistem startup, dan analisis pasar.', 'icon' => 'TrendingUp'],
            ['name' => 'Digital Marketing', 'description' => 'Teknik SEO, iklan digital, pemasaran media sosial, dan strategi pertumbuhan konten.', 'icon' => 'Target'],
            ['name' => 'Produktivitas', 'description' => 'Tips efisiensi kerja, manajemen waktu, dan tools pendukung produktivitas harian.', 'icon' => 'Zap'],
            ['name' => 'Lifestyle', 'description' => 'Gaya hidup seimbang, kesehatan mental pengembang, dan pengalaman harian.', 'icon' => 'Smile'],
            ['name' => 'Pengumuman', 'description' => 'Informasi penting dari manajemen, pembaruan rilis sistem, dan agenda acara.', 'icon' => 'Megaphone'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'icon' => $cat['icon'],
                ]
            );
        }
    }
}
