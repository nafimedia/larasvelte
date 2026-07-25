<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingSection;
use App\Models\LandingSectionVersion;
use App\Models\LandingSiteSetting;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class LandingBuilderController extends Controller
{
    /**
     * Display the visual landing page builder SPA.
     */
    public function index()
    {
        $sections = LandingSection::orderBy('order', 'asc')->get();

        // Seed default sections if database is empty
        if ($sections->isEmpty()) {
            $sections = $this->seedDefaultSections();
        }

        $themeSettings = LandingSiteSetting::get('theme_config', [
            'primaryColor' => '#6366f1',
            'secondaryColor' => '#a855f7',
            'accentColor' => '#ec4899',
            'backgroundColor' => '#090d16',
            'fontFamily' => 'Plus Jakarta Sans',
            'darkMode' => true,
            'containerWidth' => '7xl',
        ]);

        $seoSettings = LandingSiteSetting::get('seo_config', [
            'metaTitle' => 'FairuzKit — Starter Kit Laravel 13 + Svelte 5 + Inertia',
            'metaDescription' => 'Starter kit full-stack terlengkap dengan Svelte 5 Runes, Laravel 13 RBAC, Inertia.js, Tailwind v4 & Dark Mode bawaan.',
            'keywords' => 'laravel 13, svelte 5, inertia.js, starter kit, rbac, tailwindcss',
            'ogImage' => '/images/hero-hijab.png',
        ]);

        $mediaFiles = MediaFile::latest()->get();

        return Inertia::render('Admin/LandingBuilder/Index', [
            'sections' => $sections,
            'themeSettings' => $themeSettings,
            'seoSettings' => $seoSettings,
            'mediaFiles' => $mediaFiles,
        ]);
    }

    /**
     * Create a new landing section.
     */
    public function storeSection(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'name' => 'required|string',
            'title' => 'nullable|string',
            'subtitle' => 'nullable|string',
        ]);

        $maxOrder = LandingSection::max('order') ?? 0;
        $slug = Str::slug($validated['name']) . '-' . Str::random(4);

        $defaultContent = $this->getDefaultContentForType($validated['type']);
        $defaultSettings = [
            'background' => 'transparent',
            'paddingTop' => 'py-20',
            'containerWidth' => '7xl',
            'alignment' => 'left',
            'animation' => 'fade-in',
            'hideMobile' => false,
        ];

        $section = LandingSection::create([
            'section_id' => $slug,
            'type' => $validated['type'],
            'name' => $validated['name'],
            'title' => $validated['title'] ?? 'Judul Section Baru',
            'subtitle' => $validated['subtitle'] ?? 'Deskripsi singkat mengenai section ini',
            'description' => '',
            'content' => $defaultContent,
            'settings' => $defaultSettings,
            'order' => $maxOrder + 1,
            'is_active' => true,
            'status' => 'draft',
        ]);

        return redirect()->back()->with('success', 'Section baru berhasil ditambahkan');
    }

    /**
     * Update an existing section.
     */
    public function updateSection(Request $request, $id)
    {
        $section = LandingSection::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string',
            'title' => 'nullable|string',
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
            'content' => 'required|array',
            'settings' => 'required|array',
            'is_active' => 'boolean',
        ]);

        $section->update([
            'name' => $validated['name'],
            'title' => $validated['title'] ?? '',
            'subtitle' => $validated['subtitle'] ?? '',
            'description' => $validated['description'] ?? '',
            'content' => $validated['content'],
            'settings' => $validated['settings'],
            'is_active' => $validated['is_active'] ?? $section->is_active,
            'status' => 'draft',
        ]);

        return redirect()->back()->with('success', 'Perubahan section disimpan sebagai draft');
    }

    /**
     * Reorder sections.
     */
    public function reorderSections(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:landing_sections,id',
            'orders.*.order' => 'required|integer',
        ]);

        foreach ($request->orders as $item) {
            LandingSection::where('id', $item['id'])->update([
                'order' => $item['order'],
                'status' => 'draft',
            ]);
        }

        return redirect()->back()->with('success', 'Urutan section berhasil diperbarui');
    }

    /**
     * Duplicate a section.
     */
    public function duplicateSection($id)
    {
        $original = LandingSection::findOrFail($id);

        $maxOrder = LandingSection::max('order') ?? 0;
        $newSlug = $original->type . '-' . Str::random(6);

        $duplicate = $original->replicate();
        $duplicate->section_id = $newSlug;
        $duplicate->name = $original->name . ' (Salinan)';
        $duplicate->order = $maxOrder + 1;
        $duplicate->status = 'draft';
        $duplicate->save();

        return redirect()->back()->with('success', 'Section berhasil diduplikasi');
    }

    /**
     * Delete a section.
     */
    public function destroySection($id)
    {
        $section = LandingSection::findOrFail($id);
        $section->delete();

        return redirect()->back()->with('success', 'Section berhasil dihapus');
    }

    /**
     * Publish all draft changes to live site.
     */
    public function publish()
    {
        $sections = LandingSection::all();

        foreach ($sections as $section) {
            $section->update(['status' => 'published']);

            // Save version snapshot
            LandingSectionVersion::create([
                'landing_section_id' => $section->id,
                'version_name' => 'Version ' . date('Y-m-d H:i:s'),
                'content' => $section->content,
                'settings' => $section->settings,
                'created_by' => auth()->id(),
            ]);
        }

        return redirect()->back()->with('success', 'Seluruh perubahan landing page berhasil dipublikasikan ke live site!');
    }

    /**
     * Update global Theme & SEO Settings.
     */
    public function updateSettings(Request $request)
    {
        if ($request->has('theme')) {
            LandingSiteSetting::set('theme_config', $request->input('theme'));
        }

        if ($request->has('seo')) {
            LandingSiteSetting::set('seo_config', $request->input('seo'));
        }

        return redirect()->back()->with('success', 'Pengaturan global berhasil diperbarui');
    }

    /**
     * Handle Media File Upload.
     */
    public function uploadMedia(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,webp,svg,gif,mp4,pdf|max:10240',
            'alt_text' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads', $filename, 'public');

        $media = MediaFile::create([
            'name' => $file->getClientOriginalName(),
            'file_path' => '/storage/' . $path,
            'mime_type' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
            'alt_text' => $request->input('alt_text', $file->getClientOriginalName()),
            'folder' => 'general',
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'File media berhasil diunggah');
    }

    /**
     * Default content structure helper.
     */
    private function getDefaultContentForType(string $type): array
    {
        switch ($type) {
            case 'hero':
                return [
                    'badge' => 'Build SaaS Enterprise in Record Time',
                    'heading' => 'Starter Kit Fullstack Laravel 13 + Svelte 5',
                    'highlight_text' => 'Terkeren untuk SaaS Anda',
                    'description' => 'Nikmati kekuatan Svelte 5 Runes berpadu dengan arsitektur Laravel 13, Inertia v2, RBAC granular, dan Tailwind CSS v4.',
                    'primary_btn_text' => 'Coba Demo Aplikasi',
                    'primary_btn_url' => '/login',
                    'secondary_btn_text' => 'Panduan Instalasi',
                    'secondary_btn_url' => '#quickstart',
                    'hero_image' => '/images/hero-hijab.png',
                ];
            case 'features':
                return [
                    'items' => [
                        ['title' => 'Performa Kilat Svelte 5', 'desc' => 'Svelte 5 Runes untuk reaktivitas tingkat granular.', 'icon' => 'Zap'],
                        ['title' => 'Sistem RBAC Presisi', 'desc' => 'Super Admin, Admin, dan User dengan hak akses granular.', 'icon' => 'ShieldCheck'],
                        ['title' => 'UI Components & Dark Mode', 'desc' => 'Data Table, Modal, Toast, Dialog, Avatar, File Upload.', 'icon' => 'Layers'],
                    ]
                ];
            case 'stats':
                return [
                    'items' => [
                        ['value' => '100%', 'label' => 'Svelte 5 Runes Native'],
                        ['value' => '0.05s', 'label' => 'Rata-rata Response Time'],
                        ['value' => '10+', 'label' => 'Komponen UI Siap Pakai'],
                        ['value' => '100%', 'label' => 'Open Source & Royalty Free'],
                    ]
                ];
            case 'testimonials':
                return [
                    'items' => [
                        ['name' => 'Fairuz Tech', 'role' => 'CTO at TechSaaS', 'comment' => 'FairuzKit memangkas waktu pengembangan proyek kami dari 3 bulan menjadi hanya 1 minggu!', 'avatar' => '/images/hero-hijab.png'],
                        ['name' => 'Budi Santoso', 'role' => 'Lead Developer', 'comment' => 'Arsitektur Laravel 13 + Svelte 5 paling rapi dan mudah digunakan.', 'avatar' => ''],
                    ]
                ];
            case 'pricing':
                return [
                    'plans' => [
                        ['name' => 'Community', 'price' => 'Gratis', 'period' => 'selamanya', 'features' => ['Laravel 13 & Svelte 5', 'RBAC System', 'Community Support'], 'button_text' => 'Download Free'],
                        ['name' => 'Pro Lifetime', 'price' => 'Rp 499.000', 'period' => 'sekali bayar', 'features' => ['Semua Fitur Community', 'Landing Builder Studio', 'Full Source Code & Updates'], 'button_text' => 'Beli Lisensi Pro', 'is_popular' => true],
                    ]
                ];
            case 'faq':
                return [
                    'items' => [
                        ['question' => 'Apakah FairuzKit menggunakan Svelte 5 Runes?', 'answer' => 'Ya! FairuzKit menggunakan Svelte 5 terbaru dengan sintaks $state, $derived, dan $effect.'],
                        ['question' => 'Apakah mendukung Dark Mode?', 'answer' => 'Tentu saja, Dark Mode terintegrasi penuh secara otomatis di seluruh komponen.'],
                    ]
                ];
            default:
                return [
                    'html_content' => '<div class="p-8 bg-slate-900 rounded-2xl text-center"><h3 class="text-xl font-bold">Custom Content</h3><p class="text-slate-400 mt-2">Edit konten ini sesuai kebutuhan Anda.</p></div>'
                ];
        }
    }

    /**
     * Seed default initial sections if empty.
     */
    private function seedDefaultSections()
    {
        $defaultSectionsData = [
            [
                'section_id' => 'hero-main',
                'type' => 'hero',
                'name' => 'Hero Banner Utama',
                'title' => 'Starter Kit Fullstack Laravel 13 + Svelte 5',
                'subtitle' => 'Terkeren untuk SaaS Anda',
                'description' => 'Nikmati kekuatan Svelte 5 Runes ($state, $derived) berpadu dengan arsitektur Laravel 13, Inertia v2, RBAC granular, dan Tailwind CSS v4. Siap dideploy hari ini!',
                'content' => $this->getDefaultContentForType('hero'),
                'settings' => ['background' => 'transparent', 'paddingTop' => 'py-20', 'containerWidth' => '7xl', 'alignment' => 'left', 'animation' => 'fade-in', 'hideMobile' => false],
                'order' => 1,
                'is_active' => true,
                'status' => 'published',
            ],
            [
                'section_id' => 'stats-bar',
                'type' => 'stats',
                'name' => 'Statistik Performa',
                'title' => 'Metrik FairuzKit',
                'subtitle' => '',
                'description' => '',
                'content' => $this->getDefaultContentForType('stats'),
                'settings' => ['background' => 'slate-900/50', 'paddingTop' => 'py-12', 'containerWidth' => '7xl', 'alignment' => 'center', 'animation' => 'none', 'hideMobile' => false],
                'order' => 2,
                'is_active' => true,
                'status' => 'published',
            ],
            [
                'section_id' => 'features-grid',
                'type' => 'features',
                'name' => 'Fitur Unggulan',
                'title' => 'Semua Fitur Esensial SaaS dalam Satu Kit',
                'subtitle' => 'Anda tidak perlu membuang waktu berminggu-minggu membuat fitur dari nol.',
                'description' => '',
                'content' => $this->getDefaultContentForType('features'),
                'settings' => ['background' => 'transparent', 'paddingTop' => 'py-24', 'containerWidth' => '7xl', 'alignment' => 'center', 'animation' => 'fade-in', 'hideMobile' => false],
                'order' => 3,
                'is_active' => true,
                'status' => 'published',
            ],
            [
                'section_id' => 'faq-accordion',
                'type' => 'faq',
                'name' => 'Pertanyaan Umum (FAQ)',
                'title' => 'Pertanyaan Sering Diajukan',
                'subtitle' => 'Jawaban lengkap mengenai FairuzKit',
                'description' => '',
                'content' => $this->getDefaultContentForType('faq'),
                'settings' => ['background' => 'slate-900/30', 'paddingTop' => 'py-20', 'containerWidth' => '4xl', 'alignment' => 'center', 'animation' => 'fade-in', 'hideMobile' => false],
                'order' => 4,
                'is_active' => true,
                'status' => 'published',
            ]
        ];

        foreach ($defaultSectionsData as $sec) {
            LandingSection::create($sec);
        }

        return LandingSection::orderBy('order', 'asc')->get();
    }
}
