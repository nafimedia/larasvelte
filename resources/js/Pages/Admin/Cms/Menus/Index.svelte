<script lang="ts">
    import { useForm, router } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import Input from '@/Components/UI/Input.svelte';
    import Badge from '@/Components/UI/Badge.svelte';
    import { Menu as MenuIcon, Plus, Trash2, Link as LinkIcon, FileText, Newspaper, Folder, ExternalLink, Move } from 'lucide-svelte';

    interface Props {
        menus?: any[];
        availablePages?: any[];
        availablePosts?: any[];
        availableCategories?: any[];
    }

    let {
        menus = [],
        availablePages = [],
        availablePosts = [],
        availableCategories = []
    }: Props = $props();

    // svelte-ignore state_referenced_locally
    let selectedMenuId = $state<number>(menus[0]?.id || 0);

    const activeMenu = $derived(menus.find(m => m.id === selectedMenuId) || menus[0]);

    // Form for new menu
    const menuForm = useForm({
        name: '',
        location: 'navbar',
    });

    // Form for new item
    const itemForm = useForm({
        title: '',
        url: '',
        type: 'custom',
        target: '_self',
    });

    function submitNewMenu(e: Event) {
        e.preventDefault();
        menuForm.post('/admin/cms/menus', {
            onSuccess: () => menuForm.reset()
        });
    }

    function submitNewItem(e: Event) {
        e.preventDefault();
        if (!activeMenu) return;
        itemForm.post(`/admin/cms/menus/${activeMenu.id}/items`, {
            onSuccess: () => itemForm.reset()
        });
    }

    function addQuickLink(title: string, url: string, type: string) {
        if (!activeMenu) return;
        router.post(`/admin/cms/menus/${activeMenu.id}/items`, {
            title,
            url,
            type,
            target: '_self',
        });
    }

    function deleteItem(id: number) {
        if (confirm('Hapus item menu ini?')) {
            router.delete(`/admin/cms/menus/items/${id}`);
        }
    }
</script>

<AppLayout title="Manajemen Menu Navigasi (Menu Manager)">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <MenuIcon class="w-5 h-5 text-indigo-500" />
                <span>Manajemen Menu Navigasi</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Atur susunan Navbar, Footer, dan Sidebar menu secara visual</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Sidebar: Quick Link Sources & New Menu Form (4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Select or Create Menu -->
            <Card title="Pilih Struktur Menu">
                <div class="space-y-4 text-xs">
                    <div class="space-y-1.5">
                        <label for="menu-select" class="block font-semibold uppercase tracking-wider text-slate-400">Pilih Menu Aktif</label>
                        <select id="menu-select" bind:value={selectedMenuId} class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 font-bold">
                            {#each menus as m}
                                <option value={m.id}>{m.name} ({m.location.toUpperCase()})</option>
                            {/each}
                        </select>
                    </div>

                    <form onsubmit={submitNewMenu} class="pt-3 border-t border-slate-800 space-y-3">
                        <p class="font-bold text-slate-300">Buat Menu Baru:</p>
                        <Input placeholder="Nama Menu (Contoh: Footer Links)" bind:value={menuForm.name} required />
                        <select bind:value={menuForm.location} class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs">
                            <option value="navbar">Navbar Utama</option>
                            <option value="footer">Footer Bottom</option>
                            <option value="sidebar">Sidebar Side</option>
                        </select>
                        <Button variant="primary" size="sm" type="submit" class="w-full">
                            <Plus class="w-4 h-4 mr-1" />
                            <span>Buat Menu</span>
                        </Button>
                    </form>
                </div>
            </Card>

            <!-- Quick Add Pages -->
            <Card title="Tambah dari Halaman Web">
                <div class="space-y-2 max-h-44 overflow-y-auto pr-1 text-xs">
                    {#each availablePages as p}
                        <div class="flex items-center justify-between p-2 rounded-lg bg-slate-900 border border-slate-800">
                            <span class="font-semibold text-slate-300 truncate max-w-[170px]">{p.title}</span>
                            <button type="button" onclick={() => addQuickLink(p.title, `/${p.slug}`, 'page')} class="px-2 py-1 rounded bg-indigo-600/80 hover:bg-indigo-600 text-white text-[10px] font-bold">
                                + Tambah
                            </button>
                        </div>
                    {/each}
                </div>
            </Card>

            <!-- Quick Add Categories -->
            <Card title="Tambah dari Kategori Artikel">
                <div class="space-y-2 max-h-44 overflow-y-auto pr-1 text-xs">
                    {#each availableCategories as c}
                        <div class="flex items-center justify-between p-2 rounded-lg bg-slate-900 border border-slate-800">
                            <span class="font-semibold text-slate-300 truncate max-w-[170px]">{c.name}</span>
                            <button type="button" onclick={() => addQuickLink(c.name, `/blog?category=${c.slug}`, 'category')} class="px-2 py-1 rounded bg-indigo-600/80 hover:bg-indigo-600 text-white text-[10px] font-bold">
                                + Tambah
                            </button>
                        </div>
                    {/each}
                </div>
            </Card>
        </div>

        <!-- Right Main Column: Menu Structure Visual Editor (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            <Card title={`Struktur Item Menu: ${activeMenu?.name || ''}`} description="Tambahkan custom link atau hapus item dari daftar navigasi ini">

                <!-- Add Custom Item Form -->
                <form onsubmit={submitNewItem} class="p-4 rounded-xl bg-slate-900 border border-slate-800 grid grid-cols-1 sm:grid-cols-12 gap-3 mb-6">
                    <div class="sm:col-span-5">
                        <Input placeholder="Label Link (Contoh: Beranda)" bind:value={itemForm.title} required />
                    </div>
                    <div class="sm:col-span-5">
                        <Input placeholder="URL / Path (Contoh: /about-us)" bind:value={itemForm.url} required />
                    </div>
                    <div class="sm:col-span-2">
                        <Button variant="primary" size="md" type="submit" class="w-full h-full">
                            <Plus class="w-4 h-4 mr-1" />
                            <span>Tambah</span>
                        </Button>
                    </div>
                </form>

                <!-- Menu Items List -->
                <div class="space-y-2">
                    {#if !activeMenu?.items || activeMenu.items.length === 0}
                        <div class="p-8 text-center text-slate-500 text-xs">
                            Menu ini belum memiliki item link. Tambahkan item di atas atau dari panel samping.
                        </div>
                    {:else}
                        {#each activeMenu.items as item}
                            <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-between hover:border-slate-700 transition-all text-xs">
                                <div class="flex items-center gap-3">
                                    <Move class="w-4 h-4 text-slate-600 cursor-grab" />
                                    <div>
                                        <p class="font-bold text-white flex items-center gap-2">
                                            <span>{item.title}</span>
                                            <Badge variant="slate" class="text-[10px] font-mono">{item.type}</Badge>
                                        </p>
                                        <p class="text-slate-400 font-mono text-[11px]">{item.url}</p>
                                    </div>
                                </div>

                                <Button variant="ghost" size="icon" onclick={() => deleteItem(item.id)}>
                                    <Trash2 class="w-4 h-4 text-rose-400" />
                                </Button>
                            </div>
                        {/each}
                    {/if}
                </div>
            </Card>
        </div>
    </div>
</AppLayout>
