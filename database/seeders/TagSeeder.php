<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'AI', 'color' => '#6366f1', 'description' => 'Artificial Intelligence & Large Language Models'],
            ['name' => 'Machine Learning', 'color' => '#8b5cf6', 'description' => 'Algoritma dan model prediktif machine learning'],
            ['name' => 'Cloud Computing', 'color' => '#0ea5e9', 'description' => 'Infrastruktur cloud AWS, GCP, dan Azure'],
            ['name' => 'Software', 'color' => '#3b82f6', 'description' => 'Pengembangan perangkat lunak dan arsitektur'],
            ['name' => 'Programming', 'color' => '#10b981', 'description' => 'Bahasa pemrograman PHP, JavaScript, Python'],
            ['name' => 'Kampus', 'color' => '#f59e0b', 'description' => 'Kehidupan dan kegiatan mahasiswa di kampus'],
            ['name' => 'Mahasiswa', 'color' => '#ec4899', 'description' => 'Tips dan info seputar dunia kemahasiswaan'],
            ['name' => 'Pembelajaran', 'color' => '#14b8a6', 'description' => 'Metode edutech dan e-learning terintegrasi'],
            ['name' => 'Akademik', 'color' => '#64748b', 'description' => 'Riset ilmiah dan kegiatan perkuliahan'],
            ['name' => 'UMKM', 'color' => '#ef4444', 'description' => 'Pengembangan dan digitalisasi usaha mikro'],
            ['name' => 'Marketing', 'color' => '#d97706', 'description' => 'Pemasaran produk dan strategi branding'],
            ['name' => 'Startup', 'color' => '#84cc16', 'description' => 'Rintisan teknologi dan pendanaan modal ventura'],
            ['name' => 'Digital Business', 'color' => '#06b6d4', 'description' => 'Transformasi bisnis dan ekonomi digital'],
            ['name' => 'Productivity', 'color' => '#a855f7', 'description' => 'Manajemen waktu dan efisiensi alur kerja'],
            ['name' => 'Web Development', 'color' => '#f43f5e', 'description' => 'Pengembangan website modern dengan Laravel & Svelte'],
        ];

        foreach ($tags as $tag) {
            Tag::updateOrCreate(
                ['slug' => Str::slug($tag['name'])],
                [
                    'name' => $tag['name'],
                    'color' => $tag['color'],
                    'description' => $tag['description'],
                ]
            );
        }
    }
}
