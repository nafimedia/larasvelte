<script lang="ts">
    import { useForm, router } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import DataTable from '@/Components/DataTable/DataTable.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import Input from '@/Components/UI/Input.svelte';
    import Modal from '@/Components/UI/Modal.svelte';
    import { Plus, Edit3, Trash2, Folder, Layers } from 'lucide-svelte';

    interface Props {
        categories: any;
        allCategories?: any[];
        filters: any;
    }

    let { categories, allCategories = [], filters = {} }: Props = $props();

    let isModalOpen = $state(false);
    let editingCategory = $state<any>(null);

    // svelte-ignore state_referenced_locally
    const form = useForm({
        name: '',
        slug: '',
        parent_id: '',
        description: '',
        icon: '',
        cover_image: '',
    });

    function openCreateModal() {
        editingCategory = null;
        form.reset();
        isModalOpen = true;
    }

    function openEditModal(category: any) {
        editingCategory = category;
        form.name = category.name;
        form.slug = category.slug;
        form.parent_id = category.parent_id || '';
        form.description = category.description || '';
        form.icon = category.icon || '';
        form.cover_image = category.cover_image || '';
        isModalOpen = true;
    }

    function submit(e: Event) {
        e.preventDefault();
        if (editingCategory) {
            form.put(`/admin/cms/categories/${editingCategory.id}`, {
                onSuccess: () => isModalOpen = false,
            });
        } else {
            form.post('/admin/cms/categories', {
                onSuccess: () => {
                    isModalOpen = false;
                    form.reset();
                },
            });
        }
    }

    function deleteCategory(id: number) {
        if (confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
            router.delete(`/admin/cms/categories/${id}`);
        }
    }
</script>

<AppLayout title="Manajemen Kategori Artikel">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <Folder class="w-5 h-5 text-indigo-500" />
                <span>Kategori Artikel & Berita</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola taksonomi kategori bertingkat (Nested Categories)</p>
        </div>
        <Button variant="primary" size="md" onclick={openCreateModal}>
            <Plus class="w-4 h-4" />
            <span>Tambah Kategori Baru</span>
        </Button>
    </div>

    <!-- Categories Data Table -->
    <DataTable
        searchable={true}
        searchPlaceholder="Cari nama atau slug kategori..."
        searchValue={filters.search}
        filters={filters}
        pagination={categories}
        routePath="/admin/cms/categories"
    >
        {#snippet header()}
            <tr>
                <th class="px-4 py-3">Nama Kategori</th>
                <th class="px-4 py-3">Slug URL</th>
                <th class="px-4 py-3">Kategori Induk</th>
                <th class="px-4 py-3">Total Artikel</th>
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
        {/snippet}

        {#each (categories.data || []) as category}
            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                <td class="px-4 py-3">
                    <div class="space-y-0.5">
                        <p class="font-bold text-slate-900 dark:text-slate-100">{category.name}</p>
                        <p class="text-[10px] text-slate-400">{category.description || 'Tidak ada deskripsi'}</p>
                    </div>
                </td>
                <td class="px-4 py-3 text-xs text-indigo-400 font-mono">
                    /{category.slug}
                </td>
                <td class="px-4 py-3 text-xs text-slate-400">
                    {category.parent?.name || '— (Root)'}
                </td>
                <td class="px-4 py-3 text-xs font-mono font-bold text-slate-300">
                    {category.posts_count || 0} Posts
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-1">
                        <Button variant="ghost" size="icon" onclick={() => openEditModal(category)} title="Edit Kategori">
                            <Edit3 class="w-4 h-4 text-indigo-400" />
                        </Button>
                        <Button variant="ghost" size="icon" onclick={() => deleteCategory(category.id)} title="Hapus Kategori">
                            <Trash2 class="w-4 h-4 text-rose-400" />
                        </Button>
                    </div>
                </td>
            </tr>
        {/each}
    </DataTable>

    <!-- Create/Edit Modal -->
    <Modal bind:open={isModalOpen} title={editingCategory ? 'Edit Kategori' : 'Tambah Kategori Baru'} description="Isi form data kategori di bawah ini">
        <form onsubmit={submit} class="space-y-4">
            <Input label="Nama Kategori" placeholder="Contoh: Pemrograman / Tips & Trik" bind:value={form.name} error={form.errors.name} required />
            <Input label="Slug URL (Opsional)" placeholder="pemrograman" bind:value={form.slug} error={form.errors.slug} />

            <div class="space-y-1.5">
                <label for="cat-parent" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Kategori Induk (Parent)</label>
                <select id="cat-parent" bind:value={form.parent_id} class="w-full px-3.5 py-2 text-sm rounded-lg border bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100">
                    <option value="">— Tanpa Induk (Root Category)</option>
                    {#each allCategories as c}
                        <option value={c.id}>{c.name}</option>
                    {/each}
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="cat-desc" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Deskripsi</label>
                <textarea id="cat-desc" rows="2" class="w-full px-3.5 py-2 text-sm rounded-lg border bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100" bind:value={form.description}></textarea>
            </div>
        </form>

        {#snippet footer()}
            <Button variant="outline" size="sm" onclick={() => isModalOpen = false}>Batal</Button>
            <Button variant="primary" size="sm" onclick={submit} disabled={form.processing}>Simpan Kategori</Button>
        {/snippet}
    </Modal>
</AppLayout>
