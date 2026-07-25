<script lang="ts">
    import { useForm, router } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import Input from '@/Components/UI/Input.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import RichTextEditor from '@/Components/UI/RichTextEditor.svelte';
    import FileUpload from '@/Components/UI/FileUpload.svelte';
    import { Save, ArrowLeft, Globe, Lock, Shield, FileText, Sparkles, CheckCircle2 } from 'lucide-svelte';

    interface Props {
        pageItem?: any;
        allPages?: any[];
    }

    let { pageItem = null, allPages = [] }: Props = $props();

    const isEditing = $derived(!!pageItem);

    // svelte-ignore state_referenced_locally
    const form = useForm({
        title: pageItem?.title || '',
        slug: pageItem?.slug || '',
        summary: pageItem?.summary || '',
        content: pageItem?.content || '',
        featured_image: pageItem?.featured_image || '',
        banner_image: pageItem?.banner_image || '',
        parent_id: pageItem?.parent_id || '',
        template: pageItem?.template || 'default',
        order: pageItem?.order || 0,
        status: pageItem?.status || 'published',
        visibility: pageItem?.visibility || 'public',
        password: pageItem?.password || '',
        meta_title: pageItem?.meta_title || '',
        meta_description: pageItem?.meta_description || '',
        keywords: pageItem?.keywords || '',
        canonical_url: pageItem?.canonical_url || '',
        custom_css: pageItem?.custom_css || '',
        custom_js: pageItem?.custom_js || '',
    });

    // Auto-generate slug when title changes
    function autoSlug() {
        if (!isEditing || !form.slug) {
            form.slug = form.title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        }
    }

    // Dynamic SEO Score Computation
    const seoScore = $derived(() => {
        let score = 0;
        if (form.title.length > 5) score += 25;
        if (form.slug.length > 2) score += 25;
        if (form.meta_title.length > 5) score += 25;
        if (form.meta_description.length > 10) score += 25;
        return score;
    });

    function submit(e: Event) {
        e.preventDefault();
        if (isEditing) {
            form.put(`/admin/cms/pages/${pageItem.id}`);
        } else {
            form.post('/admin/cms/pages');
        }
    }
</script>

