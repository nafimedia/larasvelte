<script lang="ts">
    import { useForm, router } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import Input from '@/Components/UI/Input.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import RichTextEditor from '@/Components/UI/RichTextEditor.svelte';
    import FileUpload from '@/Components/UI/FileUpload.svelte';
    import RevisionsModal from '@/Components/CMS/RevisionsModal.svelte';
    import { Save, ArrowLeft, Globe, Lock, Shield, Newspaper, Sparkles, Clock, Pin, Star, History, Copy, CheckCircle2, AlertCircle } from 'lucide-svelte';

    interface Props {
        postItem?: any;
        categories?: any[];
        tags?: any[];
        allPosts?: any[];
        revisions?: any[];
    }

    let {
        postItem = null,
        categories = [],
        tags = [],
        allPosts = [],
        revisions = []
    }: Props = $props();

    const isEditing = $derived(!!postItem);
    let isRevisionsOpen = $state(false);
    let autosaveState = $state<'idle' | 'saving' | 'saved'>('idle');
    let autosaveTime = $state<string>('');

    // svelte-ignore state_referenced_locally
    const form = useForm({
        title: postItem?.title || '',
        slug: postItem?.slug || '',
        summary: postItem?.summary || '',
        content: postItem?.content || '',
        featured_image: postItem?.featured_image || '',
        category_id: postItem?.category_id || '',
        tag_ids: postItem?.tags ? postItem.tags.map((t: any) => t.id) : [] as number[],
        manual_related_ids: postItem?.manual_related_ids || [] as number[],
        is_sticky: postItem?.is_sticky || false,
        is_featured: postItem?.is_featured || false,
        allow_comment: postItem?.allow_comment ?? true,
        status: postItem?.status || 'published',
        published_at: postItem?.published_at ? new Date(postItem.published_at).toISOString().slice(0, 16) : new Date().toISOString().slice(0, 16),
        timezone: 'Asia/Jakarta',
        meta_title: postItem?.meta_title || '',
        meta_description: postItem?.meta_description || '',
        keywords: postItem?.keywords || '',
        canonical_url: postItem?.canonical_url || '',
    });

    // Auto-generate slug when title changes
    function autoSlug() {
        if (!isEditing || !form.slug) {
            form.slug = form.title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        }
    }

    function toggleTag(tagId: number) {
        if (form.tag_ids.includes(tagId)) {
            form.tag_ids = form.tag_ids.filter((id: number) => id !== tagId);
        } else {
            form.tag_ids = [...form.tag_ids, tagId];
        }
    }

    function toggleRelated(postId: number) {
        if (form.manual_related_ids.includes(postId)) {
            form.manual_related_ids = form.manual_related_ids.filter((id: number) => id !== postId);
        } else {
            form.manual_related_ids = [...form.manual_related_ids, postId];
        }
    }

    function duplicateArticle() {
        if (!postItem) return;
        if (confirm('Gandakan artikel ini menjadi draf baru?')) {
            router.post(`/admin/cms/posts/${postItem.id}/duplicate`);
        }
    }

    // Dynamic Reading Time Estimator (200 wpm)
    const estimatedReadingTime = $derived(() => {
        const words = (form.content || '').trim().split(/\s+/).filter(Boolean).length;
        return Math.max(1, Math.ceil(words / 200));
    });

    function submit(e: Event) {
        e.preventDefault();
        if (isEditing) {
            form.put(`/admin/cms/posts/${postItem.id}`);
        } else {
            form.post('/admin/cms/posts');
        }
    }
</script>

