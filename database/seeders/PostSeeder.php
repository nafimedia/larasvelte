<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $authors = User::all();
        $categories = Category::all()->keyBy('slug');
        $tags = Tag::all()->keyBy('slug');

        if ($authors->isEmpty() || $categories->isEmpty()) {
            return;
        }

        $articlesData = [
            [
                'title' => 'Transformasi Digital dalam Dunia Pendidikan Modern',
                'category_slug' => 'pendidikan',
                'tag_slugs' => ['pembelajaran', 'akademik', 'kampus'],
                'status' => 'published',
                'published_at' => '2023-01-15 08:00:00',
                'created_at' => '2026-07-20 10:00:00', // Backdated!
                'views' => 18450, // Popular
                'is_featured' => true,
                'is_sticky' => true,
                'featured_image' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=800&auto=format&fit=crop&q=80',
                'summary' => 'Bagaimana adopsi teknologi e-learning dan otomatisasi kampus mengubah metode pengajaran era abad 21.',
                'content' => '<h2>Pendahuluan</h2>
<p>Dunia pendidikan mengalami revolusi terbesar sepanjang sejarah dengan hadirnya teknologi digital. Institusi pendidikan kini tidak hanya mengandalkan tatap muka langsung, melainkan mengadopsi platform terpadu untuk efisiensi pengajaran.</p>

<h2>Manfaat Teknologi dalam Pengajaran</h2>
<p>Implementasi Learning Management System (LMS) memberikan akses materi 24 jam penuh bagi mahasiswa. Pengajar dapat memantau perkembangan belajar secara inklusif dan berbasis data analitik.</p>

<h2>Integrasi Kecerdasan Buatan</h2>
<p>Fitur rekomendasi kuis personal dan asisten belajar AI membantu siswa memahami konsep sulit secara mandiri dengan kecepatan belajar yang disesuaikan.</p>

<h2>Kesimpulan</h2>
<p>Transformasi digital di sektor pendidikan bukan lagi pilihan melainkan kebutuhan mutlak untuk mencetak generasi bersaing di pasar global.</p>',
            ],
            [
                'title' => 'Panduan Lengkap Membangun Website Profesional Tahun 2026',
                'category_slug' => 'tutorial',
                'tag_slugs' => ['web-development', 'programming', 'software'],
                'status' => 'published',
                'published_at' => '2024-05-10 09:30:00',
                'created_at' => '2024-05-10 09:00:00',
                'views' => 12300, // Popular
                'is_featured' => true,
                'is_sticky' => false,
                'featured_image' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800&auto=format&fit=crop&q=80',
                'summary' => 'Langkah praktis merancang arsitektur web modern menggunakan Laravel 13, Svelte 5, dan Tailwind CSS v4.',
                'content' => '<h2>Pendahuluan</h2>
<p>Membangun website berkinerja tinggi memerlukan pemilihan stack teknologi yang tepat. Kombinasi Laravel sebagai fondasi backend yang solid dan Svelte 5 untuk antarmuka yang responsif menjadi standar baru pengembang profesional.</p>

<h2>Persiapan Environment Development</h2>
<p>Pastikan PHP 8.3 dan Node.js terbaru sudah terinstal di server lokal Anda. Jalankan perintah instalasi starter kit modern untuk menghemat waktu penyiapan infrastruktur dasar.</p>

<h2>Optimasi Performa & Rendering</h2>
<p>Gunakan konsep Svelte 5 Runes ($state, $derived) untuk manajemen state yang bersih tanpa reaktivitas berlebih yang memberatkan peramban web pengguna.</p>

<h2>Kesimpulan</h2>
<p>Dengan mengikuti kaidah arsitektur teruji, website Anda tidak hanya memanjakan pengunjung tetapi juga mendapat nilai SEO tinggi dari Google.</p>',
            ],
            [
                'title' => 'Strategi Digital Marketing untuk Meningkatkan Bisnis UMKM',
                'category_slug' => 'digital-marketing',
                'tag_slugs' => ['umkm', 'marketing', 'digital-business'],
                'status' => 'published',
                'published_at' => '2024-11-20 14:15:00',
                'created_at' => '2024-11-20 12:00:00',
                'views' => 8900, // Popular
                'is_featured' => true,
                'is_sticky' => false,
                'featured_image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&auto=format&fit=crop&q=80',
                'summary' => 'Langkah nyata memanfaatkan pemasaran media sosial dan SEO lokal untuk melipatgandakan omset usaha kecil menengah.',
                'content' => '<h2>Pendahuluan</h2>
<p>Sebagian besar pelaku UMKM masih kesulitan menjangkau pasar nasional akibat belum optimalnya pemanfaatan media digital. Pemasaran digital menawarkan efisiensi biaya tertinggi dibanding metode konvensional.</p>

<h2>Riset Kata Kunci dan Profil Google Bisnis</h2>
<p>Langkah pertama yang paling efisien adalah mendaftarkan alamat fisik usaha Anda di Google Maps dan mengoptimalkan profil bisnis lokal.</p>

<h2>Strategi Konten Pemasaran</h2>
<p>Buatlah konten edukatif yang menjawab permasalahan langsung calon pembeli daripada sekadar memposting brosur promosi jualan secara terus-menerus.</p>

<h2>Kesimpulan</h2>
<p>Konsistensi adalah kunci utama dalam pemasaran digital. Mulailah dari kanal yang paling dikuasai sebelum merambah ke ekosistem yang lebih luas.</p>',
            ],
            [
                'title' => 'Peran Teknologi AI dalam Perkembangan Pendidikan',
                'category_slug' => 'teknologi',
                'tag_slugs' => ['ai', 'machine-learning', 'pembelajaran'],
                'status' => 'published',
                'published_at' => '2025-03-01 11:00:00',
                'created_at' => '2025-03-01 10:00:00',
                'views' => 4500, // Normal
                'is_featured' => false,
                'is_sticky' => false,
                'featured_image' => 'https://images.unsplash.com/photo-1677442136019-21780efad99a?w=800&auto=format&fit=crop&q=80',
                'summary' => 'Dampak penggunaan Generative AI dan Machine Learning terhadap akselerasi riset ilmiah dan metode belajar siswa.',
                'content' => '<h2>Pendahuluan</h2>
<p>Kecerdasan buatan telah mengubah lanskap akademis secara drastis dalam beberapa tahun terakhir. Dari evaluasi otomatis hingga pengolahan bahasa alami, AI mempercepat proses riset.</p>

<h2>Asisten Belajar Personal</h2>
<p>Siswa kini memiliki tutor virtual 24 jam yang siap menjelaskan konsep matematika atau pemrograman rumit menggunakan analogi sederhana.</p>

<h2>Etika & Integritas Akademik</h2>
<p>Penting bagi institusi untuk merumuskan panduan penggunaan AI yang bijak agar integritas akademis dan orisinalitas pemikiran siswa tetap terjaga.</p>

<h2>Kesimpulan</h2>
<p>AI merupakan pendamping hebat jika dimanfaatkan sebagai penguat kapabilitas manusia, bukan sebagai pengganti proses berpikir kritis.</p>',
            ],
            [
                'title' => 'Tips Mengelola Produktivitas Kerja Secara Efektif',
                'category_slug' => 'produktivitas',
                'tag_slugs' => ['productivity', 'software'],
                'status' => 'published',
                'published_at' => '2025-06-18 07:45:00',
                'created_at' => '2025-06-18 07:00:00',
                'views' => 2750, // Normal
                'is_featured' => false,
                'is_sticky' => false,
                'featured_image' => 'https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?w=800&auto=format&fit=crop&q=80',
                'summary' => 'Metode time blocking, teknik pomodoro, dan pengelolaan fokus saat bekerja jarak jauh.',
                'content' => '<h2>Pendahuluan</h2>
<p>Bekerja secara cerdas jauh lebih bernilai dibanding bekerja berjam-jam tanpa arah yang jelas. Gangguan notifikasi smartphone seringkali merusak fokus utama pekerjaan.</p>

<h2>Teknik Time Blocking</h2>
<p>Alokasikan blok waktu khusus untuk menyelesaikan satu jenis tugas tertentu tanpa distraksi pembukaan email atau aplikasi perpesanan.</p>

<h2>Keseimbangan Istirahat</h2>
<p>Otak membutuhkan jeda istirahat berkala untuk mempertahankan konsentrasi tinggi secara berkelanjutan sepanjang hari.</p>

<h2>Kesimpulan</h2>
<p>Produktivitas sejati dicapai saat Anda dapat menyelesaikan pekerjaan berkualitas tinggi dengan tingkat stres yang terkendali.</p>',
            ],
            [
                'title' => 'Universitas Mengembangkan Sistem Akademik Digital Terintegrasi',
                'category_slug' => 'kampus',
                'tag_slugs' => ['kampus', 'mahasiswa', 'akademik'],
                'status' => 'published',
                'published_at' => '2026-01-10 13:00:00',
                'created_at' => '2026-07-25 15:00:00', // Backdate scenario!
                'views' => 3400, // Normal
                'is_featured' => false,
                'is_sticky' => false,
                'featured_image' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=800&auto=format&fit=crop&q=80',
                'summary' => 'Peluncuran portal akademik satu pintu yang memudahkan pengisian KRS, pembayaran UKT, dan layanan wisuda.',
                'content' => '<h2>Pendahuluan</h2>
<p>Guna memberikan pelayanan terbaik bagi seluruh civitas akademika, universitas meresmikan sistem informasi akademik berbasis arsitektur modern modern.</p>

<h2>Kemudahan Layanan Mandiri Mahasiswa</h2>
<p>Seluruh proses administrasi mulai dari cetak transkrip nilai hingga konsultasi dosen pembimbing dapat diakses langsung dari ponsel cerdas.</p>

<h2>Integrasi Keamanan Sistem</h2>
<p>Sistem baru ini dilengkapi enkripsi ganda untuk menjamin keamanan data pribadi dan riwayat akademik seluruh mahasiswa.</p>

<h2>Kesimpulan</h2>
<p>Peluncuran portal ini menandai komitmen kampus menjadi percontohan kampus berorientasi teknologi di tingkat internasional.</p>',
            ],
            [
                'title' => 'Cara Membuat Konten Berkualitas untuk Website',
                'category_slug' => 'berita',
                'tag_slugs' => ['marketing', 'web-development'],
                'status' => 'published',
                'published_at' => '2026-04-05 10:20:00',
                'created_at' => '2026-04-05 10:00:00',
                'views' => 1200, // Normal
                'is_featured' => false,
                'is_sticky' => false,
                'featured_image' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?w=800&auto=format&fit=crop&q=80',
                'summary' => 'Prinsip penulisan riset mendalam, tata bahasa yang menarik, serta penyesuaian SEO pada konten blog.',
                'content' => '<h2>Pendahuluan</h2>
<p>Konten adalah raja di dunia digital. Namun, hanya konten yang relevan dan memberikan solusi nyata yang sanggup menahan pengunjung tetap berada di website Anda.</p>

<h2>Struktur Penulisan yang Nyaman Dibaca</h2>
<p>Gunakan subjudul yang jelas, paragraf pendek, dan poin-poin tebal agar pembaca dapat melakukan scanning informasi dengan mudah.</p>

<h2>Optimasi Kata Kunci Tanpa Keyword Stuffing</h2>
<p>Tempatkan kata kunci secara natural pada judul, paragraf pembuka, dan subjudul utama tanpa merusak kualitas keterbacaan.</p>

<h2>Kesimpulan</h2>
<p>Investasi waktu pada riset konten yang mendalam selalu memberikan imbal hasil traffic jangka panjang yang berkesinambungan.</p>',
            ],
            [
                'title' => 'Mengenal Cloud Computing dan Manfaatnya Bagi Perusahaan',
                'category_slug' => 'teknologi',
                'tag_slugs' => ['cloud-computing', 'software', 'digital-business'],
                'status' => 'published',
                'published_at' => '2026-06-12 16:00:00',
                'created_at' => '2026-06-12 15:30:00',
                'views' => 980, // Normal
                'is_featured' => false,
                'is_sticky' => false,
                'featured_image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&auto=format&fit=crop&q=80',
                'summary' => 'Mengapa migrasi infrastruktur fisik ke komputasi awan dapat menghemat pengeluaran IT hingga 40%.',
                'content' => '<h2>Pendahuluan</h2>
<p>Biaya perawatan server fisik yang tinggi mendorong perusahaan dari berbagai skala bisnis beralih menggunakan platform komputasi awan scalable.</p>

<h2>Skalabilitas dan Keandalan Infrastruktur</h2>
<p>Cloud computing memungkinkan server menambah kapasitas komputasi secara otomatis saat terjadi lonjakan traffic pengunjung secara tiba-tiba.</p>

<h2>Keamanan dan Manajemen Disaster Recovery</h2>
<p>Penyedia cloud terkemuka menjamin ketersediaan backup data otomatis tersebar di multiple lokasi geografis secara aman.</p>

<h2>Kesimpulan</h2>
<p>Adopsi cloud computing adalah langkah strategis untuk menjamin kelangsungan operasional teknologi perusahaan di era digital.</p>',
            ],
            [
                'title' => 'Strategi SEO Terbaru untuk Meningkatkan Traffic Website',
                'category_slug' => 'digital-marketing',
                'tag_slugs' => ['marketing', 'web-development', 'digital-business'],
                'status' => 'published',
                'published_at' => '2026-07-25 10:00:00', // Latest Post
                'created_at' => '2026-07-25 09:30:00',
                'views' => 45, // New Article
                'is_featured' => true,
                'is_sticky' => false,
                'featured_image' => 'https://images.unsplash.com/photo-1571721795195-a2ca2d3370a9?w=800&auto=format&fit=crop&q=80',
                'summary' => 'Faktor algoritma pencarian Google terbaru yang berfokus pada kecepatan muat, EEAT, dan kepuasan pengguna.',
                'content' => '<h2>Pendahuluan</h2>
<p>Algoritma mesin pencari terus berevolusi untuk menyajikan hasil terbaik. Strategi SEO manipulatif seperti link farm sudah tidak lagi efektif.</p>

<h2>Faktor Pengalaman Pengguna & Kecepatan Website</h2>
<p>Google menempatkan indikator Core Web Vitals sebagai salah satu sinyal pemeringkatan utama. Website yang lambat memuat halaman akan mengalami penurunan rangking.</p>

<h2>Penerapan Prinsip EEAT (Experience, Expertise, Authoritativeness, Trustworthiness)</h2>
<p>Tunjukkan kredibilitas penulis dan sertakan sumber riset yang sahih untuk membangun kepercayaan algoritma dan pengunjung.</p>

<h2>Kesimpulan</h2>
<p>Fokuslah pada penciptaan nilai terbaik bagi pengguna, maka posisi rangking pencarian akan mengikuti secara alami.</p>',
            ],
            [
                'title' => 'Tren Ekosistem Startup Teknologi Indonesia Masa Depan',
                'category_slug' => 'bisnis',
                'tag_slugs' => ['startup', 'digital-business', 'umkm'],
                'status' => 'published',
                'published_at' => '2026-07-24 14:00:00',
                'created_at' => '2026-07-24 13:00:00',
                'views' => 88, // New Article
                'is_featured' => false,
                'is_sticky' => false,
                'featured_image' => 'https://images.unsplash.com/photo-1559136555-9303baea8ebd?w=800&auto=format&fit=crop&q=80',
                'summary' => 'Pergeseran fokus dari bakar uang menuju profitabilitas berkelanjutan pada dunia rintisan teknologi.',
                'content' => '<h2>Pendahuluan</h2>
<p>Ekosistem rintisan teknologi di Asia Tenggara memasuki babak kedewasaan baru di mana investor lebih memprioritaskan indikator profitabilitas jernih.</p>

<h2>Pertumbuhan Sektor Agritech dan Climate Tech</h2>
<p>Sektor teknologi pertanian dan energi terbarukan menarik minat pendanaan besar berkat nilai dampak sosial nyata yang dihasilkannya.</p>

<h2>Pentingnya Tata Kelola Perusahaan</h2>
<p>Pendiri startup dituntut memiliki tata kelola keuangan yang transparan dan disiplin alokasi modal agar mampu bertahan melalui gelombang ketidakpastian.</p>

<h2>Kesimpulan</h2>
<p>Startup yang berfokus menyelesaikan masalah riil masyarakat dengan modal efisien akan menjadi pemenang jangka panjang.</p>',
            ],
            // 3 DRAFT ARTICLES
            [
                'title' => '[Draft] Panduan Implementasi Microservices dengan Laravel',
                'category_slug' => 'tutorial',
                'tag_slugs' => ['software', 'programming', 'cloud-computing'],
                'status' => 'draft',
                'published_at' => null,
                'created_at' => '2026-07-25 11:00:00',
                'views' => 0,
                'is_featured' => false,
                'is_sticky' => false,
                'featured_image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format&fit=crop&q=80',
                'summary' => 'Konsep pemisahan monolithic Laravel menjadi layanan independen berkemampuan tinggi.',
                'content' => '<h2>Pendahuluan</h2><p>Draf tulisan tentang arsitektur komunikasi event-driven menggunakan RabbitMQ dan Redis.</p>',
            ],
            [
                'title' => '[Draft] Rahasia Membangun Budaya Kerja Tim Remote yang Efektif',
                'category_slug' => 'lifestyle',
                'tag_slugs' => ['productivity', 'software'],
                'status' => 'draft',
                'published_at' => null,
                'created_at' => '2026-07-25 11:30:00',
                'views' => 0,
                'is_featured' => false,
                'is_sticky' => false,
                'featured_image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&auto=format&fit=crop&q=80',
                'summary' => 'Pola komunikasi asinkron untuk menjaga ritme kerja tim terdistribusi lintas zona waktu.',
                'content' => '<h2>Pendahuluan</h2><p>Draf panduan internal manajemen kolaborasi tim engineering jarak jauh.</p>',
            ],
            [
                'title' => '[Draft] Strategi Monetisasi Aplikasi SaaS untuk Pasar Asia Tenggara',
                'category_slug' => 'bisnis',
                'tag_slugs' => ['startup', 'digital-business'],
                'status' => 'draft',
                'published_at' => null,
                'created_at' => '2026-07-25 12:00:00',
                'views' => 0,
                'is_featured' => false,
                'is_sticky' => false,
                'featured_image' => 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?w=800&auto=format&fit=crop&q=80',
                'summary' => 'Model penentuan harga berlapis (tiered pricing) untuk menjangkau pasar bisnis lokal.',
                'content' => '<h2>Pendahuluan</h2><p>Draf studi kasus riset monetisasi produk perangkat lunak sebagai layanan.</p>',
            ],
            // 1 SCHEDULED ARTICLE
            [
                'title' => '[Terjadwal] Pengumuman Pembaruan Platform FairuzKit Versi 3.0',
                'category_slug' => 'pengumuman',
                'tag_slugs' => ['web-development', 'software', 'programming'],
                'status' => 'scheduled',
                'published_at' => '2026-08-01 09:00:00', // Future Scheduled Date!
                'created_at' => '2026-07-25 14:00:00',
                'views' => 0,
                'is_featured' => true,
                'is_sticky' => false,
                'featured_image' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=800&auto=format&fit=crop&q=80',
                'summary' => 'Fitur-fitur terbaru yang akan diluncurkan pada pembaruan akbar bulan depan.',
                'content' => '<h2>Pendahuluan</h2>
<p>Kami sangat bersemangat mengumumkan peluncuran versi terbaru yang dilengkapi arsitektur visual builder yang lebih cepat dan aman.</p>
<h2>Fitur Unggulan Versi 3.0</h2>
<p>Termasuk peningkatan kecepatan rendering Svelte 5, dukungan mode gelap otomatis, dan integrasi API RESTful.</p>
<h2>Kesimpulan</h2>
<p>Nantikan tanggal peluncurannya pada awal bulan depan!</p>',
            ],
            // 1 ARCHIVED ARTICLE
            [
                'title' => '[Arsip] Laporan Perkembangan Teknologi Perkuliahan Tahun 2022',
                'category_slug' => 'pengumuman',
                'tag_slugs' => ['akademik', 'kampus'],
                'status' => 'archived',
                'published_at' => '2022-12-20 10:00:00',
                'created_at' => '2022-12-20 09:00:00',
                'views' => 6400,
                'is_featured' => false,
                'is_sticky' => false,
                'featured_image' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=800&auto=format&fit=crop&q=80',
                'summary' => 'Dokumen arsip laporan perkembangan sistem teknologi masa lalu.',
                'content' => '<h2>Pendahuluan</h2><p>Laporan historis terkait evaluasi implementasi sistem perkuliahan era 2022.</p>',
            ],
        ];

        foreach ($articlesData as $art) {
            $cat = $categories->get($art['category_slug']);
            $author = $authors->random();

            $post = Post::updateOrCreate(
                ['slug' => Str::slug($art['title'])],
                [
                    'title' => $art['title'],
                    'summary' => $art['summary'],
                    'content' => $art['content'],
                    'featured_image' => $art['featured_image'],
                    'author_id' => $author->id,
                    'category_id' => $cat?->id,
                    'status' => $art['status'],
                    'published_at' => $art['published_at'],
                    'created_at' => $art['created_at'],
                    'updated_at' => $art['created_at'],
                    'view_count' => $art['views'],
                    'reading_time' => max(1, (int) ceil(str_word_count(strip_tags($art['content'])) / 200)),
                    'is_featured' => $art['is_featured'],
                    'is_sticky' => $art['is_sticky'],
                    'allow_comment' => true,
                    'meta_title' => $art['title'] . ' | FairuzKit Blog',
                    'meta_description' => Str::limit(strip_tags($art['summary']), 150),
                    'keywords' => implode(', ', $art['tag_slugs']),
                    'canonical_url' => config('app.url') . '/blog/' . Str::slug($art['title']),
                ]
            );

            // Sync Tags
            $tagIds = [];
            foreach ($art['tag_slugs'] as $tslug) {
                if ($t = $tags->get($tslug)) {
                    $tagIds[] = $t->id;
                }
            }
            $post->tags()->sync($tagIds);

            // Generate 20+ Realistic Comments for published posts
            if ($art['status'] === 'published' && rand(0, 1) === 1) {
                $commenters = [
                    ['name' => 'Bambang Wijaya', 'email' => 'bambang@example.com', 'text' => 'Artikel yang sangat bermanfaat dan mudah dipahami! Terima kasih penjelasannya.'],
                    ['name' => 'Dewi Anggraini', 'email' => 'dewi.a@example.com', 'text' => 'Sangat menginspirasi. Saya mencoba menerapkan strategi ini pada proyek saya.'],
                    ['name' => 'Hendro Prasetyo', 'email' => 'hendro@example.com', 'text' => 'Penjelasan mengenai poin ke-2 sangat mendalam. Sukses selalu untuk tim penulis.'],
                    ['name' => 'Maya Kartika', 'email' => 'maya.k@example.com', 'text' => 'Apakah ada rekomendasi referensi tambahan mengenai topik ini? moga ada part 2.'],
                ];

                foreach ($commenters as $comm) {
                    Comment::create([
                        'post_id' => $post->id,
                        'author_name' => $comm['name'],
                        'author_email' => $comm['email'],
                        'content' => $comm['text'],
                        'status' => 'approved',
                        'created_at' => now()->subDays(rand(1, 30)),
                    ]);
                }
            }
        }
    }
}
