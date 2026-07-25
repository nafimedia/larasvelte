<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import DataTable from '@/Components/DataTable/DataTable.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import Badge from '@/Components/UI/Badge.svelte';
    import Modal from '@/Components/UI/Modal.svelte';
    import { Plus, Edit3, Trash2, RotateCcw, Newspaper, Eye, Pin, Star, Clock, Folder, ExternalLink, Copy } from 'lucide-svelte';

    interface Props {
        posts: any;
        categories: any[];
        tags: any[];
        filters: any;
    }

    let { posts, categories = [], tags = [], filters = {} }: Props = $props();

    let selectedIds = $state<number[]>([]);
    let previewPost = $state<any>(null);
    let isPreviewOpen = $state(false);

    function handlePostView(postItem: any) {
        if (postItem.status === 'published') {
            window.open(`/blog/${postItem.slug}`, '_blank');
        } else {
            const token = postItem.preview_token || 'preview';
            window.open(`/preview/posts/${postItem.id}/${token}`, '_blank');
        }
    }

    function duplicatePost(id: number) {
        if (confirm('Gandakan artikel ini menjadi draf baru?')) {
            router.post(`/admin/cms/posts/${id}/duplicate`);
        }
    }

    function toggleSelect(id: number) {
        if (selectedIds.includes(id)) {
            selectedIds = selectedIds.filter(i => i !== id);
        } else {
            selectedIds = [...selectedIds, id];
        }
    }

    function toggleSelectAll() {
        if (selectedIds.length === posts.data.length) {
            selectedIds = [];
        } else {
            selectedIds = posts.data.map((p: any) => p.id);
        }
    }

    function deletePost(id: number) {
        if (confirm('Pindahkan artikel ini ke sampah?')) {
            router.delete(`/admin/cms/posts/${id}`);
        }
    }

    function restorePost(id: number) {
        router.post(`/admin/cms/posts/${id}/restore`);
    }

    function executeBulk(action: string) {
        if (selectedIds.length === 0) return;
        if (confirm(`Terapkan tindakan "${action}" pada ${selectedIds.length} artikel?`)) {
            router.post('/admin/cms/posts/bulk', {
                ids: selectedIds,
                action
            }, {
                onSuccess: () => selectedIds = []
            });
        }
    }

    function openPreview(postItem: any) {
        previewPost = postItem;
        isPreviewOpen = true;
    }

    const filterOptions = $derived([
        {
            label: 'Kategori',
            key: 'category',
            options: [
                { label: 'Semua Kategori', value: '' },
                ...categories.map(c => ({ label: c.name, value: String(c.id) }))
            ]
        },
        {
            label: 'Status',
            key: 'status',
            options: [
                { label: 'Semua Status', value: '' },
                { label: 'Published', value: 'published' },
                { label: 'Draft', value: 'draft' },
                { label: 'Scheduled', value: 'scheduled' },
                { label: 'Trash (Sampah)', value: 'trash' },
            ]
        }
    ]);
</script>

