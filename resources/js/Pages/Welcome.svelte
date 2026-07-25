<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import {
        Sparkles,
        Zap,
        ShieldCheck,
        Layers,
        Cpu,
        Code2,
        ArrowRight,
        CheckCircle2,
        Star,
        Smartphone,
        Lock,
        Terminal,
        Copy,
        Check,
        ChevronRight,
        Globe,
        Rocket,
        Sparkle,
        LayoutDashboard,
        UserCheck,
        Newspaper,
        Calendar,
        Clock,
        Eye,
        BookOpen,
    } from 'lucide-svelte';

    import DynamicSectionRenderer from '@/Components/Landing/DynamicSectionRenderer.svelte';
    import ThemeToggle from '@/Components/UI/ThemeToggle.svelte';

    import type { PageProps } from '@/lib/types';

    interface Props {
        dynamicSections?: any[];
        themeSettings?: any;
        seoSettings?: any;
        latestPosts?: any[];
        navMenu?: any;
    }

    let { dynamicSections = [], themeSettings = {}, seoSettings = {}, latestPosts = [], navMenu = null }: Props = $props();

    let copied = $state(false);
    const installCommand = 'npx create-fairuzkit my-app';

    function copyCommand() {
        navigator.clipboard.writeText(installCommand);
        copied = true;
        setTimeout(() => {
            copied = false;
        }, 2000);
    }

    const pageProps = $derived(page.props as unknown as PageProps);
    const authUser = $derived(pageProps.auth?.user);
    const site = $derived(pageProps.site || { name: 'LaraSvelte', description: '' });
    const branding = $derived(pageProps.branding || {});

    const techStack = [
        {
            name: 'Laravel 13',
            desc: 'Backend PHP Framework robust & aman',
            color: 'from-rose-500 to-red-600',
            tag: 'Backend',
        },
        {
            name: 'Svelte 5',
            desc: 'Runes ($state, $derived) super cepat',
            color: 'from-amber-500 to-orange-600',
            tag: 'Frontend',
        },
        {
            name: 'Inertia.js v2',
            desc: 'Pengalaman SPA tanpa ribet bikin API',
            color: 'from-purple-500 to-indigo-600',
            tag: 'Adapter',
        },
        {
            name: 'Tailwind CSS v4',
            desc: 'Styling modern dengan CSS Engine baru',
            color: 'from-cyan-500 to-blue-600',
            tag: 'Styling',
        },
        {
            name: 'Vite 6',
            desc: 'Bundler & Instant Hot Module Reload',
            color: 'from-emerald-500 to-teal-600',
            tag: 'Build Tool',
        },
        {
            name: 'Spatie RBAC',
            desc: 'Manajemen Peran & Izin Pengguna',
            color: 'from-violet-500 to-purple-600',
            tag: 'Security',
        },
    ];

    const features = [
        {
            icon: Zap,
            title: 'Performa Kilat Svelte 5',
            desc: 'Memanfaatkan Svelte 5 Runes untuk reaktivitas tingkat granular tanpa overhead Virtual DOM.',
            color: 'text-amber-400 bg-amber-500/10 border-amber-500/20',
        },
        {
            icon: ShieldCheck,
            title: 'Sistem RBAC Presisi',
            desc: 'Fitur Super Admin, Admin, dan User dengan hak akses granular hingga level tombol & route.',
            color: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
        },
        {
            icon: Layers,
            title: 'UI Components & Dark Mode',
            desc: 'Dilengkapi komponen Data Table, Modal, Toast, Dialog, Avatar, dan File Upload siap pakai.',
            color: 'text-cyan-400 bg-cyan-500/10 border-cyan-500/20',
        },
        {
            icon: Lock,
            title: 'Keamanan Tingkat Tinggi',
            desc: 'Terintegrasi proteksi CSRF, Sanitasi Input, Hashing Bcrypt, dan Audit Activity Logging.',
            color: 'text-rose-400 bg-rose-500/10 border-rose-500/20',
        },
        {
            icon: Smartphone,
            title: '100% Responsif & Mobile Native',
            desc: 'Desain adaptif penuh yang terlihat menawan di semua ukuran layar dari ponsel hingga 4K.',
            color: 'text-indigo-400 bg-indigo-500/10 border-indigo-500/20',
        },
        {
            icon: Code2,
            title: 'Clean Architecture & Type Safety',
            desc: 'Kode terstruktur rapi dengan TypeScript penuh, siap dikembangkan untuk skala enterprise.',
            color: 'text-purple-400 bg-purple-500/10 border-purple-500/20',
        },
    ];

    const stats = [
        { value: '100%', label: 'Svelte 5 Runes Native' },
        { value: '0.05s', label: 'Rata-rata Waktu Response' },
        { value: '10+', label: 'Komponen UI Siap Pakai' },
        { value: '100%', label: 'Bebas Royalty & Open Source' },
    ];
