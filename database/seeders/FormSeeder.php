<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\FormField;
use App\Models\FormSubmission;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Form Kontak Kami
        $contactForm = Form::updateOrCreate(
            ['slug' => 'kontak-kami'],
            [
                'title' => 'Formulir Kontak & Pertanyaan',
                'description' => 'Silakan isi formulir di bawah ini untuk terhubung dengan tim customer service kami.',
                'theme_color' => '#6366F1',
                'is_accepting_responses' => true,
                'confirmation_message' => 'Terima kasih telah menghubungi kami! Tim kami akan membalas pesan Anda dalam 1x24 jam.',
                'require_login' => false,
            ]
        );

        $contactForm->fields()->delete();

        FormField::create([
            'form_id' => $contactForm->id,
            'type' => 'text',
            'label' => 'Nama Lengkap Anda',
            'help_text' => 'Tuliskan nama lengkap sesuai kartu identitas.',
            'placeholder' => 'mis. Budi Santoso',
            'is_required' => true,
            'order' => 1,
        ]);

        FormField::create([
            'form_id' => $contactForm->id,
            'type' => 'text',
            'label' => 'Alamat Email',
            'help_text' => 'Email aktif untuk balasan dari tim kami.',
            'placeholder' => 'budi@example.com',
            'is_required' => true,
            'order' => 2,
        ]);

        FormField::create([
            'form_id' => $contactForm->id,
            'type' => 'dropdown',
            'label' => 'Topik Pertanyaan / Layanan',
            'options' => ['Konsultasi Layanan', 'Dukungan Teknis', 'Kerjasama Bisnis', 'Lainnya'],
            'is_required' => true,
            'order' => 3,
        ]);

        FormField::create([
            'form_id' => $contactForm->id,
            'type' => 'paragraph',
            'label' => 'Pesan / Pertanyaan Anda',
            'placeholder' => 'Jelaskan detail pesan atau pertanyaan Anda di sini...',
            'is_required' => true,
            'order' => 4,
        ]);

        // Sample submission for contact form
        FormSubmission::create([
            'form_id' => $contactForm->id,
            'ip_address' => '127.0.0.1',
            'response_data' => [
                'field_1' => 'Ahmad Rizky',
                'field_2' => 'ahmad.rizky@example.com',
                'field_3' => 'Konsultasi Layanan',
                'field_4' => 'Halo tim LaraSvelte, saya tertarik untuk menggunakan solusi CMS ini untuk project perusahaan kami. Mohon informasi paketnya.',
            ],
            'is_read' => true,
        ]);

        // 2. Form Survei Kepuasan Pelanggan
        $surveyForm = Form::updateOrCreate(
            ['slug' => 'survei-kepuasan-pelanggan'],
            [
                'title' => 'Survei Kepuasan Layanan Website 2026',
                'description' => 'Bantu kami meningkatkan kualitas layanan dengan mengisi survei singkat ini (estimasi 2 menit).',
                'theme_color' => '#10B981',
                'is_accepting_responses' => true,
                'confirmation_message' => 'Terima kasih atas partisipasi Anda dalam survei kepuasan pelanggan kami!',
                'require_login' => false,
            ]
        );

        $surveyForm->fields()->delete();

        FormField::create([
            'form_id' => $surveyForm->id,
            'type' => 'rating',
            'label' => 'Berapa tingkat kepuasan Anda terhadap kecepatan website kami?',
            'help_text' => 'Skala 1 (Sangat Buruk) hingga 5 (Sangat Puas).',
            'is_required' => true,
            'order' => 1,
        ]);

        FormField::create([
            'form_id' => $surveyForm->id,
            'type' => 'multiple_choice',
            'label' => 'Seberapa sering Anda mengunjungi website kami dalam sebulan?',
            'options' => ['Hampir Setiap Hari', '1-3 Kali Seminggu', 'Beberapa Kali Sebulan', 'Ini Kunjungan Pertama Saya'],
            'is_required' => true,
            'order' => 2,
        ]);

        FormField::create([
            'form_id' => $surveyForm->id,
            'type' => 'checkboxes',
            'label' => 'Fitur apa saja yang paling sering Anda gunakan?',
            'options' => ['Artikel & Blog', 'Landing Builder Studio', 'Media Library', 'Form Builder', 'SEO Manager'],
            'is_required' => false,
            'order' => 3,
        ]);

        FormField::create([
            'form_id' => $surveyForm->id,
            'type' => 'paragraph',
            'label' => 'Saran dan Masukan Tambahan',
            'placeholder' => 'Berikan saran konstruktif Anda untuk pengembangan kami selanjutnya...',
            'is_required' => false,
            'order' => 4,
        ]);
    }
}