<AppLayout title="Manajemen Artikel & Blog (Posts)">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <Newspaper class="w-5 h-5 text-indigo-500" />
                <span>Manajemen Artikel & Berita</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola postingan blog, artikel berita, kategori, tag, dan waktu baca</p>
        </div>
        <Button variant="primary" size="md" onclick={() => router.get('/admin/cms/posts/create')}>
            <Plus class="w-4 h-4" />
            <span>Tulis Artikel Baru</span>
        </Button>
    </div>

    <!-- Bulk Actions Bar -->
    {#if selectedIds.length > 0}
        <div class="p-3 bg-indigo-950/40 border border-indigo-500/30 rounded-xl flex items-center justify-between gap-4">
            <span class="text-xs font-semibold text-indigo-300">
                {selectedIds.length} Artikel Terpilih
            </span>
            <div class="flex items-center gap-2">
                <Button variant="outline" size="sm" onclick={() => executeBulk('publish')}>Publikasikan</Button>
                <Button variant="outline" size="sm" onclick={() => executeBulk('draft')}>Jadikan Draft</Button>
                <Button variant="danger" size="sm" onclick={() => executeBulk('delete')}>Hapus</Button>
            </div>
        </div>
    {/if}

    <!-- Posts Data Table -->
    <DataTable
        searchable={true}
        searchPlaceholder="Cari judul artikel atau konten..."
        searchValue={filters.search}
        filters={filters}
        filterOptions={filterOptions}
        pagination={posts}
        routePath="/admin/cms/posts"
    >
        {#snippet header()}
            <tr>
                <th class="w-10 px-4 py-3 text-center">
                    <input
                        type="checkbox"
                        checked={posts.data?.length > 0 && selectedIds.length === posts.data?.length}
                        onchange={toggleSelectAll}
                        class="rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-indigo-500"
                    />
                </th>
                <th class="px-4 py-3">Artikel</th>
                <th class="px-4 py-3">Kategori</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Tanggal Terbit</th>
                <th class="px-4 py-3">Stats</th>
                <th class="px-4 py-3">Penulis</th>
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
        {/snippet}

        {#each (posts.data || []) as postItem}
            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                <td class="w-10 px-4 py-3 text-center">
                    <input
                        type="checkbox"
                        checked={selectedIds.includes(postItem.id)}
                        onchange={() => toggleSelect(postItem.id)}
                        class="rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-indigo-500"
                    />
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        {#if postItem.featured_image}
                            <img src={postItem.featured_image} alt={postItem.title} class="w-12 h-12 object-cover rounded-lg border border-slate-700 shrink-0" />
                        {:else}
                            <div class="w-12 h-12 rounded-lg bg-slate-800 flex items-center justify-center text-slate-500 shrink-0">
                                <Newspaper class="w-6 h-6" />
                            </div>
                        {/if}
                        <div class="space-y-0.5 max-w-xs">
                            <p class="font-bold text-slate-900 dark:text-slate-100 truncate flex items-center gap-1">
                                {#if postItem.is_sticky}
                                    <span title="Sticky Post"><Pin class="w-3 h-3 text-amber-400 fill-amber-400 shrink-0" /></span>
                                {/if}
                                {#if postItem.is_featured}
                                    <span title="Featured Article"><Star class="w-3 h-3 text-indigo-400 fill-indigo-400 shrink-0" /></span>
                                {/if}
                                <span>{postItem.title}</span>
                            </p>
                            <p class="text-[10px] text-slate-400 font-mono truncate">/{postItem.slug}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-xs">
                    {#if postItem.category}
                        <span class="px-2 py-0.5 rounded-full bg-slate-800 text-indigo-300 font-semibold border border-slate-700">
                            {postItem.category.name}
                        </span>
                    {:else}
                        <span class="text-slate-500">— Uncategorized</span>
                    {/if}
                </td>
                <td class="px-4 py-3">
                    <Badge variant={
                        postItem.status === 'published' ? 'success' :
                        postItem.status === 'draft' ? 'slate' : 'danger'
                    }>
                        {postItem.status.toUpperCase()}
                    </Badge>
                </td>
                <td class="px-4 py-3 text-xs text-slate-400 font-mono">
                    {postItem.published_at ? new Date(postItem.published_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' }) : 'Draft'}
                </td>
                <td class="px-4 py-3 text-xs text-slate-400 font-mono">
                    <div class="flex items-center gap-2">
                        <span class="flex items-center gap-1" title="Reading Time">
                            <Clock class="w-3 h-3 text-amber-400" />
                            {postItem.reading_time}m
                        </span>
                        <span class="flex items-center gap-1" title="Total Views">
                            <Eye class="w-3 h-3 text-cyan-400" />
                            {postItem.view_count || 0}
                        </span>
                    </div>
                </td>
                <td class="px-4 py-3 text-xs text-slate-400">
                    {postItem.author?.name || 'Admin'}
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-1">
                        <Button variant="ghost" size="icon" onclick={() => handlePostView(postItem)} title={postItem.status === 'published' ? 'Lihat Post Live' : 'Preview Artikel (Draft Mode)'}>
                            <ExternalLink class="w-4 h-4 text-cyan-400" />
                        </Button>
                        <Button variant="ghost" size="icon" onclick={() => openPreview(postItem)} title="Detail Quick View">
                            <Eye class="w-4 h-4 text-slate-400" />
                        </Button>

                        {#if postItem.deleted_at}
                            <Button variant="ghost" size="icon" onclick={() => restorePost(postItem.id)} title="Pulihkan">
                                <RotateCcw class="w-4 h-4 text-emerald-400" />
                            </Button>
                        {:else}
                            <Button variant="ghost" size="icon" onclick={() => duplicatePost(postItem.id)} title="Gandakan Artikel">
                                <Copy class="w-4 h-4 text-amber-400" />
                            </Button>
                            <Button variant="ghost" size="icon" onclick={() => router.get(`/admin/cms/posts/${postItem.id}/edit`)} title="Edit Artikel">
                                <Edit3 class="w-4 h-4 text-indigo-400" />
                            </Button>
                            <Button variant="ghost" size="icon" onclick={() => deletePost(postItem.id)} title="Hapus">
                                <Trash2 class="w-4 h-4 text-rose-400" />
                            </Button>
                        {/if}
                    </div>
                </td>
            </tr>
        {/each}
    </DataTable>

    <!-- Article Preview Modal -->
    <Modal bind:open={isPreviewOpen} title={`Preview: ${previewPost?.title}`} description="Detail isi artikel blog">
        {#if previewPost}
            <div class="space-y-4 text-xs">
                {#if previewPost.featured_image}
                    <img src={previewPost.featured_image} alt={previewPost.title} class="w-full h-48 object-cover rounded-xl border border-slate-800" />
                {/if}
                <div class="p-3 bg-slate-900 rounded-xl border border-slate-800 space-y-1">
                    <p class="text-slate-400 font-mono">URL Slug: /{previewPost.slug}</p>
                    <p class="text-slate-400">Estimasi Waktu Baca: {previewPost.reading_time} Menit</p>
                </div>
                <div class="prose prose-invert max-w-none">
                    {@html previewPost.content || previewPost.summary || '<p class="text-slate-500 italic">Konten artikel belum diisi.</p>'}
                </div>
            </div>
        {/if}
    </Modal>
</AppLayout>
