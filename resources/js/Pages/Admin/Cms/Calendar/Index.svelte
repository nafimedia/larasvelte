<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import Badge from '@/Components/UI/Badge.svelte';
    import { Calendar as CalendarIcon, Clock, Edit3, Newspaper, CheckCircle, ExternalLink } from 'lucide-svelte';

    interface Props {
        scheduledPosts?: any[];
    }

    let { scheduledPosts = [] }: Props = $props();
</script>

<AppLayout title="Kalender Publikasi Artikel (Publishing Calendar)">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <CalendarIcon class="w-5 h-5 text-indigo-500" />
                <span>Kalender Jadwal Publikasi Content</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola linimasa penerbitan artikel terjadwal (*Scheduled Posts*) dan histori publikasi</p>
        </div>
    </div>

    <!-- Timeline List Cards -->
    <div class="space-y-4">
        {#if scheduledPosts.length === 0}
            <div class="p-12 text-center rounded-2xl bg-slate-900 border border-slate-800 space-y-2">
                <CalendarIcon class="w-10 h-10 mx-auto text-slate-600" />
                <h3 class="text-sm font-bold text-white">Belum Ada Artikel Terjadwal</h3>
                <p class="text-xs text-slate-400">Gunakan pilihan status "Scheduled" dan atur jam pada tanggal terbit untuk menjadwalkan artikel.</p>
            </div>
        {:else}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {#each scheduledPosts as post}
                    <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 flex flex-col justify-between hover:border-indigo-500/40 transition-all">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <Badge variant={
                                    post.status === 'scheduled' ? 'slate' : 'success'
                                }>
                                    {post.status.toUpperCase()}
                                </Badge>
                                <span class="text-xs font-mono text-amber-400 font-bold flex items-center gap-1">
                                    <Clock class="w-3.5 h-3.5" />
                                    {post.published_at ? new Date(post.published_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) : '—'}
                                </span>
                            </div>

                            {#if post.featured_image}
                                <img src={post.featured_image} alt={post.title} class="w-full h-36 object-cover rounded-xl border border-slate-800" />
                            {/if}

                            <h3 class="font-bold text-sm text-white line-clamp-2">{post.title}</h3>
                            <p class="text-xs text-slate-400 line-clamp-2">{post.summary || 'Tidak ada ringkasan'}</p>
                        </div>

                        <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                            <span class="text-slate-400 font-mono">{post.author?.name || 'Admin'}</span>
                            <div class="flex items-center gap-2">
                                <Link href={`/admin/cms/posts/${post.id}/edit`} class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-indigo-400 transition-colors" title="Edit Artikel">
                                    <Edit3 class="w-4 h-4" />
                                </Link>
                            </div>
                        </div>
                    </div>
                {/each}
            </div>
        {/if}
    </div>
</AppLayout>
