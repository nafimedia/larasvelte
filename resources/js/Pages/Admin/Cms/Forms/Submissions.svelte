<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import { Inbox, ArrowLeft, Clock, User, Mail, Globe } from 'lucide-svelte';

    interface Props {
        form?: any;
        submissions?: any[];
    }

    let { form = {}, submissions = [] }: Props = $props();
</script>

<AppLayout title={`Inbox Pesan: ${form.name}`}>
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <Link href="/admin/cms/forms" class="p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white transition-all">
                <ArrowLeft class="w-4 h-4" />
            </Link>
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    <Inbox class="w-5 h-5 text-indigo-500" />
                    <span>Inbox Submissions: {form.name}</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar tanggapan dan pesan masuk dari pengunjung</p>
            </div>
        </div>
    </div>

    <Card>
        {#if submissions.length === 0}
            <div class="p-12 text-center text-slate-500 space-y-2">
                <Inbox class="w-10 h-10 mx-auto text-slate-600" />
                <p class="text-xs">Belum ada tanggapan atau pesan masuk untuk formulir ini.</p>
            </div>
        {:else}
            <div class="space-y-4">
                {#each submissions as sub}
                    <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-2 text-xs font-mono text-slate-400">
                            <span class="flex items-center gap-1.5 text-indigo-300">
                                <Clock class="w-3.5 h-3.5" />
                                {new Date(sub.created_at).toLocaleString('id-ID')}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <Globe class="w-3.5 h-3.5" />
                                IP: {sub.ip_address || '—'}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                            {#each Object.entries(sub.data || {}) as [key, val]}
                                <div class="p-2.5 rounded-lg bg-slate-950 border border-slate-800/80">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase font-mono">{key}</span>
                                    <span class="text-slate-200 font-medium">{val}</span>
                                </div>
                            {/each}
                        </div>
                    </div>
                {/each}
            </div>
        {/if}
    </Card>
</AppLayout>