<AppLayout title={isEditing ? `Edit Halaman: ${pageItem?.title}` : 'Buat Halaman Baru'}>
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <Button variant="outline" size="sm" onclick={() => router.get('/admin/cms/pages')}>
                <ArrowLeft class="w-4 h-4 mr-1" />
                <span>Kembali</span>
            </Button>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">
                {isEditing ? `Edit Halaman: ${pageItem?.title}` : 'Buat Halaman Baru'}
            </h1>
        </div>

        <Button variant="primary" size="md" onclick={submit} disabled={form.processing}>
            <Save class="w-4 h-4 mr-1.5" />
            <span>{isEditing ? 'Simpan Perubahan' : 'Terbitkan Halaman'}</span>
        </Button>
    </div>

    <form onsubmit={submit} class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Content Editor (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            <Card title="Detail Utama Halaman" description="Judul, slug URL, dan isi konten lengkap">
                <div class="space-y-4">
                    <Input
                        label="Judul Halaman"
                        placeholder="Contoh: Tentang Kami / Layanan Utama"
                        bind:value={form.title}
                        oninput={autoSlug}
                        error={form.errors.title}
                        required
                    />

                    <div class="space-y-1.5">
                        <label for="page-slug" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Slug URL (Permalinks)
                        </label>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-500 font-mono">domain.com/</span>
                            <Input
                                id="page-slug"
                                placeholder="tentang-kami"
                                bind:value={form.slug}
                                error={form.errors.slug}
                                class="font-mono text-xs"
                            />
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="page-summary" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Ringkasan Halaman (Excerpt)
                        </label>
                        <textarea
                            id="page-summary"
                            rows="2"
                            class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/30"
                            placeholder="Deskripsi singkat mengenai halaman ini..."
                            bind:value={form.summary}
                        ></textarea>
                    </div>

                    <div class="space-y-1.5">
                        <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Isi Konten Halaman (Rich Text Editor)
                        </span>
                        <RichTextEditor
                            bind:value={form.content}
                            placeholder="Tulis isi halaman lengkap di sini..."
                        />
                    </div>
                </div>
            </Card>

            <!-- SEO & Meta Inspector -->
            <Card title="Pengaturan SEO & Meta Tags" description="Optimalkan visibilitas halaman di mesin pencari (Google)">
                <div class="space-y-4">
                    <div class="p-3 bg-slate-900 rounded-xl border border-slate-800 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-300">SEO Score Meter</span>
                        <div class="flex items-center gap-2">
                            <div class="w-32 h-2.5 rounded-full bg-slate-800 overflow-hidden">
                                <div
                                    class={`h-full transition-all ${
                                        seoScore() >= 75 ? 'bg-emerald-500' :
                                        seoScore() >= 50 ? 'bg-amber-500' : 'bg-rose-500'
                                    }`}
                                    style={`width: ${seoScore()}%`}
                                ></div>
                            </div>
                            <span class="text-xs font-bold text-white font-mono">{seoScore()}%</span>
                        </div>
                    </div>

                    <Input label="SEO Title" placeholder="Judul khusus untuk Google search..." bind:value={form.meta_title} />
                    <Input label="Meta Description" placeholder="Deskripsi meta yang muncul di hasil pencarian..." bind:value={form.meta_description} />
                    <Input label="Keywords (Kata Kunci)" placeholder="contoh: cms, laravel, svelte" bind:value={form.keywords} />
                    <Input label="Canonical URL" placeholder="https://domain.com/canonical-link" bind:value={form.canonical_url} />
                </div>
            </Card>
        </div>

        <!-- Right Column: Settings & Attributes (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            <Card title="Status & Publikasi">
                <div class="space-y-4 text-xs">
                    <div class="space-y-1.5">
                        <label for="page-status" class="block font-semibold uppercase tracking-wider text-slate-400">Status Halaman</label>
                        <select id="page-status" bind:value={form.status} class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100">
                            <option value="published">Published (Publik)</option>
                            <option value="draft">Draft (Konsep)</option>
                            <option value="scheduled">Scheduled (Terjadwal)</option>
                            <option value="private">Private (Privat)</option>
                            <option value="archived">Archived (Arsip)</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label for="page-visibility" class="block font-semibold uppercase tracking-wider text-slate-400">Akses Visibilitas</label>
                        <select id="page-visibility" bind:value={form.visibility} class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100">
                            <option value="public">Publik untuk semua pengunjung</option>
                            <option value="private">Privat hanya pengguna login</option>
                            <option value="password">Proteksi Kata Sandi</option>
                        </select>
                    </div>

                    {#if form.visibility === 'password'}
                        <Input type="password" label="Kata Sandi Halaman" bind:value={form.password} />
                    {/if}
                </div>
            </Card>

            <Card title="Atribut & Hierarchy Halaman">
                <div class="space-y-4 text-xs">
                    <div class="space-y-1.5">
                        <label for="page-parent" class="block font-semibold uppercase tracking-wider text-slate-400">Parent Page (Halaman Induk)</label>
                        <select id="page-parent" bind:value={form.parent_id} class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100">
                            <option value="">— Tidak ada (Root Level)</option>
                            {#each allPages as p}
                                <option value={p.id}>{p.title}</option>
                            {/each}
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label for="page-template" class="block font-semibold uppercase tracking-wider text-slate-400">Template Layout</label>
                        <select id="page-template" bind:value={form.template} class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100">
                            <option value="default">Default Template</option>
                            <option value="fullwidth">Full Width Layout</option>
                            <option value="landing">Landing Builder Canvas</option>
                            <option value="sidebar">Sidebar Layout</option>
                        </select>
                    </div>

                    <Input type="number" label="Urutan Display (Order)" bind:value={form.order} />
                </div>
            </Card>

            <Card title="Featured Image">
                <Input label="URL Gambar Unggulan" placeholder="https://.../featured.png" bind:value={form.featured_image} />
            </Card>
        </div>

    </form>
</AppLayout>
