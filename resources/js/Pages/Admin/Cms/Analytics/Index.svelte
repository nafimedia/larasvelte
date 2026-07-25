<script lang="ts">
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import { BarChart3, Eye, Newspaper, FileText, MessageSquare, Folder, TrendingUp, Sparkles } from 'lucide-svelte';

    interface Props {
        metrics: any;
        topPosts: any[];
        topPages: any[];
        recentComments: any[];
    }

    let { metrics = {}, topPosts = [], topPages = [], recentComments = [] }: Props = $props();
</script>

<AppLayout title="Analitik & Metrik CMS">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <BarChart3 class="w-5 h-5 text-indigo-500" />
                <span>Analitik & Performa CMS</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Statistik jumlah tayangan artikel, halaman terpopuler, dan aktivitas konten</p>
        </div>
    </div>

    <!-- Top Summary Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-2">
            <div class="flex items-center justify-between text-indigo-400">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Artikel</span>
                <Newspaper class="w-5 h-5" />
            </div>
            <p class="text-3xl font-extrabold text-white font-mono">{metrics.totalPosts || 0}</p>
            <p class="text-[10px] text-slate-500">Artikel Blog & Berita</p>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-2">
            <div class="flex items-center justify-between text-purple-400">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Halaman</span>
                <FileText class="w-5 h-5" />
            </div>
            <p class="text-3xl font-extrabold text-white font-mono">{metrics.totalPages || 0}</p>
            <p class="text-[10px] text-slate-500">Halaman Website Status Live</p>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-2">
            <div class="flex items-center justify-between text-cyan-400">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Komentar</span>
                <MessageSquare class="w-5 h-5" />
            </div>
            <p class="text-3xl font-extrabold text-white font-mono">{metrics.totalComments || 0}</p>
            <p class="text-[10px] text-slate-500">Komentar Masuk</p>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-2">
            <div class="flex items-center justify-between text-emerald-400">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Kategori Publik</span>
                <Folder class="w-5 h-5" />
            </div>
            <p class="text-3xl font-extrabold text-white font-mono">{metrics.totalCategories || 0}</p>
            <p class="text-[10px] text-slate-500">Taksonomi Kategori</p>
        </div>
    </div>

    <!-- Analytics Breakdown Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Top Posts -->
        <Card title="Artikel Blog Terpopuler" description="Berdasarkan jumlah tayangan (Total Views)">
            <div class="space-y-3 pt-2">
                {#if topPosts.length === 0}
                    <p class="text-xs text-slate-500 italic">Belum ada data tayangan artikel.</p>
                {:else}
                    {#each topPosts as post, i}
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800/80 flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-3 truncate">
                                <span class="w-6 h-6 rounded-full bg-indigo-600/20 text-indigo-400 flex items-center justify-center font-bold text-xs shrink-0">
                                    #{i + 1}
                                </span>
                                <div class="truncate">
                                    <p class="font-bold text-white truncate">{post.title}</p>
                                    <p class="text-[10px] text-slate-400 font-mono">/{post.slug}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 font-mono font-bold text-cyan-400 shrink-0">
                                <Eye class="w-3.5 h-3.5" />
                                <span>{post.view_count || 0} views</span>
                            </div>
                        </div>
                    {/each}
                {/if}
            </div>
        </Card>

        <!-- Top Pages -->
        <Card title="Halaman Website Terpopuler" description="Halaman paling sering dikunjungi pengunjung">
            <div class="space-y-3 pt-2">
                {#if topPages.length === 0}
                    <p class="text-xs text-slate-500 italic">Belum ada data tayangan halaman.</p>
                {:else}
                    {#each topPages as pageItem, i}
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800/80 flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-3 truncate">
                                <span class="w-6 h-6 rounded-full bg-purple-600/20 text-purple-400 flex items-center justify-center font-bold text-xs shrink-0">
                                    #{i + 1}
                                </span>
                                <div class="truncate">
                                    <p class="font-bold text-white truncate">{pageItem.title}</p>
                                    <p class="text-[10px] text-slate-400 font-mono">/{pageItem.slug}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 font-mono font-bold text-cyan-400 shrink-0">
                                <Eye class="w-3.5 h-3.5" />
                                <span>{pageItem.view_count || 0} views</span>
                            </div>
                        </div>
                    {/each}
                {/if}
            </div>
        </Card>

    </div>
</AppLayout>
