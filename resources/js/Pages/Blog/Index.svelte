<script lang="ts">
    import { router, Link } from '@inertiajs/svelte';
    import { Search, Grid, List, Clock, Eye, Calendar, User, ArrowRight, Sparkles, Filter, Newspaper, ChevronRight } from 'lucide-svelte';
    import ThemeToggle from '@/Components/UI/ThemeToggle.svelte';

    interface Props {
        posts: any;
        featuredPost?: any;
        popularPosts?: any[];
        categories?: any[];
        popularTags?: any[];
        filters?: any;
    }

    let {
        posts,
        featuredPost = null,
        popularPosts = [],
        categories = [],
        popularTags = [],
        filters = {}
    }: Props = $props();

    let viewMode = $state<'grid' | 'list'>('grid');
    // svelte-ignore state_referenced_locally
    let searchValue = $state(filters.search || '');

    function handleSearch(e: Event) {
        e.preventDefault();
        router.get('/blog', {
            ...filters,
            search: searchValue
        }, { preserveState: true });
    }

    function applyFilter(key: string, val: string) {
        const newFilters = { ...filters };
        if (val) {
            newFilters[key] = val;
        } else {
            delete newFilters[key];
        }
        router.get('/blog', newFilters, { preserveState: true });
    }
</script>

