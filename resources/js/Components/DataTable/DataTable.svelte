<script lang="ts" generics="T">
    import { router } from '@inertiajs/svelte';
    import Button from '../UI/Button.svelte';
    import Input from '../UI/Input.svelte';
    import { Search, Download, ChevronLeft, ChevronRight, Filter } from 'lucide-svelte';
    import type { Snippet } from 'svelte';

    interface Props {
        searchable?: boolean;
        searchPlaceholder?: string;
        searchValue?: string;
        filters?: Record<string, any>;
        filterOptions?: { label: string; key: string; options: { label: string; value: string }[] }[];
        pagination?: {
            current_page: number;
            last_page: number;
            total: number;
            from: number;
            to: number;
            per_page: number;
            links: { url: string | null; label: string; active: boolean }[];
        };
        exportFilename?: string;
        exportData?: any[];
        header?: Snippet;
        children?: Snippet;
        routePath?: string;
    }

    let {
        searchable = true,
        searchPlaceholder = 'Cari data...',
        searchValue = '',
        filters = {},
        filterOptions = [],
        pagination,
        exportFilename = 'export-data.csv',
        exportData = [],
        header,
        children,
        routePath = window.location.pathname
    }: Props = $props();

    let searchInput = $state('');

    $effect(() => {
        searchInput = searchValue;
    });

    function applySearch() {
        router.get(routePath, { ...filters, search: searchInput, page: 1 }, { preserveState: true, replace: true });
    }

    function handleFilterChange(key: string, value: string) {
        router.get(routePath, { ...filters, [key]: value, page: 1 }, { preserveState: true, replace: true });
    }

    function changePage(url: string | null) {
        if (url) {
            router.get(url, {}, { preserveState: true });
        }
    }

    function exportToCsv() {
        if (!exportData || exportData.length === 0) return;
        const keys = Object.keys(exportData[0]);
        const csvRows = [
            keys.join(','),
            ...exportData.map(row => keys.map(k => `"${String(row[k] ?? '').replace(/"/g, '""')}"`).join(','))
        ];
        const blob = new Blob([csvRows.join('\n')], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.setAttribute('href', url);
        a.setAttribute('download', exportFilename);
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
</script>

<div class="space-y-4">
    <!-- Toolbar: Search, Filters, Export -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            {#if searchable}
                <form onsubmit={(e) => { e.preventDefault(); applySearch(); }} class="relative w-full sm:w-64">
                    <Input
                        placeholder={searchPlaceholder}
                        bind:value={searchInput}
                        class="pl-9 pr-4 text-xs"
                    />
                    <Search class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
                </form>
            {/if}

            {#each filterOptions as opt}
                <div class="flex items-center gap-1.5">
                    <Filter class="w-3.5 h-3.5 text-slate-400" />
                    <select
                        class="text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-lg px-3 py-2 text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                        value={filters[opt.key] ?? ''}
                        onchange={(e) => handleFilterChange(opt.key, (e.target as HTMLSelectElement).value)}
                    >
                        <option value="">Semua {opt.label}</option>
                        {#each opt.options as o}
                            <option value={o.value}>{o.label}</option>
                        {/each}
                    </select>
                </div>
            {/each}
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            {#if exportData.length > 0}
                <Button variant="outline" size="sm" onclick={exportToCsv}>
                    <Download class="w-4 h-4" />
                    <span>Ekspor CSV</span>
                </Button>
            {/if}
        </div>
    </div>

    <!-- Table Wrapper -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-x-auto shadow-xs">
        <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
            {#if header}
                <thead class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                    {@render header()}
                </thead>
            {/if}
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                {#if children}
                    {@render children()}
                {/if}
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    {#if pagination && pagination.last_page > 1}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-2">
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Menampilkan <span class="font-medium text-slate-900 dark:text-slate-100">{pagination.from ?? 0}</span> sampai <span class="font-medium text-slate-900 dark:text-slate-100">{pagination.to ?? 0}</span> dari <span class="font-medium text-slate-900 dark:text-slate-100">{pagination.total}</span> data
            </p>

            <div class="flex items-center gap-1.5">
                {#each pagination.links as link}
                    {#if link.label.includes('Previous')}
                        <Button
                            variant="outline"
                            size="sm"
                            disabled={!link.url}
                            onclick={() => changePage(link.url)}
                        >
                            <ChevronLeft class="w-4 h-4" />
                        </Button>
                    {:else if link.label.includes('Next')}
                        <Button
                            variant="outline"
                            size="sm"
                            disabled={!link.url}
                            onclick={() => changePage(link.url)}
                        >
                            <ChevronRight class="w-4 h-4" />
                        </Button>
                    {:else}
                        <button
                            type="button"
                            class={`px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors cursor-pointer ${
                                link.active
                                    ? 'bg-indigo-600 text-white dark:bg-indigo-500'
                                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'
                            }`}
                            onclick={() => changePage(link.url)}
                        >
                            {@html link.label}
                        </button>
                    {/if}
                {/each}
            </div>
        </div>
    {/if}
</div>
