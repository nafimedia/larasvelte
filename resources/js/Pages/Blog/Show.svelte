<script lang="ts">
    import { useForm, Link } from '@inertiajs/svelte';
    import { Calendar, Clock, Eye, User, Share2, MessageSquare, Send, ArrowLeft, Bookmark, Sparkles, List } from 'lucide-svelte';
    import ThemeToggle from '@/Components/UI/ThemeToggle.svelte';

    interface Props {
        post: any;
        toc?: any[];
        relatedPosts?: any[];
        popularPosts?: any[];
        categories?: any[];
        popularTags?: any[];
    }

    let {
        post,
        toc = [],
        relatedPosts = [],
        popularPosts = [],
        categories = [],
        popularTags = []
    }: Props = $props();

    // Comment Form state
    // svelte-ignore state_referenced_locally
    const commentForm = useForm({
        author_name: '',
        author_email: '',
        content: '',
        parent_id: null as number | null,
    });

    let copiedLink = $state(false);

    function submitComment(e: Event) {
        e.preventDefault();
        commentForm.post(`/blog/${post.id}/comments`, {
            onSuccess: () => commentForm.reset('content')
        });
    }

    function copyLink() {
        navigator.clipboard.writeText(window.location.href);
        copiedLink = true;
        setTimeout(() => copiedLink = false, 2000);
    }

    const currentUrl = typeof window !== 'undefined' ? window.location.href : '';

    // Dynamic Schema.org JSON-LD for Google Rich Snippets
    const jsonLdSchema = $derived(JSON.stringify({
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": post.title,
        "image": [post.featured_image],
        "datePublished": post.published_at,
        "dateModified": post.updated_at,
        "author": [{
            "@type": "Person",
            "name": post.author?.name || 'Admin',
        }],
        "publisher": {
            "@type": "Organization",
            "name": "FairuzKit CMS",
            "logo": {
                "@type": "ImageObject",
                "url": "https://laravel.com/img/logomark.min.svg"
            }
        },
        "description": post.summary || post.meta_description
    }));
</script>

<svelte:head>
    <title>{post.meta_title || post.title} | FairuzKit Blog</title>
    <meta name="description" content={post.meta_description || post.summary} />
    <meta name="keywords" content={post.keywords} />
    <meta property="og:title" content={post.title} />
    <meta property="og:description" content={post.summary} />
    <meta property="og:image" content={post.featured_image} />
    <meta property="og:type" content="article" />
    {@html `<script type="application/ld+json">${jsonLdSchema}</script>`}
</svelte:head>