</script>

<svelte:head>
    <title>{site.name} — Modern CMS Platform</title>
    <meta name="description" content={site.description} />
    {#if branding?.public_favicon}
        <link rel="icon" href={branding.public_favicon} />
    {/if}
    {#if branding?.public_apple_touch_icon}
        <link rel="apple-touch-icon" href={branding.public_apple_touch_icon} />
    {/if}
</svelte:head>

<div
    class="relative min-h-screen overflow-x-hidden bg-slate-50 dark:bg-slate-950 font-sans text-slate-900 dark:text-slate-100 selection:bg-indigo-500 selection:text-white transition-colors duration-300"
>
    <!-- Background Dynamic Glow FX -->
    <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
        <div class="absolute -top-40 -left-40 h-96 w-96 animate-pulse rounded-full bg-indigo-600/20 dark:bg-indigo-600/30 blur-[128px]"></div>
        <div class="absolute top-1/3 -right-40 h-96 w-96 rounded-full bg-purple-600/15 dark:bg-purple-600/20 blur-[140px]"></div>
        <div class="absolute bottom-10 left-1/4 h-[500px] w-[500px] rounded-full bg-emerald-600/10 dark:bg-emerald-600/15 blur-[160px]"></div>
    </div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-50 border-b border-slate-200/80 dark:border-slate-800/80 bg-white/80 dark:bg-slate-950/75 backdrop-blur-xl transition-all">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <!-- Brand Logo -->
            <Link href="/" class="group flex items-center gap-3">
                {#if branding?.public_logo_dark || branding?.public_logo_light}
                    <div class="h-10 flex items-center">
                        {#if branding.public_logo_dark}
                            <img src={branding.public_logo_dark} alt={site.name} class="h-9 object-contain hidden dark:block" />
                        {/if}
                        <img src={branding.public_logo_light || branding.public_logo_dark} alt={site.name} class={`h-9 object-contain ${branding.public_logo_dark ? 'dark:hidden' : ''}`} />
                    </div>
                {:else}
                    <div
                        class="h-10 w-10 rounded-xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-pink-500 p-0.5 shadow-lg shadow-indigo-500/30 transition-transform duration-300 group-hover:scale-105"
                    >
                        <div class="flex h-full w-full items-center justify-center rounded-[10px] bg-white dark:bg-slate-950">
                            <Sparkles
                                class="h-5 w-5 text-indigo-500 dark:text-indigo-400 transition-transform duration-300 group-hover:rotate-12"
                            />
                        </div>
                    </div>
                    <div>
                        <span
                            class="bg-gradient-to-r from-slate-900 dark:from-white via-slate-700 dark:via-slate-100 to-slate-500 dark:to-slate-400 bg-clip-text text-xl font-black tracking-tight text-transparent"
                        >
                            {site.name}
                        </span>
                    </div>
                {/if}
            </Link>

            <!-- Navigation Links (Dynamic from Menu Management) -->
            <nav class="hidden items-center gap-8 text-sm font-medium text-slate-700 dark:text-slate-300 md:flex">
                {#if navMenu && navMenu.items && navMenu.items.length > 0}
                    {#each navMenu.items as item}
                        {#if item.url.startsWith('/')}
                            <Link href={item.url} target={item.target || '_self'} class="transition-colors hover:text-indigo-400">
                                {item.title}
                            </Link>
                        {:else}
                            <a href={item.url} target={item.target || '_self'} class="transition-colors hover:text-indigo-400">
                                {item.title}
                            </a>
                        {/if}
                    {/each}
                {:else}
                    <a href="#features" class="transition-colors hover:text-indigo-400">Fitur Unggulan</a>
                    <a href="#tech-stack" class="transition-colors hover:text-indigo-400">Tech Stack</a>
                    <a href="#quickstart" class="transition-colors hover:text-indigo-400">Quick Start</a>
                    <a href="#stats" class="transition-colors hover:text-indigo-400">Statistik</a>
                    <Link href="/blog" class="transition-colors hover:text-indigo-400 font-semibold text-indigo-300 flex items-center gap-1">
                        <Newspaper class="w-4 h-4 text-indigo-400" />
                        <span>Blog</span>
                    </Link>
                {/if}
            </nav>

            <!-- Auth / Action Buttons & Theme Switcher -->
            <div class="flex items-center gap-3">
                <ThemeToggle />

                {#if authUser}
                    <Link
                        href="/admin/dashboard"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 px-4 py-2 text-xs font-semibold text-white shadow-md shadow-indigo-500/25 transition-all duration-300 hover:from-indigo-500 hover:to-violet-500 active:scale-95"
                    >
                        <LayoutDashboard class="h-4 w-4" />
                        <span>Buka Dashboard</span>
                    </Link>
                {:else}
                    <Link
                        href="/login"
                        class="px-4 py-2 text-xs font-semibold text-slate-300 transition-colors hover:text-white"
                    >
                        Masuk
                    </Link>
                    <Link
                        href="/register"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 px-4 py-2 text-xs font-semibold text-white shadow-md shadow-indigo-500/25 transition-all duration-300 hover:from-indigo-500 hover:to-violet-500 active:scale-95"
                    >
                        <span>Daftar Sekarang</span>
                        <ArrowRight class="h-3.5 w-3.5" />
                    </Link>
                {/if}
            </div>
        </div>
    </header>

    {#if dynamicSections && dynamicSections.length > 0}
        {#each dynamicSections as sec (sec.id)}
            <DynamicSectionRenderer section={sec} />
        {/each}
    {:else}
        <!-- Hero Section -->
        <section class="relative z-10 pt-12 pb-20 lg:pt-20 lg:pb-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-12 lg:gap-8">
                <!-- Hero Left Content -->
                <div class="space-y-8 text-center lg:col-span-7 lg:text-left">
                    <!-- Top Pill Tag -->
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-indigo-500/20 bg-indigo-500/10 px-3.5 py-1.5 text-xs font-medium text-indigo-300 shadow-inner backdrop-blur-md"
                    >
                        <Sparkle class="h-3.5 w-3.5 animate-spin text-amber-400" style="animation-duration: 4s;" />
                        <span>Build SaaS Enterprise in Record Time</span>
                        <ChevronRight class="h-3.5 w-3.5 text-indigo-400" />
                    </div>

                    <!-- Main Headline -->
                    <h1 class="text-4xl leading-[1.15] font-extrabold tracking-tight sm:text-5xl lg:text-6xl">
                        Starter Kit Fullstack <br class="hidden sm:inline" />
                        <span
                            class="bg-gradient-to-r from-indigo-400 via-purple-300 to-pink-400 bg-clip-text text-transparent"
                        >
                            Laravel 13 + Svelte 5
                        </span>
                        <br /> Terkeren untuk SaaS Anda.
                    </h1>

                    <!-- Description -->
                    <p class="mx-auto max-w-2xl text-base leading-relaxed text-slate-400 sm:text-lg lg:mx-0">
                        Nikmati kekuatan Svelte 5 Runes (<code
                            class="rounded bg-slate-900 px-1.5 py-0.5 text-indigo-300">$state</code
                        >, <code class="rounded bg-slate-900 px-1.5 py-0.5 text-indigo-300">$derived</code>) berpadu
                        dengan arsitektur Laravel 13, Inertia v2, RBAC granular, dan Tailwind CSS v4. Siap dideploy hari
                        ini!
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col items-center justify-center gap-4 pt-2 sm:flex-row lg:justify-start">
                        <Link
                            href="/login"
                            class="inline-flex w-full items-center justify-center gap-3 rounded-xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 px-7 py-3.5 text-sm font-semibold text-white shadow-xl shadow-indigo-500/30 transition-all duration-300 hover:-translate-y-0.5 hover:from-indigo-500 hover:to-pink-500 hover:shadow-indigo-500/50 sm:w-auto"
                        >
                            <Rocket class="h-4 w-4" />
                            <span>Coba Demo Aplikasi</span>
                        </Link>
                        <a
                            href="#quickstart"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-800 bg-slate-900 px-7 py-3.5 text-sm font-semibold text-slate-300 transition-all duration-300 hover:bg-slate-800 hover:text-white sm:w-auto"
                        >
                            <Terminal class="h-4 w-4 text-indigo-400" />
                            <span>Panduan Instalasi</span>
                        </a>
                    </div>

                    <!-- Tech Stack Badges Small -->
                    <div
                        class="flex flex-wrap items-center justify-center gap-6 border-t border-slate-800/60 pt-4 text-xs text-slate-400 lg:justify-start"
                    >
                        <span class="flex items-center gap-2 font-medium">
                            <CheckCircle2 class="h-4 w-4 text-emerald-400" />
                            Laravel 13 Backend
                        </span>
                        <span class="flex items-center gap-2 font-medium">
                            <CheckCircle2 class="h-4 w-4 text-emerald-400" />
                            Svelte 5 Runes Engine
                        </span>
                        <span class="flex items-center gap-2 font-medium">
                            <CheckCircle2 class="h-4 w-4 text-emerald-400" />
                            Role-Based RBAC
                        </span>
                    </div>
                </div>

                <!-- Hero Right Content: Feature Graphic with Hijab Mascot -->
                <div class="relative flex justify-center lg:col-span-5">
                    <!-- Glowing Aura behind image -->
                    <div
                        class="absolute inset-0 scale-95 transform animate-pulse rounded-3xl bg-gradient-to-tr from-indigo-600/30 via-purple-600/30 to-pink-500/30 blur-3xl filter"
                    ></div>

                    <!-- Main Image Frame with Glassmorphism -->
                    <div
                        class="relative w-full max-w-md rounded-3xl border border-slate-700/60 bg-gradient-to-b from-indigo-500/20 via-slate-800/40 to-slate-900/80 p-3 shadow-2xl backdrop-blur-2xl"
                    >
                        <div class="group relative overflow-hidden rounded-2xl">
                            <img
                                src="/images/hero-hijab.png"
                                alt="FairuzKit Developer Hijab Mascot"
                                class="h-auto w-full transform object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                            />
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-60"
                            ></div>

                            <!-- Overlay Label -->
                            <div
                                class="absolute right-4 bottom-4 left-4 flex items-center justify-between rounded-xl border border-slate-700/50 bg-slate-900/80 p-3 backdrop-blur-md"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="h-2.5 w-2.5 animate-ping rounded-full bg-emerald-400"></div>
                                    <span class="text-xs font-semibold text-white">FairuzKit Engine v1.0</span>
                                </div>
                                <span
                                    class="rounded-full border border-indigo-500/30 bg-indigo-500/20 px-2 py-0.5 text-[10px] font-semibold text-indigo-300"
                                >
                                    Active
                                </span>
                            </div>
                        </div>

                        <!-- Floating Micro Badges -->
                        <div
                            class="absolute -top-4 -left-4 flex animate-bounce items-center gap-2 rounded-xl border border-slate-700/80 bg-slate-900/90 px-4 py-2 text-xs font-semibold text-indigo-300 shadow-xl backdrop-blur-md"
                            style="animation-duration: 3s;"
                        >
                            <Zap class="h-4 w-4 fill-amber-400 text-amber-400" />
                            <span>100% Svelte 5 Runes</span>
                        </div>

                        <div
                            class="absolute -right-4 -bottom-4 flex animate-bounce items-center gap-2 rounded-xl border border-slate-700/80 bg-slate-900/90 px-4 py-2 text-xs font-semibold text-emerald-300 shadow-xl backdrop-blur-md"
                            style="animation-duration: 4s;"
                        >
                            <ShieldCheck class="h-4 w-4 text-emerald-400" />
                            <span>Spatie RBAC Auth</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Bar Section -->
    <section id="stats" class="relative z-10 border-y border-slate-800/80 bg-slate-900/50 py-12 backdrop-blur-md">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-8 text-center md:grid-cols-4">
                {#each stats as item}
                    <div class="space-y-1">
                        <p
                            class="bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-3xl font-extrabold text-transparent sm:text-4xl"
                        >
                            {item.value}
                        </p>
                        <p class="text-xs font-medium tracking-wider text-slate-400 uppercase">
                            {item.label}
                        </p>
                    </div>
                {/each}
            </div>
        </div>
    </section>    <!-- Key Features Grid -->
    <section id="features" class="relative z-10 py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto mb-16 max-w-3xl space-y-4 text-center">
                <span
                    class="rounded-full border border-indigo-500/20 bg-indigo-500/10 px-3 py-1 text-xs font-semibold tracking-widest text-indigo-500 dark:text-indigo-400 uppercase"
                >
                    Kelebihan Utama
                </span>
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white sm:text-4xl">Semua Fitur Esensial SaaS dalam Satu Kit</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 sm:text-base">
                    Anda tidak perlu membuang waktu berminggu-minggu membuat fitur otentikasi, perizinan, tabel data,
                    dan UI dari nol.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                {#each features as f}
                    <div
                        class="group rounded-2xl border border-slate-200 dark:border-slate-800/80 bg-white dark:bg-slate-900/60 p-6 transition-all duration-300 hover:-translate-y-1 hover:border-indigo-500/40 shadow-xs dark:shadow-none"
                    >
                        <div
                            class={`mb-6 flex h-12 w-12 items-center justify-center rounded-xl border transition-transform group-hover:scale-110 ${f.color}`}
                        >
                            <f.icon class="h-6 w-6" />
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-slate-900 dark:text-white transition-colors group-hover:text-indigo-600 dark:group-hover:text-indigo-300">
                            {f.title}
                        </h3>
                        <p class="text-xs leading-relaxed text-slate-600 dark:text-slate-400 sm:text-sm">
                            {f.desc}
                        </p>
                    </div>
                {/each}
            </div>
        </div>
    </section>

    <!-- Tech Stack Showcase -->
    <section id="tech-stack" class="relative z-10 border-y border-slate-200/80 dark:border-slate-800/60 bg-white/50 dark:bg-slate-900/30 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto mb-14 max-w-2xl space-y-3 text-center">
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">Teknologi Cutting-Edge Terbaru</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400">Ditingkatkan dengan standar ekosistem web modern paling stabil</p>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {#each techStack as tech}
                    <div class="flex items-start gap-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-xs dark:shadow-none">
                        <div class={`h-12 w-3 rounded-full bg-gradient-to-b ${tech.color} shrink-0`}></div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">{tech.name}</h3>
                                <span
                                    class="rounded border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-[10px] font-semibold text-slate-700 dark:text-slate-300"
                                >
                                    {tech.tag}
                                </span>
                            </div>
                            <p class="mt-1 text-xs text-slate-600 dark:text-slate-400">{tech.desc}</p>
                        </div>
                    </div>
                {/each}
            </div>
        </div>
    </section>

    <!-- Quickstart & Installation Section -->
    <section id="quickstart" class="relative z-10 py-24">
        <div class="mx-auto max-w-4xl px-4 sm:px-6">
            <div
                class="space-y-8 rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-gradient-to-b dark:from-slate-900 dark:to-slate-950 p-8 shadow-2xl sm:p-12"
            >
                <div class="space-y-3 text-center">
                    <span class="text-xs font-semibold tracking-widest text-indigo-600 dark:text-indigo-400 uppercase"
                        >Siap dalam 2 Menit</span
                    >
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">Instalasi Instan FairuzKit</h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Cukup jalankan satu perintah di terminal Anda untuk memulai proyek baru.
                    </p>
                </div>

                <!-- Terminal Card -->
                <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-950 shadow-inner text-white">
                    <div class="flex items-center justify-between border-b border-slate-800 bg-slate-900 px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="h-3 w-3 rounded-full bg-rose-500/80"></div>
                            <div class="h-3 w-3 rounded-full bg-amber-500/80"></div>
                            <div class="h-3 w-3 rounded-full bg-emerald-500/80"></div>
                            <span class="ml-2 font-mono text-xs text-slate-400">bash ~ fairuzkit-cli</span>
                        </div>
                        <button
                            onclick={copyCommand}
                            class="flex items-center gap-1.5 rounded-lg bg-slate-800 px-2.5 py-1 text-xs text-slate-300 transition-colors hover:bg-slate-700 hover:text-white"
                        >
                            {#if copied}
                                <Check class="h-3.5 w-3.5 text-emerald-400" />
                                <span class="text-emerald-400">Tersalin!</span>
                            {:else}
                                <Copy class="h-3.5 w-3.5" />
                                <span>Salin Perintah</span>
                            {/if}
                        </button>
                    </div>

                    <div class="space-y-3 p-6 font-mono text-sm">
                        <div class="flex items-center gap-3 text-indigo-300">
                            <span class="text-emerald-400">$</span>
                            <span>{installCommand}</span>
                        </div>
                        <div class="space-y-1 border-t border-slate-900 pt-2 text-xs text-slate-400">
                            <p>✔ Cloning repository FairuzKit...</p>
                            <p>✔ Installing composer & npm dependencies...</p>
                            <p>✔ Migrating database tables & seeders...</p>
                            <p class="font-semibold text-emerald-400">✨ Server running on http://localhost:8000</p>
                        </div>
                    </div>
                </div>

                <!-- Action CTA -->
                <div class="pt-2 text-center">
                    <Link
                        href="/register"
                        class="inline-flex items-center gap-3 rounded-xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 px-8 py-4 text-sm font-bold text-white shadow-xl shadow-indigo-500/30 transition-all duration-300 hover:scale-105 hover:from-indigo-500 hover:to-pink-500"
                    >
                        <span>Mulai Sekarang (Gratis)</span>
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                </div>
            </div>
        </div>
    </section>
    {/if}

    <!-- Latest Blog Articles Section -->
    {#if latestPosts && latestPosts.length > 0}
    <section class="relative z-10 border-t border-slate-200/80 dark:border-slate-900 bg-white/60 dark:bg-slate-950 py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="space-y-3 max-w-xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-indigo-500/30 bg-indigo-50 dark:bg-indigo-950/60 px-3.5 py-1 text-xs font-semibold text-indigo-600 dark:text-indigo-300">
                        <Newspaper class="h-3.5 w-3.5 text-indigo-500 dark:text-indigo-400" />
                        <span>Kabar & Artikel Terbaru</span>
                    </div>
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white sm:text-4xl tracking-tight">
                        Wawasan & Tutorial Teknologi Terkini
                    </h2>
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                        Pelajari tips pengembangan aplikasi, tren AI, strategi bisnis digital, dan berita pembaruan platform dari para ahli.
                    </p>
                </div>

                <Link
                    href="/blog"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-5 py-3 text-xs font-bold text-indigo-600 dark:text-indigo-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-all shadow-xs shrink-0"
                >
                    <span>Lihat Semua Artikel</span>
                    <ArrowRight class="h-4 w-4" />
                </Link>
            </div>

            <!-- Articles Grid Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {#each latestPosts as post}
                    <article class="group relative rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 overflow-hidden hover:border-indigo-500/40 transition-all duration-300 flex flex-col justify-between shadow-xs hover:shadow-xl dark:hover:shadow-indigo-500/10">
                        <div class="space-y-4">
                            <Link href={`/blog/${post.slug}`} class="relative block overflow-hidden h-48">
                                <img
                                    src={post.featured_image || 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800'}
                                    alt={post.title}
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                />
                                {#if post.category}
                                    <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/90 dark:bg-slate-950/80 backdrop-blur-md text-indigo-600 dark:text-indigo-300 border border-slate-200 dark:border-slate-700 shadow-xs">
                                        {post.category.name}
                                    </span>
                                {/if}
                            </Link>

                            <div class="px-5 space-y-2">
                                <h3 class="font-bold text-base text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2 leading-snug">
                                    <Link href={`/blog/${post.slug}`}>{post.title}</Link>
                                </h3>
                                <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                    {post.summary || post.content?.replace(/<[^>]*>?/gm, '').slice(0, 110)}...
                                </p>
                            </div>
                        </div>

                        <div class="p-5 pt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[11px] text-slate-600 dark:text-slate-400 font-mono mt-4">
                            <div class="flex items-center gap-2">
                                <img src={post.author?.avatar_url || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150'} alt={post.author?.name} class="w-5 h-5 rounded-full object-cover border border-slate-200 dark:border-slate-700" />
                                <span class="truncate max-w-[90px]">{post.author?.name || 'Admin'}</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="flex items-center gap-1" title="Tanggal Terbit">
                                    <Calendar class="w-3 h-3 text-indigo-500 dark:text-indigo-400" />
                                    {post.published_at ? new Date(post.published_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) : 'Draft'}
                                </span>
                                <span class="flex items-center gap-1" title="Waktu Baca">
                                    <Clock class="w-3 h-3 text-amber-500 dark:text-amber-400" />
                                    {post.reading_time || 3}m
                                </span>
                            </div>
                        </div>
                    </article>
                {/each}
            </div>
        </div>
    </section>
    {/if}

    <!-- Footer -->
    <footer class="relative z-10 border-t border-slate-200 dark:border-slate-800/80 bg-white dark:bg-slate-950 py-12 text-xs text-slate-600 dark:text-slate-500">
        <div
            class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 sm:flex-row sm:px-6 lg:px-8"
        >
            <div class="flex items-center gap-3">
                {#if branding?.public_logo_footer}
                    <img src={branding.public_logo_footer} alt={site.name} class="h-7 object-contain" />
                {:else}
                    <div
                        class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-600 text-sm font-black text-white"
                    >
                        {site.name.charAt(0).toUpperCase()}
                    </div>
                    <span class="font-semibold text-slate-900 dark:text-slate-300">{site.name}</span>
                {/if}
            </div>

            <p>© {new Date().getFullYear()} {site.name}. All rights reserved.</p>
        </div>
    </footer>
</div>