<div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans selection:bg-indigo-500 selection:text-white transition-colors duration-300">

    <!-- Header Navigation -->
    <nav class="sticky top-0 z-40 bg-white/80 dark:bg-slate-950/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <Link href="/" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center font-bold text-white shadow-lg shadow-indigo-500/20">
                    F
                </div>
                <span class="font-extrabold text-lg text-slate-900 dark:text-white tracking-tight">FairuzKit <span class="text-indigo-600 dark:text-indigo-400 text-xs font-mono font-semibold px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-950 border border-indigo-200 dark:border-indigo-500/30">Blog</span></span>
            </Link>

            <div class="flex items-center gap-4 text-xs font-semibold">
                <ThemeToggle />
                <Link href="/" class="text-slate-400 hover:text-white transition-colors">Beranda</Link>
                <Link href="/blog" class="text-indigo-400 font-bold">Blog & Artikel</Link>
                <Link href="/admin/dashboard" class="px-3.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white transition-all shadow-sm">Dashboard Admin</Link>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">

        <!-- Hero Section: Featured Article -->
        {#if featuredPost && !filters.search && !filters.category && !filters.tag}
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-950/80 via-slate-900 to-slate-950 border border-indigo-500/20 p-6 md:p-10 shadow-2xl">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-7 space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 flex items-center gap-1.5">
                                <Sparkles class="w-3.5 h-3.5" /> Featured Article
                            </span>
                            {#if featuredPost.category}
                                <span class="text-xs text-indigo-400 font-semibold uppercase tracking-wider font-mono">
                                    • {featuredPost.category.name}
                                </span>
                            {/if}
                        </div>

                        <h1 class="text-2xl md:text-4xl font-extrabold text-white leading-tight hover:text-indigo-300 transition-colors">
                            <Link href={`/blog/${featuredPost.slug}`}>{featuredPost.title}</Link>
                        </h1>

                        <p class="text-slate-300 text-sm md:text-base line-clamp-3 leading-relaxed">
                            {featuredPost.summary || featuredPost.content?.replace(/<[^>]*>?/gm, '').slice(0, 180)}...
                        </p>

                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400 pt-2 font-mono">
                            <div class="flex items-center gap-2">
                                <img src={featuredPost.author?.avatar_url || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150'} alt={featuredPost.author?.name} class="w-6 h-6 rounded-full object-cover border border-slate-700" />
                                <span class="text-slate-200 font-semibold">{featuredPost.author?.name || 'Admin'}</span>
                            </div>
                            <span>•</span>
                            <span class="flex items-center gap-1">
                                <Calendar class="w-3.5 h-3.5 text-indigo-400" />
                                {featuredPost.published_at ? new Date(featuredPost.published_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : 'Draft'}
                            </span>
                            <span>•</span>
                            <span class="flex items-center gap-1">
                                <Clock class="w-3.5 h-3.5 text-amber-400" />
                                {featuredPost.reading_time || 3} Menit Baca
                            </span>
                        </div>

                        <div class="pt-3">
                            <Link href={`/blog/${featuredPost.slug}`} class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition-all shadow-lg shadow-indigo-600/30">
                                <span>Baca Artikel Selengkapnya</span>
                                <ArrowRight class="w-4 h-4" />
                            </Link>
                        </div>
                    </div>

                    <div class="lg:col-span-5">
                        <Link href={`/blog/${featuredPost.slug}`} class="block overflow-hidden rounded-2xl border border-slate-800 group">
                            <img src={featuredPost.featured_image} alt={featuredPost.title} class="w-full h-64 md:h-80 object-cover group-hover:scale-105 transition-transform duration-500" />
                        </Link>
                    </div>
                </div>
            </div>
        {/if}

        <!-- Filter & Search Toolbar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 rounded-2xl bg-slate-900/90 border border-slate-800">
            <form onsubmit={handleSearch} class="relative flex-1 max-w-md">
                <Search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                <input
                    type="text"
                    placeholder="Cari judul artikel, topik, atau kata kunci..."
                    bind:value={searchValue}
                    class="w-full pl-10 pr-4 py-2 text-xs rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 placeholder:text-slate-500"
                />
            </form>

            <div class="flex flex-wrap items-center gap-3 text-xs">
                <!-- Category Filter Dropdown -->
                <select
                    value={filters.category || ''}
                    onchange={(e) => applyFilter('category', (e.target as HTMLSelectElement).value)}
                    class="px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 font-semibold"
                >
                    <option value="">Semua Kategori</option>
                    {#each categories as c}
                        <option value={c.slug}>{c.name} ({c.posts_count})</option>
                    {/each}
                </select>

                <!-- Sort Dropdown -->
                <select
                    value={filters.sort || 'latest'}
                    onchange={(e) => applyFilter('sort', (e.target as HTMLSelectElement).value)}
                    class="px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 font-semibold"
                >
                    <option value="latest">Terbaru</option>
                    <option value="popular">Terpopuler (Views)</option>
                    <option value="oldest">Terlama</option>
                </select>

                <!-- Grid/List Layout Toggle -->
                <div class="flex items-center bg-slate-950 p-1 rounded-xl border border-slate-800">
                    <button
                        type="button"
                        onclick={() => viewMode = 'grid'}
                        class={`p-1.5 rounded-lg transition-colors ${viewMode === 'grid' ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white'}`}
                        title="Tampilan Grid"
                    >
                        <Grid class="w-4 h-4" />
                    </button>
                    <button
                        type="button"
                        onclick={() => viewMode = 'list'}
                        class={`p-1.5 rounded-lg transition-colors ${viewMode === 'list' ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white'}`}
                        title="Tampilan List"
                    >
                        <List class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Articles Content & Sidebar Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Articles Main Stream (8 cols) -->
            <div class="lg:col-span-8 space-y-6">

                {#if posts.data.length === 0}
                    <div class="p-12 text-center rounded-3xl bg-slate-900 border border-slate-800 space-y-3">
                        <Newspaper class="w-12 h-12 text-slate-600 mx-auto" />
                        <h3 class="text-base font-bold text-white">Tidak Ada Artikel Ditemukan</h3>
                        <p class="text-xs text-slate-400 max-w-sm mx-auto">Coba ubah kata kunci pencarian atau sesuaikan filter kategori Anda.</p>
                        <button type="button" onclick={() => router.get('/blog')} class="px-4 py-2 rounded-xl bg-slate-800 text-xs font-semibold text-indigo-400 hover:bg-slate-700 transition-colors">
                            Reset Filter Pencarian
                        </button>
                    </div>
                {:else}
                    <!-- Articles Container (Grid / List) -->
                    <div class={viewMode === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 gap-6' : 'space-y-6'}>
                        {#each posts.data as post}
                            <article class={`group rounded-2xl bg-slate-900/90 border border-slate-800 overflow-hidden hover:border-indigo-500/40 transition-all duration-300 flex ${
                                viewMode === 'list' ? 'flex-col sm:flex-row' : 'flex-col'
                            }`}>
                                <Link href={`/blog/${post.slug}`} class={viewMode === 'list' ? 'sm:w-1/3 shrink-0 relative overflow-hidden' : 'relative overflow-hidden h-48'}>
                                    <img src={post.featured_image || 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800'} alt={post.title} class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                    {#if post.category}
                                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-950/80 backdrop-blur-md text-indigo-300 border border-slate-700">
                                            {post.category.name}
                                        </span>
                                    {/if}
                                </Link>

                                <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                                    <div class="space-y-2">
                                        <h2 class="font-bold text-base text-white group-hover:text-indigo-400 transition-colors line-clamp-2 leading-snug">
                                            <Link href={`/blog/${post.slug}`}>{post.title}</Link>
                                        </h2>
                                        <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed">
                                            {post.summary || post.content?.replace(/<[^>]*>?/gm, '').slice(0, 100)}...
                                        </p>
                                    </div>

                                    <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-400 font-mono">
                                        <div class="flex items-center gap-2">
                                            <img src={post.author?.avatar_url || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150'} alt={post.author?.name} class="w-5 h-5 rounded-full object-cover" />
                                            <span class="truncate max-w-[100px]">{post.author?.name || 'Admin'}</span>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <span class="flex items-center gap-1">
                                                <Calendar class="w-3 h-3 text-indigo-400" />
                                                {post.published_at ? new Date(post.published_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) : 'Draft'}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <Eye class="w-3 h-3 text-cyan-400" />
                                                {post.view_count || 0}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        {/each}
                    </div>

                    <!-- Pagination -->
                    {#if posts.links && posts.links.length > 3}
                        <div class="flex items-center justify-center gap-1 pt-6">
                            {#each posts.links as link}
                                {#if link.url}
                                    <Link
                                        href={link.url}
                                        class={`px-3.5 py-2 rounded-xl text-xs font-semibold transition-all ${
                                            link.active ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-900 text-slate-400 hover:bg-slate-800 hover:text-white border border-slate-800'
                                        }`}
                                    >
                                        {@html link.label}
                                    </Link>
                                {/if}
                            {/each}
                        </div>
                    {/if}
                {/if}
            </div>

            <!-- Sidebar Widgets (4 cols) -->
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

                <!-- Popular Tags Widget -->
                <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-4">
                    <h3 class="text-sm font-bold text-white">Tag Populer</h3>
                    <div class="flex flex-wrap gap-1.5">
                        {#each popularTags as tag}
                            <button
                                type="button"
                                onclick={() => applyFilter('tag', tag.slug)}
                                class={`px-2.5 py-1 rounded-full text-[11px] font-semibold border transition-all ${
                                    filters.tag === tag.slug
                                        ? 'bg-indigo-600 text-white border-indigo-500'
                                        : 'bg-slate-950 text-slate-400 border-slate-800 hover:text-white'
                                }`}
                            >
                                #{tag.name}
                            </button>
                        {/each}
                    </div>
                </div>

                <!-- Newsletter Card -->
                <div class="p-6 rounded-2xl bg-gradient-to-br from-indigo-950 to-slate-900 border border-indigo-500/20 space-y-3">
                    <h4 class="text-sm font-bold text-white">Berlangganan Newsletter</h4>
                    <p class="text-xs text-slate-300">Dapatkan ringkasan artikel teknologi dan tutorial terbaru langsung di inbox email Anda setiap minggu.</p>
                    <div class="space-y-2 pt-1">
                        <input type="email" placeholder="Alamat email Anda..." class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30" />
                        <button type="button" class="w-full py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition-all">Berlangganan Sekarang</button>
                    </div>
                </div>

            </aside>
        </div>

    </main>

    <!-- Footer -->
    <footer class="mt-20 border-t border-slate-800 py-8 bg-slate-950">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-slate-500 space-y-2">
            <p>&copy; 2026 FairuzKit CMS Platform. All rights reserved.</p>
            <p>Built with Laravel 13, Svelte 5 Runes & Inertia.js v2</p>
        </div>
    </footer>
</div>
