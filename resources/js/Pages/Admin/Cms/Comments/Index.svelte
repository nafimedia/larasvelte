<script lang="ts">
    import { useForm, router } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import DataTable from '@/Components/DataTable/DataTable.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import Badge from '@/Components/UI/Badge.svelte';
    import Modal from '@/Components/UI/Modal.svelte';
    import Input from '@/Components/UI/Input.svelte';
    import { MessageSquare, CheckCircle, ShieldAlert, Trash2, Reply, CornerDownRight } from 'lucide-svelte';

    interface Props {
        comments: any;
        filters: any;
    }

    let { comments, filters = {} }: Props = $props();

    let replyComment = $state<any>(null);
    let isReplyModalOpen = $state(false);

    // svelte-ignore state_referenced_locally
    const replyForm = useForm({
        content: '',
    });

    function updateStatus(id: number, status: string) {
        router.patch(`/admin/cms/comments/${id}/status`, { status });
    }

    function openReplyModal(comment: any) {
        replyComment = comment;
        replyForm.reset();
        isReplyModalOpen = true;
    }

    function submitReply(e: Event) {
        e.preventDefault();
        if (!replyComment) return;
        replyForm.post(`/admin/cms/comments/${replyComment.id}/reply`, {
            onSuccess: () => {
                isReplyModalOpen = false;
                replyForm.reset();
            }
        });
    }

    function deletePermanently(id: number) {
        if (confirm('Hapus komentar ini secara permanen?')) {
            router.delete(`/admin/cms/comments/${id}`);
        }
    }

    const filterOptions = $derived([
        {
            label: 'Status',
            key: 'status',
            options: [
                { label: 'Semua Status', value: '' },
                { label: 'Approved (Disetujui)', value: 'approved' },
                { label: 'Pending (Menunggu)', value: 'pending' },
                { label: 'Spam', value: 'spam' },
                { label: 'Trash (Sampah)', value: 'trash' },
            ]
        }
    ]);
</script>

<AppLayout title="Moderasi Komentar Artikel">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <MessageSquare class="w-5 h-5 text-indigo-500" />
                <span>Moderasi Komentar Pengunjung</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola antrean persetujuan, deteksi spam, dan balasan bertingkat (*Threaded Replies*)</p>
        </div>
    </div>

    <!-- Comments Data Table -->
    <DataTable
        searchable={true}
        searchPlaceholder="Cari isi komentar atau nama penulis..."
        searchValue={filters.search}
        filters={filters}
        filterOptions={filterOptions}
        pagination={comments}
        routePath="/admin/cms/comments"
    >
        {#snippet header()}
            <tr>
                <th class="px-4 py-3">Penulis Komentar</th>
                <th class="px-4 py-3">Isi Komentar</th>
                <th class="px-4 py-3">Pada Artikel</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
        {/snippet}

        {#each (comments.data || []) as comment}
            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                <td class="px-4 py-3">
                    <div class="space-y-0.5">
                        <p class="font-bold text-slate-900 dark:text-slate-100">{comment.author_name || comment.user?.name || 'Anonim'}</p>
                        <p class="text-[10px] text-slate-400 font-mono">{comment.author_email || comment.user?.email || '-'}</p>
                    </div>
                </td>
                <td class="px-4 py-3 text-xs max-w-sm">
                    <p class="text-slate-300 line-clamp-2">"{comment.content}"</p>
                    {#if comment.replies && comment.replies.length > 0}
                        <div class="mt-1 flex items-center gap-1 text-[10px] text-indigo-400">
                            <CornerDownRight class="w-3 h-3" />
                            <span>{comment.replies.length} Balasan</span>
                        </div>
                    {/if}
                </td>
                <td class="px-4 py-3 text-xs">
                    {#if comment.post}
                        <p class="font-semibold text-slate-300 truncate max-w-xs">{comment.post.title}</p>
                    {:else}
                        <span class="text-slate-500">—</span>
                    {/if}
                </td>
                <td class="px-4 py-3">
                    <Badge variant={
                        comment.status === 'approved' ? 'success' :
                        comment.status === 'pending' ? 'slate' : 'danger'
                    }>
                        {comment.status.toUpperCase()}
                    </Badge>
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-1">
                        {#if comment.status !== 'approved'}
                            <Button variant="ghost" size="icon" onclick={() => updateStatus(comment.id, 'approved')} title="Setujui (Approve)">
                                <CheckCircle class="w-4 h-4 text-emerald-400" />
                            </Button>
                        {/if}
                        {#if comment.status !== 'spam'}
                            <Button variant="ghost" size="icon" onclick={() => updateStatus(comment.id, 'spam')} title="Tandai Spam">
                                <ShieldAlert class="w-4 h-4 text-amber-400" />
                            </Button>
                        {/if}
                        <Button variant="ghost" size="icon" onclick={() => openReplyModal(comment)} title="Balas Komentar">
                            <Reply class="w-4 h-4 text-indigo-400" />
                        </Button>
                        <Button variant="ghost" size="icon" onclick={() => deletePermanently(comment.id)} title="Hapus Permanen">
                            <Trash2 class="w-4 h-4 text-rose-400" />
                        </Button>
                    </div>
                </td>
            </tr>
        {/each}
    </DataTable>

    <!-- Reply Modal -->
    <Modal bind:open={isReplyModalOpen} title={`Balas Komentar ${replyComment?.author_name || ''}`} description="Tulis balasan langsung pada thread komentar ini">
        <form onsubmit={submitReply} class="space-y-4">
            <div class="p-3 bg-slate-900 rounded-xl border border-slate-800 text-xs text-slate-400 italic">
                "{replyComment?.content}"
            </div>

            <div class="space-y-1.5">
                <label for="reply-text" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Isi Balasan Anda</label>
                <textarea
                    id="reply-text"
                    rows="4"
                    class="w-full px-3.5 py-2 text-sm rounded-lg border bg-slate-950 border-slate-800 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                    placeholder="Tulis balasan komentar di sini..."
                    bind:value={replyForm.content}
                    required
                ></textarea>
            </div>
        </form>

        {#snippet footer()}
            <Button variant="outline" size="sm" onclick={() => isReplyModalOpen = false}>Batal</Button>
            <Button variant="primary" size="sm" onclick={submitReply} disabled={replyForm.processing}>Kirim Balasan</Button>
        {/snippet}
    </Modal>
</AppLayout>
