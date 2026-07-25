<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import Modal from '@/Components/UI/Modal.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import { History, RotateCcw, Clock, User, Eye, FileText } from 'lucide-svelte';

    interface Props {
        open?: boolean;
        postId?: number;
        revisions?: any[];
        onClose?: () => void;
    }

    let { open = $bindable(false), postId, revisions = [], onClose }: Props = $props();

    let selectedRevision = $state<any>(null);

    function restore(revisionId: number) {
        if (!postId) return;
        if (confirm('Pulihkan konten artikel ke versi revisi ini? Sesi saat ini akan ditimpa.')) {
            router.post(`/admin/cms/posts/${postId}/revisions/${revisionId}/restore`, {}, {
                onSuccess: () => open = false
            });
        }
    }
</script>

<Modal bind:open title="Riwayat Versi (Post Revisions)" description="Lihat versi perubahan terdahulu dan pulihkan jika diperlukan">
    <div class="space-y-4">
        {#if revisions.length === 0}
            <div class="p-8 text-center text-slate-500 space-y-2">
                <History class="w-8 h-8 mx-auto text-slate-600" />
                <p class="text-xs">Belum ada riwayat revisi tersimpan untuk artikel ini.</p>
            </div>
        {:else}
            <div class="space-y-3 max-h-[350px] overflow-y-auto pr-1">
                {#each revisions as rev}
                    <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 font-mono text-slate-400">
                                <Clock class="w-3.5 h-3.5 text-indigo-400" />
                                <span>{new Date(rev.created_at).toLocaleString('id-ID')}</span>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-950 text-indigo-300 border border-slate-800 font-mono font-bold">
                                Versi #{rev.id}
                            </span>
                        </div>

                        <p class="font-bold text-white line-clamp-1">{rev.title}</p>

                        <div class="flex items-center justify-between pt-1">
                            <div class="flex items-center gap-1.5 text-slate-400 text-[11px]">
                                <User class="w-3 h-3 text-slate-500" />
                                <span>{rev.user?.name || 'Admin'}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    onclick={() => selectedRevision = selectedRevision?.id === rev.id ? null : rev}
                                    class="text-indigo-400 hover:text-indigo-300 font-semibold text-[11px] underline"
                                >
                                    {selectedRevision?.id === rev.id ? 'Sembunyikan' : 'Lihat Ringkasan'}
                                </button>

                                <Button variant="outline" size="sm" onclick={() => restore(rev.id)}>
                                    <RotateCcw class="w-3 h-3 mr-1 text-emerald-400" />
                                    <span>Restore</span>
                                </Button>
                            </div>
                        </div>

                        {#if selectedRevision?.id === rev.id}
                            <div class="mt-2 p-3 rounded-lg bg-slate-950 border border-slate-800/80 text-[11px] space-y-1">
                                <p class="text-slate-400 font-mono">Judul: {rev.title}</p>
                                <p class="text-slate-300 italic">"{rev.summary || 'Tidak ada ringkasan'}"</p>
                            </div>
                        {/if}
                    </div>
                {/each}
            </div>
        {/if}
    </div>

    {#snippet footer()}
        <Button variant="outline" size="sm" onclick={() => open = false}>Tutup</Button>
    {/snippet}
</Modal>
