<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $authors = [
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@example.com',
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Senior Tech Writer & Cloud Solutions Architect.',
            ],
            [
                'name' => 'Siti Rahma',
                'email' => 'siti.rahma@example.com',
                'avatar_url' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Pemerhati Pendidikan Digital & Konsultan Edutech.',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Digital Marketing Strategist & SEO Enthusiast.',
            ],
            [
                'name' => 'Rina Maharani',
                'email' => 'rina.maharani@example.com',
                'avatar_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Praktisi Bisnis UMKM & Startup Mentor.',
            ],
            [
                'name' => 'Dimas Pratama',
                'email' => 'dimas.pratama@example.com',
                'avatar_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Lead Fullstack Developer & Open Source Contributor.',
            ],
            [
                'name' => 'Nabila Putri',
                'email' => 'nabila.putri@example.com',
                'avatar_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Jurnalis Teknologi & Content Creator.',
            ],
            [
                'name' => 'Reza Hardiansyah',
                'email' => 'reza.hardiansyah@example.com',
                'avatar_url' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=150&auto=format&fit=crop&q=80',
                'bio' => 'AI Researcher & Data Science Enthusiast.',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@example.com',
                'avatar_url' => 'https://images.unsplash.com/photo-1567532939604-b6b5b0db2604?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Spesialis Manajemen Produktivitas & Karir.',
            ],
            [
                'name' => 'Fajar Nugraha',
                'email' => 'fajar.nugraha@example.com',
                'avatar_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Pengembang Aplikasi Web & Mobile.',
            ],
            [
                'name' => 'Anisa Triana',
                'email' => 'anisa.triana@example.com',
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
                'bio' => 'Lifestyle & Workplace Culture Writer.',
            ],
        ];

        foreach ($authors as $auth) {
            User::updateOrCreate(
                ['email' => $auth['email']],
                [
                    'name' => $auth['name'],
                    'password' => Hash::make('password'),
                    'avatar_url' => $auth['avatar_url'],
                    'bio' => $auth['bio'],
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