<AppLayout title={isEditing ? `Edit Artikel: ${postItem?.title}` : 'Tulis Artikel Baru'}>
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <Button variant="outline" size="sm" onclick={() => router.get('/admin/cms/posts')}>
                <ArrowLeft class="w-4 h-4 mr-1" />
                <span>Kembali</span>
            </Button>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">
                {isEditing ? `Edit Artikel: ${postItem?.title}` : 'Tulis Artikel Baru'}
            </h1>
        </div>

        <div class="flex items-center gap-2">
            {#if isEditing}
                <Button variant="outline" size="sm" onclick={() => isRevisionsOpen = true}>
                    <History class="w-4 h-4 mr-1 text-indigo-400" />
                    <span>Versi ({revisions.length})</span>
                </Button>
                <Button variant="outline" size="sm" onclick={duplicateArticle}>
                    <Copy class="w-4 h-4 mr-1 text-amber-400" />
                    <span>Gandakan</span>
                </Button>
            {/if}

            <Button variant="primary" size="md" onclick={submit} disabled={form.processing}>
                <Save class="w-4 h-4 mr-1.5" />
                <span>{isEditing ? 'Simpan Perubahan' : 'Terbitkan Artikel'}</span>
            </Button>
        </div>
    </div>

    <form onsubmit={submit} class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Content Editor (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            <Card title="Konten Artikel" description="Tulis judul, slug, dan isi postingan blog">
                <div class="space-y-4">
                    <Input
                        label="Judul Artikel"
                        placeholder="Contoh: Panduan Menguasai Svelte 5 Runes dalam 10 Menit"
                        bind:value={form.title}
                        oninput={autoSlug}
                        error={form.errors.title}
                        required
                    />

                    <div class="space-y-1.5">
                        <label for="post-slug" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Slug URL Artikel
                        </label>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-500 font-mono">domain.com/blog/</span>
                            <Input
                                id="post-slug"
                                placeholder="panduan-svelte-5-runes"
                                bind:value={form.slug}
                                error={form.errors.slug}
                                class="font-mono text-xs"
                            />
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="post-summary" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Ringkasan Artikel (Excerpt)
                        </label>
                        <textarea
                            id="post-summary"
                            rows="2"
                            class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/30"
                            placeholder="Ringkasan singkat yang akan muncul di daftar blog & social share..."
                            bind:value={form.summary}
                        ></textarea>
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Isi Artikel (Rich Text Editor)
                            </span>
                            <span class="text-xs text-amber-400 font-mono flex items-center gap-1">
                                <Clock class="w-3.5 h-3.5" />
                                Est. Baca: {estimatedReadingTime()} Menit
                            </span>
                        </div>
                        <RichTextEditor
                            bind:value={form.content}
                            placeholder="Tulis artikel blog lengkap di sini..."
                        />
                    </div>
                </div>
            </Card>

            <!-- Manual Related Posts Selection Card -->
            {#if allPosts.length > 0}
                <Card title="Pilihan Artikel Terkait Manual (Manual Related Posts)" description="Pilih khusus artikel yang akan direkomendasikan di bagian bawah halaman detail artikel">
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-1 text-xs">
                        {#each allPosts as p}
                            <label class="flex items-center justify-between p-2 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 cursor-pointer">
                                <div class="flex items-center gap-2 truncate">
                                    <input
                                        type="checkbox"
                                        checked={form.manual_related_ids.includes(p.id)}
                                        onchange={() => toggleRelated(p.id)}
                                        class="rounded border-slate-700 bg-slate-950 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <span class="font-semibold text-slate-200 truncate">{p.title}</span>
                                </div>
                                <span class="text-[10px] text-slate-500 font-mono">/{p.slug}</span>
                            </label>
                        {/each}
                    </div>
                </Card>
            {/if}

            <!-- SEO Settings Card -->
            <Card title="Optimasi SEO Meta Tags" description="Metadeta Google & Open Graph Social Media">
                <div class="space-y-4">
                    <Input label="SEO Title" placeholder="Judul optimasi mesin pencari..." bind:value={form.meta_title} />
                    <Input label="Meta Description" placeholder="Deskripsi meta 150-160 karakter..." bind:value={form.meta_description} />
                    <Input label="Keywords" placeholder="artikel, svelte 5, laravel 13" bind:value={form.keywords} />
                    <Input label="Canonical Link" placeholder="https://domain.com/blog/original-post" bind:value={form.canonical_url} />
                </div>
            </Card>
        </div>

        <!-- Right Column: Taxonomy & Attributes (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            <Card title="Publikasi & Editorial Workflow">
                <div class="space-y-4 text-xs">
                    <div class="space-y-1.5">
                        <label for="post-status" class="block font-semibold uppercase tracking-wider text-slate-400">Status Editorial</label>
                        <select id="post-status" bind:value={form.status} class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 font-semibold">
                            <option value="draft">Draft (Konsep Penulis)</option>
                            <option value="review">Review Redaksi (In Review)</option>
                            <option value="revision">Memerlukan Revisi (Need Revision)</option>
                            <option value="approved">Disetujui Redaksi (Approved)</option>
                            <option value="published">Published (Terbit Publik)</option>
                            <option value="scheduled">Scheduled (Terjadwal)</option>
                            <option value="private">Private (Privat)</option>
                        </select>
                    </div>

                    <!-- Manual Published Date & Timepicker -->
                    <div class="space-y-1.5 pt-2 border-t border-slate-800">
                        <label for="published-at" class="block font-semibold uppercase tracking-wider text-slate-400">
                            Tanggal & Jam Publikasi (Manual / Backdate)
                        </label>
                        <input
                            type="datetime-local"
                            id="published-at"
                            bind:value={form.published_at}
                            class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 font-mono text-xs focus:ring-2 focus:ring-indigo-500/30"
                        />
                        <p class="text-[10px] text-slate-400">Atur tanggal mundur (Backdate) atau tanggal publikasi masa lalu/depan secara bebas seperti di WordPress.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="post-tz" class="block font-semibold uppercase tracking-wider text-slate-400">Zona Waktu (Timezone)</label>
                        <select id="post-tz" bind:value={form.timezone} class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 font-mono text-xs">
                            <option value="Asia/Jakarta">WIB — Asia/Jakarta (UTC+7)</option>
                            <option value="Asia/Makassar">WITA — Asia/Makassar (UTC+8)</option>
                            <option value="Asia/Jayapura">WIT — Asia/Jayapura (UTC+9)</option>
                            <option value="UTC">UTC — Universal Time</option>
                        </select>
                    </div>

                    <div class="pt-2 border-t border-slate-800 space-y-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" bind:checked={form.is_sticky} class="rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-indigo-500" />
                            <span class="font-semibold text-slate-300">Sticky Post (Sematkan di Atas)</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" bind:checked={form.is_featured} class="rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-indigo-500" />
                            <span class="font-semibold text-slate-300">Featured Article (Unggulan)</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" bind:checked={form.allow_comment} class="rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-indigo-500" />
                            <span class="font-semibold text-slate-300">Izinkan Komentar</span>
                        </label>
                    </div>
                </div>
            </Card>

            <Card title="Kategori & Tag Taksonomi">
                <div class="space-y-4 text-xs">
                    <div class="space-y-1.5">
                        <label for="post-category" class="block font-semibold uppercase tracking-wider text-slate-400">Pilih Kategori</label>
                        <select id="post-category" bind:value={form.category_id} class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100">
                            <option value="">— Pilih Kategori</option>
                            {#each categories as c}
                                <option value={c.id}>{c.name}</option>
                            {/each}
                        </select>
                    </div>

                    <div class="space-y-1.5 pt-2 border-t border-slate-800">
                        <span class="block font-semibold uppercase tracking-wider text-slate-400">Pilih Tag Artikel</span>
                        <div class="flex flex-wrap gap-1.5 pt-1">
                            {#each tags as t}
                                <button
                                    type="button"
                                    onclick={() => toggleTag(t.id)}
                                    class={`px-2.5 py-1 rounded-full text-xs font-semibold border transition-all ${
                                        form.tag_ids.includes(t.id)
                                            ? 'bg-indigo-600 text-white border-indigo-500 shadow-xs'
                                            : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white'
                                    }`}
                                >
                                    #{t.name}
                                </button>
                            {/each}
                        </div>
                    </div>
                </div>
            </Card>

            <Card title="Featured Image">
                <Input label="URL Gambar Sampul Artikel" placeholder="https://.../cover.png" bind:value={form.featured_image} />
            </Card>
        </div>

    </form>

    <!-- Post Revisions Modal -->
    <RevisionsModal bind:open={isRevisionsOpen} postId={postItem?.id} revisions={revisions} />
</AppLayout>