<div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans selection:bg-indigo-500 selection:text-white transition-colors duration-300">

    <!-- Navigation Header -->
    <nav class="sticky top-0 z-40 bg-white/80 dark:bg-slate-950/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Link href="/blog" class="p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-white hover:border-indigo-500/40 transition-all flex items-center gap-1.5 text-xs font-semibold">
                    <ArrowLeft class="w-4 h-4" />
                    <span>Kembali ke Blog</span>
                </Link>
            </div>

            <Link href="/" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center font-bold text-white shadow-lg shadow-indigo-500/20">
                    F
                </div>
                <span class="font-extrabold text-lg text-slate-900 dark:text-white tracking-tight">FairuzKit</span>
            </Link>

            <div class="flex items-center gap-3">
                <ThemeToggle />
                <Link href="/admin/dashboard" class="px-3.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition-all">Dashboard Admin</Link>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            <!-- Article Body Area (8 cols) -->
            <article class="lg:col-span-8 space-y-8">

                <!-- Article Header Meta -->
                <header class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        {#if post.category}
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                {post.category.name}
                            </span>
                        {/if}
                        {#each (post.tags || []) as tag}
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-900 text-slate-400 border border-slate-800">
                                #{tag.name}
                            </span>
                        {/each}
                    </div>

                    <h1 class="text-2xl md:text-4xl font-extrabold text-white leading-tight tracking-tight">
                        {post.title}
                    </h1>

                    <!-- Author Info & Publication Timeline -->
                    <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 flex flex-wrap items-center justify-between gap-4 text-xs">
                        <div class="flex items-center gap-3">
                            <img src={post.author?.avatar_url || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150'} alt={post.author?.name} class="w-10 h-10 rounded-full object-cover border border-slate-700" />
                            <div>
                                <p class="font-bold text-white">{post.author?.name || 'Admin'}</p>
                                <p class="text-[10px] text-slate-400">{post.author?.bio || 'Penulis Konten Teknis'}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 text-slate-400 font-mono text-[11px]">
                            <span class="flex items-center gap-1" title="Tanggal Terbit">
                                <Calendar class="w-3.5 h-3.5 text-indigo-400" />
                                {post.published_at ? new Date(post.published_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : 'Draft'}
                            </span>
                            <span class="flex items-center gap-1" title="Waktu Baca">
                                <Clock class="w-3.5 h-3.5 text-amber-400" />
                                {post.reading_time} Menit Baca
                            </span>
                            <span class="flex items-center gap-1" title="Tayangan">
                                <Eye class="w-3.5 h-3.5 text-cyan-400" />
                                {post.view_count || 0} Views
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

                <!-- Social Share Bar -->
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-900/60 border border-slate-800/80 text-xs">
                    <span class="font-semibold text-slate-400 flex items-center gap-1.5">
                        <Share2 class="w-4 h-4 text-indigo-400" /> Bagikan Artikel:
                    </span>
                    <div class="flex items-center gap-2">
                        <a href={`https://twitter.com/intent/tweet?text=${encodeURIComponent(post.title)}&url=${encodeURIComponent(currentUrl)}`} target="_blank" class="p-2 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition-colors" title="Twitter / X">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href={`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(currentUrl)}`} target="_blank" class="p-2 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition-colors" title="Facebook">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href={`https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(currentUrl)}&title=${encodeURIComponent(post.title)}`} target="_blank" class="p-2 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition-colors" title="LinkedIn">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9v8.37H9.25V10.9H6.46M7.86 6.75a1.45 1.45 0 1 0 0 2.9 1.45 1.45 0 0 0 0-2.9z"/></svg>
                        </a>
                        <button type="button" onclick={copyLink} class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition-colors flex items-center gap-1">
                            {copiedLink ? 'Link Tersalin!' : 'Salin Link'}
                        </button>
                    </div>
                </div>

                <!-- Table of Contents (TOC) -->
                {#if toc && toc.length > 0}
                    <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2">
                            <List class="w-4 h-4 text-indigo-400" />
                            <span>Daftar Isi Artikel (Table of Contents)</span>
                        </h3>
                        <div class="space-y-1.5 text-xs font-medium">
                            {#each toc as item}
                                <div class={item.level === 3 ? 'pl-4 text-slate-400' : 'text-indigo-300 font-semibold'}>
                                    <span class="hover:underline cursor-pointer">• {item.title}</span>
                                </div>
                            {/each}
                        </div>
                    </div>
                {/if}

                <!-- Article Body Render -->
                <div class="prose prose-invert max-w-none text-slate-200 leading-relaxed text-sm md:text-base space-y-4">
                    {@html post.content || post.summary || '<p class="text-slate-500 italic">Konten artikel tidak tersedia.</p>'}
                </div>

                <!-- Related Articles Section -->
                {#if relatedPosts.length > 0}
                    <div class="pt-8 border-t border-slate-800 space-y-4">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <Sparkles class="w-5 h-5 text-indigo-400" />
                            <span>Artikel Terkait</span>
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            {#each relatedPosts as rel}
                                <Link href={`/blog/${rel.slug}`} class="group p-3 rounded-2xl bg-slate-900 border border-slate-800 hover:border-indigo-500/40 transition-all space-y-2">
                                    {#if rel.featured_image}
                                        <img src={rel.featured_image} alt={rel.title} class="w-full h-28 object-cover rounded-xl border border-slate-800 group-hover:scale-102 transition-transform" />
                                    {/if}
                                    <h4 class="font-bold text-xs text-white group-hover:text-indigo-400 transition-colors line-clamp-2">{rel.title}</h4>
                                    <p class="text-[10px] text-slate-400 font-mono">{rel.published_at ? new Date(rel.published_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) : 'Draft'}</p>
                                </Link>
                            {/each}
                        </div>
                    </div>
                {/if}

                <!-- Threaded Comments Section -->
                <div class="pt-8 border-t border-slate-800 space-y-6">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <MessageSquare class="w-5 h-5 text-indigo-400" />
                        <span>Komentar Pengunjung ({post.comments?.length || 0})</span>
                    </h3>

                    <!-- Post Comment Form -->
                    <form onsubmit={submitComment} class="p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Tinggalkan Komentar</h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <input
                                type="text"
                                placeholder="Nama Anda *"
                                bind:value={commentForm.author_name}
                                class="px-3.5 py-2 text-xs rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                                required
                            />
                            <input
                                type="email"
                                placeholder="Email Anda *"
                                bind:value={commentForm.author_email}
                                class="px-3.5 py-2 text-xs rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                                required
                            />
                        </div>

                        <textarea
                            rows="3"
                            placeholder="Tulis pesan atau tanggapan Anda..."
                            bind:value={commentForm.content}
                            class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                            required
                        ></textarea>

                        <button type="submit" disabled={commentForm.processing} class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition-all flex items-center gap-1.5">
                            <Send class="w-3.5 h-3.5" />
                            <span>Kirim Komentar</span>
                        </button>
                    </form>

                    <!-- Comments List -->
                    <div class="space-y-3">
                        {#each (post.comments || []) as comm}
                            <div class="p-4 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-2 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-white">{comm.author_name}</span>
                                    <span class="text-[10px] text-slate-500 font-mono">{new Date(comm.created_at).toLocaleDateString('id-ID')}</span>
                                </div>
                                <p class="text-slate-300 leading-relaxed">"{comm.content}"</p>
                            </div>
                        {/each}
                    </div>
                </div>

            </article>

            <!-- Sidebar Desktop (4 cols) -->
            <aside class="lg:col-span-4 space-y-6">
                <!-- Popular Articles Widget -->
                <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-4">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <Sparkles class="w-4 h-4 text-amber-400" />
                        <span>Artikel Terpopuler</span>
                    </h3>
                    <div class="space-y-3">
                        {#each popularPosts as pop, i}
                            <Link href={`/blog/${pop.slug}`} class="flex items-center gap-3 group">
                                <span class="w-6 h-6 rounded-lg bg-indigo-950 text-indigo-400 font-bold font-mono text-xs flex items-center justify-center shrink-0 border border-indigo-500/20">
                                    #{i + 1}
                                </span>
                                <div class="truncate">
                                    <p class="text-xs font-bold text-slate-200 group-hover:text-indigo-400 transition-colors truncate">{pop.title}</p>
                                    <span class="text-[10px] text-slate-400 font-mono">{pop.view_count || 0} views</span>
                                </div>
                            </Link>
                        {/each}
                    </div>
                </div>

                <!-- Categories Widget -->
                <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-3">
                    <h3 class="text-sm font-bold text-white">Kategori Artikel</h3>
                    <div class="space-y-1.5 text-xs">
                        {#each categories as cat}
                            <Link href={`/blog?category=${cat.slug}`} class="flex items-center justify-between p-2 rounded-xl bg-slate-950 hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">
                                <span>{cat.name}</span>
                                <span class="font-mono text-[10px] text-indigo-400 font-bold px-2 py-0.5 rounded-full bg-slate-900 border border-slate-800">{cat.posts_count}</span>
                            </Link>
                        {/each}
                    </div>
                </div>
            </aside>

        </div>

    </main>
</div>
