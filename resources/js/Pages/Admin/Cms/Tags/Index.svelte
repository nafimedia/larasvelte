<script lang="ts">
    import { useForm, router } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import DataTable from '@/Components/DataTable/DataTable.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import Input from '@/Components/UI/Input.svelte';
    import Modal from '@/Components/UI/Modal.svelte';
    import { Plus, Edit3, Trash2, Tag as TagIcon } from 'lucide-svelte';

    interface Props {
        tags: any;
        filters: any;
    }

    let { tags, filters = {} }: Props = $props();

    let isModalOpen = $state(false);
    let editingTag = $state<any>(null);

    // svelte-ignore state_referenced_locally
    const form = useForm({
        name: '',
        slug: '',
        color: '#6366f1',
        description: '',
    });

    function openCreateModal() {
        editingTag = null;
        form.reset();
        isModalOpen = true;
    }

    function openEditModal(tag: any) {
        editingTag = tag;
        form.name = tag.name;
        form.slug = tag.slug;
        form.color = tag.color || '#6366f1';
        form.description = tag.description || '';
        isModalOpen = true;
    }

    function submit(e: Event) {
        e.preventDefault();
        if (editingTag) {
            form.put(`/admin/cms/tags/${editingTag.id}`, {
                onSuccess: () => isModalOpen = false,
            });
        } else {
            form.post('/admin/cms/tags', {
                onSuccess: () => {
                    isModalOpen = false;
                    form.reset();
                },
            });
        }
    }

    function deleteTag(id: number) {
        if (confirm('Apakah Anda yakin ingin menghapus tag ini?')) {
            router.delete(`/admin/cms/tags/${id}`);
        }
    }
</script>

<AppLayout title="Manajemen Tag Artikel">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <TagIcon class="w-5 h-5 text-indigo-500" />
                <span>Tag Artikel & Berita</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola kata kunci tag penanda artikel blog</p>
        </div>
        <Button variant="primary" size="md" onclick={openCreateModal}>
            <Plus class="w-4 h-4" />
            <span>Tambah Tag Baru</span>
        </Button>
    </div>

    <!-- Tags Data Table -->
    <DataTable
        searchable={true}
        searchPlaceholder="Cari nama tag..."
        searchValue={filters.search}
        filters={filters}
        pagination={tags}
        routePath="/admin/cms/tags"
    >
        {#snippet header()}
            <tr>
                <th class="px-4 py-3">Nama Tag</th>
                <th class="px-4 py-3">Slug URL</th>
                <th class="px-4 py-3">Warna Pill</th>
                <th class="px-4 py-3">Total Artikel</th>
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
        {/snippet}

        {#each (tags.data || []) as tag}
            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                <td class="px-4 py-3">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold text-white border shadow-xs" style={`background-color: ${tag.color}; border-color: ${tag.color}`}>
                        #{tag.name}
                    </span>
                </td>
                <td class="px-4 py-3 text-xs text-indigo-400 font-mono">
                    /{tag.slug}
                </td>
                <td class="px-4 py-3 text-xs font-mono">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full border border-slate-700" style={`background-color: ${tag.color}`}></div>
                        <span>{tag.color}</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-xs font-mono font-bold text-slate-300">
                    {tag.posts_count || 0} Posts
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-1">
                        <Button variant="ghost" size="icon" onclick={() => openEditModal(tag)} title="Edit Tag">
                            <Edit3 class="w-4 h-4 text-indigo-400" />
                        </Button>
                        <Button variant="ghost" size="icon" onclick={() => deleteTag(tag.id)} title="Hapus Tag">
                            <Trash2 class="w-4 h-4 text-rose-400" />
                        </Button>
                    </div>
                </td>
            </tr>
        {/each}
    </DataTable>

    <!-- Create/Edit Modal -->
    <Modal bind:open={isModalOpen} title={editingTag ? 'Edit Tag' : 'Tambah Tag Baru'} description="Isi data label tag di bawah ini">
        <form onsubmit={submit} class="space-y-4">
            <Input label="Nama Tag" placeholder="Contoh: Svelte5 / Tutorials" bind:value={form.name} error={form.errors.name} required />
            <Input label="Slug URL (Opsional)" placeholder="tutorials" bind:value={form.slug} error={form.errors.slug} />

            <div class="space-y-1.5">
                <label for="tag-color" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Warna Tag Badge</label>
                <div class="flex items-center gap-3">
                    <input type="color" id="tag-color" bind:value={form.color} class="w-10 h-10 rounded-lg cursor-pointer bg-slate-900 border border-slate-800" />
                    <Input bind:value={form.color} class="font-mono text-xs" />
                </div>
            </div>
        </form>

        {#snippet footer()}
            <Button variant="outline" size="sm" onclick={() => isModalOpen = false}>Batal</Button>
            <Button variant="primary" size="sm" onclick={submit} disabled={form.processing}>Simpan Tag</Button>
        {/snippet}
    </Modal>
</AppLayout>
