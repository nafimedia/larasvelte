<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import DataTable from '@/Components/DataTable/DataTable.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import Modal from '@/Components/UI/Modal.svelte';
    import Dialog from '@/Components/UI/Dialog.svelte';
    import Badge from '@/Components/UI/Badge.svelte';
    import Avatar from '@/Components/UI/Avatar.svelte';
    import { Activity, Trash2, Eye, FileText } from 'lucide-svelte';
    import type { ActivityItem, PaginatedData } from '@/lib/types';

    interface Props {
        activities: PaginatedData<ActivityItem>;
        logNames: string[];
        filters: { search: string; log_name: string; per_page: number };
    }

    let { activities, logNames = [], filters }: Props = $props();

    let isDetailModalOpen = $state(false);
    let isClearDialogOpen = $state(false);
    let selectedActivity = $state<ActivityItem | null>(null);

    function showDetail(act: ActivityItem) {
        selectedActivity = act;
        isDetailModalOpen = true;
    }

    function executeClearLogs() {
        router.delete('/admin/activity-logs/clear', { preserveScroll: true });
    }

    const filterOptions = $derived([
        {
            label: 'Kategori Log',
            key: 'log_name',
            options: logNames.map(name => ({ label: name, value: name }))
        }
    ]);
</script>

<AppLayout title="Audit Log Aktivitas System">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Audit Log Aktivitas</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Riwayat jejak audit tindakan pengguna dan aktivitas sistem (Spatie Activitylog)</p>
        </div>
        <Button variant="danger" size="md" onclick={() => isClearDialogOpen = true}>
            <Trash2 class="w-4 h-4" />
            <span>Bersihkan Log</span>
        </Button>
    </div>

    <!-- Activity Data Table -->
    <DataTable
        searchable={true}
        searchPlaceholder="Cari deskripsi atau pelaku..."
        searchValue={filters.search}
        filters={filters}
        filterOptions={filterOptions}
        pagination={activities}
        exportFilename="audit-log-aktivitas.csv"
        exportData={activities.data}
        routePath="/admin/activity-logs"
    >
        {#snippet header()}
            <tr>
                <th class="px-4 py-3">Pelaku (Causer)</th>
                <th class="px-4 py-3">Kategori</th>
                <th class="px-4 py-3">Aktivitas / Deskripsi</th>
                <th class="px-4 py-3">Waktu Event</th>
                <th class="px-4 py-3 text-right">Detail</th>
            </tr>
        {/snippet}

        {#each activities.data as act}
            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2.5">
                        <Avatar src={act.causer_avatar} alt={act.causer_name} size="sm" />
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-slate-100 text-xs">{act.causer_name}</p>
                            {#if act.causer_email}
                                <p class="text-[10px] text-slate-400">{act.causer_email}</p>
                            {/if}
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <Badge variant="info">{act.log_name}</Badge>
                </td>
                <td class="px-4 py-3 text-xs text-slate-700 dark:text-slate-300 font-medium">
                    {act.description}
                </td>
                <td class="px-4 py-3 text-xs text-slate-500 font-mono">
                    {act.created_at_human}
                </td>
                <td class="px-4 py-3 text-right">
                    <Button variant="ghost" size="icon" onclick={() => showDetail(act)}>
                        <Eye class="w-4 h-4 text-slate-500" />
                    </Button>
                </td>
            </tr>
        {/each}
    </DataTable>

    <!-- Detail Modal -->
    <Modal bind:open={isDetailModalOpen} title="Detail Activity Log" description={`Event #${selectedActivity?.id}`} maxWidth="lg">
        {#if selectedActivity}
            <div class="space-y-4 text-xs">
                <div class="grid grid-cols-2 gap-4 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                    <div>
                        <span class="text-slate-400 block font-semibold uppercase text-[10px]">Pelaku</span>
                        <span class="font-bold text-slate-900 dark:text-slate-100">{selectedActivity.causer_name}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold uppercase text-[10px]">Waktu</span>
                        <span class="font-mono">{selectedActivity.created_at}</span>
                    </div>
                </div>

                <div>
                    <span class="text-slate-400 block font-semibold uppercase text-[10px] mb-1">Deskripsi</span>
                    <p class="p-3 border border-slate-200 dark:border-slate-800 rounded-xl font-medium">{selectedActivity.description}</p>
                </div>

                {#if selectedActivity.properties && Object.keys(selectedActivity.properties).length > 0}
                    <div>
                        <span class="text-slate-400 block font-semibold uppercase text-[10px] mb-1">Properties (Payload JSON)</span>
                        <pre class="p-3 bg-slate-900 text-emerald-400 rounded-xl font-mono text-[11px] overflow-x-auto">{JSON.stringify(selectedActivity.properties, null, 2)}</pre>
                    </div>
                {/if}
            </div>
        {/if}
    </Modal>

    <!-- Clear Log Dialog -->
    <Dialog
        bind:open={isClearDialogOpen}
        title="Pembersihan Riwayat Log"
        message="Apakah Anda yakin ingin menghapus seluruh log aktivitas sistem? Data yang dihapus tidak dapat dipulihkan."
        type="danger"
        confirmText="Bersihkan Sekarang"
        onconfirm={executeClearLogs}
    />
</AppLayout>
