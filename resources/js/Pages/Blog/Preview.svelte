<script lang="ts">
    import { router, Link } from '@inertiajs/svelte';
    import { Eye, Edit3, ArrowLeft, ShieldAlert, CheckCircle, Calendar, Clock, User, Share2 } from 'lucide-svelte';

    interface Props {
        post: any;
        previewToken: string;
    }

    let { post, previewToken }: Props = $props();

    function publishNow() {
        if (confirm('Terbitkan artikel ini sekarang ke publik?')) {
            router.put(`/admin/cms/posts/${post.id}`, {
                ...post,
                status: 'published',
                published_at: new Date().toISOString().slice(0, 16)
            });
        }
    }
</script>

<svelte:head>
    <title>[PREVIEW MODE] {post.title}</title>
    <meta name="robots" content="noindex, nofollow" />
</svelte:head>

<div class="min-h-screen bg-slate-950 text-slate-100 font-sans selection:bg-amber-500 selection:text-black">

    <!-- PREVIEW MODE TOP BANNER FOR ADMINS -->
    <div class="sticky top-0 z-50 bg-gradient-to-r from-amber-600 via-amber-500 to-amber-600 text-slate-950 px-4 py-2.5 shadow-xl flex flex-wrap items-center justify-between gap-3 text-xs font-bold">
        <div class="flex items-center gap-2">
            <ShieldAlert class="w-4 h-4 animate-bounce shrink-0" />
            <span>PREVIEW MODE — Artikel ini belum dipublikasikan (Status: <span class="uppercase underline font-extrabold">{post.status}</span>)</span>
        </div>

        <div class="flex items-center gap-2">
            <Link href="/admin/dashboard" class="px-3 py-1 rounded-lg bg-slate-950 text-white hover:bg-slate-900 transition-colors flex items-center gap-1">
                <ArrowLeft class="w-3.5 h-3.5" />
                <span>Dashboard</span>
            </Link>

            <Link href={`/admin/cms/posts/${post.id}/edit`} class="px-3 py-1 rounded-lg bg-slate-950 text-amber-300 hover:bg-slate-900 transition-colors flex items-center gap-1">
                <Edit3 class="w-3.5 h-3.5" />
                <span>Edit Artikel</span>
            </Link>

            <button type="button" onclick={publishNow} class="px-3 py-1 rounded-lg bg-emerald-950 text-emerald-300 hover:bg-emerald-900 transition-colors flex items-center gap-1">
                <CheckCircle class="w-3.5 h-3.5" />
                <span>Terbitkan Artikel</span>
            </button>
        </div>
    </div>

    <!-- Main Container -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

        <!-- Article Header -->
        <header class="space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    STATUS: {post.status.toUpperCase()}
                </span>
                {#if post.category}
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        {post.category.name}
                    </span>
                {/if}
            </div>

            <h1 class="text-2xl md:text-4xl font-extrabold text-white leading-tight">
                {post.title}
            </h1>

            <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 flex flex-wrap items-center justify-between gap-4 text-xs font-mono text-slate-400">
                <div class="flex items-center gap-2">
                    <img src={post.author?.avatar_url || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150'} alt={post.author?.name} class="w-6 h-6 rounded-full object-cover" />
                    <span class="text-slate-200 font-semibold">{post.author?.name || 'Admin'}</span>
                </div>

                <div class="flex items-center gap-4 text-[11px]">
                    <span class="flex items-center gap-1">
                        <Calendar class="w-3.5 h-3.5 text-indigo-400" />
                        Target Terbit: {post.published_at ? new Date(post.published_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : 'Draf Konten'}
                    </span>
                    <span class="flex items-center gap-1">
                        <Clock class="w-3.5 h-3.5 text-amber-400" />
                        {post.reading_time || 3}m Baca
                    </span>
                </div>
            </div>
        </header>

        <!-- Featured Image -->
        {#if post.featured_image}
            <div class="overflow-hidden rounded-3xl border border-slate-800 shadow-2xl">
                <img src={post.featured_image} alt={post.title} class="w-full max-h-[450px] object-cover" />
            </div>
        {/if}

        <!-- Article Body -->
        <div class="prose prose-invert max-w-none text-slate-200 leading-relaxed text-sm md:text-base space-y-4">
            {@html post.content || post.summary || '<p class="text-slate-500 italic">Konten artikel belum diisi.</p>'}
        </div>

    </main>
</div>
