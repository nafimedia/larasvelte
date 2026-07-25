<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import { toast } from 'svelte-sonner';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import Input from '@/Components/UI/Input.svelte';
    import Badge from '@/Components/UI/Badge.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import {
        Layers,
        Search,
        CheckCircle2,
        XCircle,
        ShieldAlert,
        Newspaper,
        FileText,
        Folder,
        MessageSquare,
        Calendar,
        Sparkles,
        Menu as MenuIcon,
        FileSpreadsheet,
        Image as ImageIcon,
        ArrowRightLeft,
        BarChart3,
        Activity,
        Users,
        ShieldCheck,
        Settings,
        Lock,
        RefreshCw
    } from 'lucide-svelte';
    import type { ModuleItem } from '@/lib/types';

    interface Props {
        modules: ModuleItem[];
    }

    let { modules = [] }: Props = $props();

    let searchQuery = $state('');
    let selectedGroup = $state<string>('all');
    let togglingKey = $state<string | null>(null);

    const iconMap: Record<string, any> = {
        Newspaper,
        FileText,
        Folder,
        MessageSquare,
        Calendar,
        Sparkles,
        Menu: MenuIcon,
        FileSpreadsheet,
        Image: ImageIcon,
        ArrowRightLeft,
        BarChart3,
        Activity,
        Users,
        ShieldCheck,
        Settings,
        Layers,
    };

    const groupLabels: Record<string, string> = {
        content: 'Content Management',
        builder: 'Website Builder',
        seo: 'SEO & Analytics',
        media: 'Media',
        system: 'System Core',
    };

    const filteredModules = $derived(
        modules.filter(mod => {
            const matchesSearch = mod.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
                (mod.description && mod.description.toLowerCase().includes(searchQuery.toLowerCase()));
            const matchesGroup = selectedGroup === 'all' || mod.group === selectedGroup;
            return matchesSearch && matchesGroup;
        })
    );

    const activeCount = $derived(modules.filter(m => m.is_active).length);
    const disabledCount = $derived(modules.filter(m => !m.is_active).length);

    function toggleModule(mod: ModuleItem) {
        if (mod.is_system) {
            toast.error('Modul utama (System Core) tidak dapat dinonaktifkan.');
            return;
        }

        togglingKey = mod.key;

        router.patch(`/admin/settings/modules/${mod.key}/toggle`, {}, {
            preserveScroll: true,
            onSuccess: () => {
                togglingKey = null;
            },
            onError: (err) => {
                togglingKey = null;
                toast.error(Object.values(err)[0] || 'Gagal mengubah status modul.');
            }
        });
    }
</script>

<AppLayout title="Manajemen Modul System">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <Layers class="w-5 h-5 text-indigo-500" />
                <span>Module Management System</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Aktifkan atau nonaktifkan modul CMS sesuai kebutuhan arsitektur website Anda
            </p>
        </div>

        <!-- Summary Stats Badges -->
        <div class="flex items-center gap-2 text-xs font-semibold">
            <div class="px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                Total: {modules.length} Modul
            </div>
            <div class="px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                {activeCount} Aktif
            </div>
            {#if disabledCount > 0}
                <div class="px-3 py-1.5 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                    {disabledCount} Nonaktif
                </div>
            {/if}
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
        <!-- Search Input -->
        <div class="relative w-full sm:w-80">
            <Search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
            <input
                type="text"
                placeholder="Cari nama atau deskripsi modul..."
                bind:value={searchQuery}
                class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
            />
        </div>

        <!-- Group Filter Tabs -->
        <div class="flex items-center gap-1 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0">
            <button
                type="button"
                onclick={() => selectedGroup = 'all'}
                class={`px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors cursor-pointer shrink-0 ${
                    selectedGroup === 'all'
                        ? 'bg-indigo-600 text-white shadow-xs'
                        : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'
                }`}
            >
                Semua ({modules.length})
            </button>

            {#each Object.entries(groupLabels) as [gKey, gLabel]}
                <button
                    type="button"
                    onclick={() => selectedGroup = gKey}
                    class={`px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors cursor-pointer shrink-0 ${
                        selectedGroup === gKey
                            ? 'bg-indigo-600 text-white shadow-xs'
                            : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'
                    }`}
                >
                    {gLabel} ({modules.filter(m => m.group === gKey).length})
                </button>
            {/each}
        </div>
    </div>

    <!-- Modules Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {#each filteredModules as mod (mod.id)}
            {@const IconComponent = iconMap[mod.icon || 'Layers'] || Layers}
            <div class={`p-5 rounded-2xl border transition-all flex flex-col justify-between space-y-4 ${
                mod.is_active
                    ? 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 shadow-sm'
                    : 'bg-slate-50/60 dark:bg-slate-950/40 border-slate-200/60 dark:border-slate-850 opacity-75'
            }`}>
                <!-- Header: Icon & Group Badge -->
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class={`p-3 rounded-xl shrink-0 ${
                            mod.is_active
                                ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400'
                                : 'bg-slate-200/60 dark:bg-slate-800 text-slate-400'
                        }`}>
                            <IconComponent class="w-6 h-6" />
                        </div>

                        <div class="flex items-center gap-1.5">
                            {#if mod.is_system}
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900">
                                    <Lock class="w-3 h-3" />
                                    <span>System Core</span>
                                </span>
                            {:else if mod.is_active}
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">
                                    <CheckCircle2 class="w-3 h-3" />
                                    <span>Aktif</span>
                                </span>
                            {:else}
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900">
                                    <XCircle class="w-3 h-3" />
                                    <span>Nonaktif</span>
                                </span>
                            {/if}
                        </div>
                    </div>

                    <!-- Title & Description -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">{mod.name}</h3>
                        <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">
                            {groupLabels[mod.group] || mod.group}
                        </span>
                        {#if mod.description}
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed line-clamp-2">
                                {mod.description}
                            </p>
                        {/if}
                    </div>
                </div>

                <!-- Footer: Action Toggle Switch -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <span class="text-[11px] font-medium text-slate-400">
                        Status Modul
                    </span>

                    {#if mod.is_system}
                        <span class="text-[11px] font-medium text-slate-400 italic">
                            Tidak dapat diubah
                        </span>
                    {:else}
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                checked={mod.is_active}
                                disabled={togglingKey === mod.key}
                                onchange={() => toggleModule(mod)}
                                class="sr-only peer"
                            />
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-800 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    {/if}
                </div>
            </div>
        {:else}
            <div class="col-span-full py-12 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-2">
                <ShieldAlert class="w-10 h-10 text-slate-400 mx-auto stroke-1" />
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Tidak ada modul ditemukan</p>
                <p class="text-xs text-slate-400">Coba ubah kata kunci pencarian atau filter kategori modul</p>
            </div>
        {/each}
    </div>
</AppLayout>
