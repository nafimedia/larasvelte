<script lang="ts">
    import { useForm, router } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import Input from '@/Components/UI/Input.svelte';
    import Badge from '@/Components/UI/Badge.svelte';
    import Modal from '@/Components/UI/Modal.svelte';
    import { ArrowRightLeft, Plus, Trash2, ArrowRight, Activity } from 'lucide-svelte';

    interface Props {
        redirectRules?: any[];
    }

    let { redirectRules = [] }: Props = $props();

    let isCreateOpen = $state(false);

    const form = useForm({
        old_url: '',
        new_url: '',
        status_code: '301',
    });

    function submitRedirect(e: Event) {
        e.preventDefault();
        form.post('/admin/cms/redirects', {
            onSuccess: () => {
                isCreateOpen = false;
                form.reset();
            }
        });
    }

    function deleteRedirect(id: number) {
        if (confirm('Hapus aturan pengalihan URL ini?')) {
            router.delete(`/admin/cms/redirects/${id}`);
        }
    }
</script>

<AppLayout title="Redirect Manager (301/302)">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <ArrowRightLeft class="w-5 h-5 text-indigo-500" />
                <span>Redirect Manager 301 & 302</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola pengalihan URL lama ke URL baru untuk menjaga reputasi SEO</p>
        </div>

        <Button variant="primary" size="md" onclick={() => isCreateOpen = true}>
            <Plus class="w-4 h-4 mr-1.5" />
            <span>Tambah Redirect</span>
        </Button>
    </div>

    <!-- Redirects Table -->
    <Card class="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-900/50 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        <th class="px-4 py-3">Old Path / URL Lama</th>
                        <th class="px-4 py-3">Destination / URL Baru</th>
                        <th class="px-4 py-3 text-center">Status Code</th>
                        <th class="px-4 py-3 text-center">Hit Count</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80 text-xs">
                    {#if redirectRules.length === 0}
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                Belum ada aturan pengalihan URL yang didaftarkan.
                            </td>
                        </tr>
                    {:else}
                        {#each redirectRules as r}
                            <tr class="hover:bg-slate-900/40 transition-colors">
                                <td class="px-4 py-3 font-mono text-indigo-300 font-bold">
                                    {r.old_url}
                                </td>
                                <td class="px-4 py-3 font-mono text-emerald-400">
                                    {r.new_url}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <Badge variant={r.status_code === 301 ? 'success' : 'slate'}>
                                        {r.status_code} {r.status_code === 301 ? 'Permanent' : 'Temporary'}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3 text-center font-mono text-amber-400 font-bold">
                                    {r.hits || 0} clicks
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <Button variant="ghost" size="icon" onclick={() => deleteRedirect(r.id)}>
                                        <Trash2 class="w-4 h-4 text-rose-400" />
                                    </Button>
                                </td>
                            </tr>
                        {/each}
                    {/if}
                </tbody>
            </table>
        </div>
    </Card>

    <!-- Create Redirect Modal -->
    <Modal bind:open={isCreateOpen} title="Tambah Pengalihan URL Baru" description="Isi URL lama dan URL tujuan baru">
        <form onsubmit={submitRedirect} class="space-y-4">
            <Input label="Path URL Lama (Old URL)" placeholder="/artikel-lama" bind:value={form.old_url} required />
            <Input label="Path / URL Tujuan Baru (New URL)" placeholder="/blog/artikel-baru-2026" bind:value={form.new_url} required />

            <div class="space-y-1.5 text-xs">
                <label for="status-code" class="block font-semibold uppercase tracking-wider text-slate-400">Jenis Redirect (HTTP Status Code)</label>
                <select id="status-code" bind:value={form.status_code} class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 font-mono">
                    <option value="301">301 Moved Permanently (Direkomendasikan SEO)</option>
                    <option value="302">302 Found (Pengalihan Sementara)</option>
                </select>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <Button variant="outline" size="sm" onclick={() => isCreateOpen = false}>Batal</Button>
                <Button variant="primary" size="sm" type="submit" disabled={form.processing}>Simpan Redirect</Button>
            </div>
        </form>
    </Modal>
</AppLayout>
