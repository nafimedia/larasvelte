<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import DataTable from '@/Components/DataTable/DataTable.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import Badge from '@/Components/UI/Badge.svelte';
    import Modal from '@/Components/UI/Modal.svelte';
    import { Plus, Edit3, Trash2, RotateCcw, FileText, Eye, CheckSquare, Layers, Lock, Shield } from 'lucide-svelte';

    interface Props {
        pages: any;
        allPages: any[];
        filters: any;
    }

    let { pages, allPages = [], filters = {} }: Props = $props();

    let selectedIds = $state<number[]>([]);
    let previewPage = $state<any>(null);
    let isPreviewOpen = $state(false);

    function toggleSelect(id: number) {
        if (selectedIds.includes(id)) {
            selectedIds = selectedIds.filter(i => i !== id);
        } else {
            selectedIds = [...selectedIds, id];
        }
    }

    function toggleSelectAll() {
        if (selectedIds.length === pages.data.length) {
            selectedIds = [];
        } else {
            selectedIds = pages.data.map((p: any) => p.id);
        }
    }

    function deletePage(id: number) {
        if (confirm('Pindahkan halaman ini ke sampah?')) {
            router.delete(`/admin/cms/pages/${id}`);
        }
    }

    function restorePage(id: number) {
        router.post(`/admin/cms/pages/${id}/restore`);
    }

    function executeBulk(action: string) {
        if (selectedIds.length === 0) return;
        if (confirm(`Terapkan tindakan "${action}" pada ${selectedIds.length} halaman?`)) {
            router.post('/admin/cms/pages/bulk', {
                ids: selectedIds,
                action
            }, {
                onSuccess: () => selectedIds = []
            });
        }
    }

    function openPreview(pageItem: any) {
        previewPage = pageItem;
        isPreviewOpen = true;
    }

    const filterOptions = $derived([
        {
            label: 'Status',
            key: 'status',
            options: [
                { label: 'Semua Status', value: '' },
                { label: 'Published', value: 'published' },
                { label: 'Draft', value: 'draft' },
                { label: 'Scheduled', value: 'scheduled' },
                { label: 'Private', value: 'private' },
                { label: 'Trash (Sampah)', value: 'trash' },
            ]
        }
    ]);
</script>

<AppLayout title="Manajemen Halaman Web (Pages)">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <FileText class="w-5 h-5 text-indigo-500" />
                <span>Manajemen Halaman Website</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola halaman bertingkat, template, slug, dan opsi publikasi</p>
        </div>
        <Button variant="primary" size="md" onclick={() => router.get('/admin/cms/pages/create')}>
            <Plus class="w-4 h-4" />
            <span>Buat Halaman Baru</span>
        </Button>
    </div>

    <!-- Bulk Actions Bar -->
    {#if selectedIds.length > 0}
        <div class="p-3 bg-indigo-950/40 border border-indigo-500/30 rounded-xl flex items-center justify-between gap-4">
            <span class="text-xs font-semibold text-indigo-300">
                {selectedIds.length} Halaman Terpilih
            </span>
            <div class="flex items-center gap-2">
                <Button variant="outline" size="sm" onclick={() => executeBulk('publish')}>Set Published</Button>
                <Button variant="outline" size="sm" onclick={() => executeBulk('draft')}>Set Draft</Button>
                <Button variant="danger" size="sm" onclick={() => executeBulk('delete')}>Hapus</Button>
            </div>
        </div>
    {/if}

    <!-- Pages Data Table -->
    <DataTable
        searchable={true}
        searchPlaceholder="Cari judul atau slug halaman..."
        searchValue={filters.search}
        filters={filters}
        filterOptions={filterOptions}
        pagination={pages}
        routePath="/admin/cms/pages"
    >
        {#snippet header()}
            <tr>
                <th class="w-10 px-4 py-3 text-center">
                    <input
                        type="checkbox"
                        checked={pages.data?.length > 0 && selectedIds.length === pages.data?.length}
                        onchange={toggleSelectAll}
                        class="rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-indigo-500"
                    />
                </th>
                <th class="px-4 py-3">Judul Halaman</th>
                <th class="px-4 py-3">Slug URL</th>
                <th class="px-4 py-3">Parent Page</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Penulis</th>
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
        {/snippet}

        {#each (pages.data || []) as pageItem}
            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                <td class="w-10 px-4 py-3 text-center">
                    <input
                        type="checkbox"
                        checked={selectedIds.includes(pageItem.id)}
                        onchange={() => toggleSelect(pageItem.id)}
                        class="rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-indigo-500"
                    />
                </td>
                <td class="px-4 py-3">
                    <div class="space-y-0.5">
                        <p class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                            {#if pageItem.parent}
                                <span class="text-xs text-indigo-400 font-normal">↳</span>
                            {/if}
                            {pageItem.title}
                        </p>
                        <span class="text-[10px] text-slate-400 font-mono">Template: {pageItem.template || 'default'}</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-xs text-indigo-400 font-mono">
                    /{pageItem.slug}
                </td>
                <td class="px-4 py-3 text-xs text-slate-400">
                    {pageItem.parent?.title || '— (Root)'}
                </td>
                <td class="px-4 py-3">
                    <Badge variant={
                        pageItem.status === 'published' ? 'success' :
                        pageItem.status === 'draft' ? 'slate' : 'danger'
                    }>
                        {pageItem.status.toUpperCase()}
                    </Badge>
                </td>
                <td class="px-4 py-3 text-xs text-slate-400">
                    {pageItem.author?.name || 'Admin'}
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-1">
                        <Button variant="ghost" size="icon" onclick={() => openPreview(pageItem)} title="Preview Halaman">
                            <Eye class="w-4 h-4 text-slate-400" />
                        </Button>

                        {#if pageItem.deleted_at}
                            <Button variant="ghost" size="icon" onclick={() => restorePage(pageItem.id)} title="Pulihkan">
                                <RotateCcw class="w-4 h-4 text-emerald-400" />
                            </Button>
                        {:else}
                            <Button variant="ghost" size="icon" onclick={() => router.get(`/admin/cms/pages/${pageItem.id}/edit`)} title="Edit Halaman">
                                <Edit3 class="w-4 h-4 text-indigo-400" />
                            </Button>
                            <Button variant="ghost" size="icon" onclick={() => deletePage(pageItem.id)} title="Hapus">
                                <Trash2 class="w-4 h-4 text-rose-400" />
                            </Button>
                        {/if}
                    </div>
                </td>
            </tr>
        {/each}
    </DataTable>

    <!-- Preview Modal -->
    <Modal bind:open={isPreviewOpen} title={`Preview: ${previewPage?.title}`} description="Detail isi ringkasan halaman">
        {#if previewPage}
            <div class="space-y-4 text-xs">
                <div class="p-3 bg-slate-900 rounded-xl border border-slate-800 space-y-1">
                    <p class="text-slate-400 font-mono">URL: /{previewPage.slug}</p>
                    <p class="text-slate-400">Status: {previewPage.status} | Visibility: {previewPage.visibility}</p>
                </div>
                <div class="prose prose-invert max-w-none">
                    {@html previewPage.content || previewPage.summary || '<p class="text-slate-500 italic">Konten halaman kosong.</p>'}
                </div>
            </div>
        {/if}
    </Modal>
</AppLayout>
