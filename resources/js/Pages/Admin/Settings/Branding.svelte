<script lang="ts">
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import BrandingUploader from '@/Components/UI/BrandingUploader.svelte';
    import { Globe, LayoutDashboard, Eye, Sun, Moon, Smartphone, ShieldCheck, Sparkles } from 'lucide-svelte';
    import type { BrandingAsset } from '@/lib/types';

    interface Props {
        branding: Record<string, BrandingAsset>;
    }

    let { branding = {} }: Props = $props();

    let activeTab = $state<'public' | 'admin' | 'preview'>('public');
    let previewTheme = $state<'light' | 'dark'>('light');

    const publicAssetsKeys = [
        'public_logo_light',
        'public_logo_dark',
        'public_logo_mobile',
        'public_logo_footer',
        'public_favicon',
        'public_apple_touch_icon',
    ];

    const adminAssetsKeys = [
        'admin_logo_light',
        'admin_logo_dark',
        'admin_logo_collapsed',
        'admin_favicon',
        'admin_login_logo',
    ];

    const recommendedSizes: Record<string, string> = {
        public_logo_light: 'Rekomendasi: 240x60 px (PNG/SVG)',
        public_logo_dark: 'Rekomendasi: 240x60 px (PNG/SVG)',
        public_logo_mobile: 'Rekomendasi: 120x40 px (PNG/SVG)',
        public_logo_footer: 'Rekomendasi: 200x50 px (PNG/SVG)',
        public_favicon: 'Rekomendasi: 32x32 / 48x48 px (ICO/PNG)',
        public_apple_touch_icon: 'Rekomendasi: 180x180 px (PNG)',
        admin_logo_light: 'Rekomendasi: 200x50 px (PNG/SVG)',
        admin_logo_dark: 'Rekomendasi: 200x50 px (PNG/SVG)',
        admin_logo_collapsed: 'Rekomendasi: 48x48 px (Icon Only)',
        admin_favicon: 'Rekomendasi: 32x32 px (ICO/PNG)',
        admin_login_logo: 'Rekomendasi: 280x70 px (PNG/SVG)',
    };
</script>

<AppLayout title="Manajemen Branding & Identitas Visual">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <Sparkles class="w-5 h-5 text-indigo-500" />
                <span>Site Branding & Visual Identity</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Kelola logo, favicon, dan identitas visual publik serta admin panel secara terpisah dan dinamis
            </p>
        </div>

        <!-- Navigation Tabs -->
        <div class="inline-flex p-1 rounded-xl bg-slate-200/80 dark:bg-slate-800/80 border border-slate-300/60 dark:border-slate-700/60">
            <button
                type="button"
                onclick={() => activeTab = 'public'}
                class={`inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer ${
                    activeTab === 'public'
                        ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-xs'
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'
                }`}
            >
                <Globe class="w-3.5 h-3.5" />
                <span>Public Website</span>
            </button>

            <button
                type="button"
                onclick={() => activeTab = 'admin'}
                class={`inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer ${
                    activeTab === 'admin'
                        ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-xs'
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'
                }`}
            >
                <LayoutDashboard class="w-3.5 h-3.5" />
                <span>Admin Dashboard</span>
            </button>

            <button
                type="button"
                onclick={() => activeTab = 'preview'}
                class={`inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer ${
                    activeTab === 'preview'
                        ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-xs'
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'
                }`}
            >
                <Eye class="w-3.5 h-3.5" />
                <span>Pratinjau Live</span>
            </button>
        </div>
    </div>

    <!-- TAB 1: Public Website Branding -->
    {#if activeTab === 'public'}
        <div class="space-y-6">
            <Card title="Public Website Branding" description="Pengaturan logo dan favicon untuk tampilan pengguna pengunjung website">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {#each publicAssetsKeys as key}
                        {#if branding[key]}
                            <BrandingUploader asset={branding[key]} recommendedSize={recommendedSizes[key]} />
                        {/if}
                    {/each}
                </div>
            </Card>
        </div>
    {/if}

    <!-- TAB 2: Admin Dashboard Branding -->
    {#if activeTab === 'admin'}
        <div class="space-y-6">
            <Card title="Admin Dashboard Branding" description="Pengaturan logo khusus untuk sidebar, loading screen, dan halaman login admin">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {#each adminAssetsKeys as key}
                        {#if branding[key]}
                            <BrandingUploader asset={branding[key]} recommendedSize={recommendedSizes[key]} />
                        {/if}
                    {/each}
                </div>
            </Card>
        </div>
    {/if}

    <!-- TAB 3: Live Preview Studio -->
    {#if activeTab === 'preview'}
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Live Preview & Branding Studio</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Simulasi tampilan tab browser, icon mobile, serta kontras mode terang & gelap</p>
                </div>

                <!-- Dark/Light Toggle -->
                <div class="inline-flex p-1 rounded-xl bg-slate-200 dark:bg-slate-800">
                    <button
                        type="button"
                        onclick={() => previewTheme = 'light'}
                        class={`inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold transition-all cursor-pointer ${
                            previewTheme === 'light' ? 'bg-white text-amber-600 shadow-xs' : 'text-slate-500'
                        }`}
                    >
                        <Sun class="w-3.5 h-3.5" />
                        <span>Light Mode</span>
                    </button>
                    <button
                        type="button"
                        onclick={() => previewTheme = 'dark'}
                        class={`inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold transition-all cursor-pointer ${
                            previewTheme === 'dark' ? 'bg-slate-900 text-indigo-400 shadow-xs' : 'text-slate-500'
                        }`}
                    >
                        <Moon class="w-3.5 h-3.5" />
                        <span>Dark Mode</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Browser Tab Preview -->
                <Card title="Pratinjau Browser Tab & Favicon" description="Simulasi tampilan judul halaman dan favicon di tab Google Chrome / Firefox">
                    <div class="p-4 rounded-xl bg-slate-200 dark:bg-slate-950 border border-slate-300 dark:border-slate-800">
                        <div class="flex items-center gap-2 px-3 py-2 rounded-t-lg bg-slate-100 dark:bg-slate-900 border-b border-slate-300 dark:border-slate-800 max-w-xs shadow-xs">
                            {#if branding.public_favicon?.url}
                                <img src={branding.public_favicon.url} alt="Favicon" class="w-4 h-4 object-contain" />
                            {:else}
                                <Globe class="w-4 h-4 text-indigo-500" />
                            {/if}
                            <span class="text-xs font-semibold truncate text-slate-800 dark:text-slate-200">
                                LaraSvelte — Modern CMS Platform
                            </span>
                        </div>
                        <div class="h-28 bg-white dark:bg-slate-900 rounded-b-lg p-4 flex items-center justify-center text-xs text-slate-400">
                            Halaman Web Utama
                        </div>
                    </div>
                </Card>

                <!-- Mobile Apple Touch Icon Preview -->
                <Card title="Pratinjau Icon Mobile / iOS Bookmark" description="Simulasi tampilan icon aplikasi saat disimpan ke layar utama smartphone">
                    <div class="p-6 rounded-xl bg-slate-900 text-white flex flex-col items-center justify-center space-y-3">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 p-0.5 shadow-xl flex items-center justify-center overflow-hidden">
                            {#if branding.public_apple_touch_icon?.url}
                                <img src={branding.public_apple_touch_icon.url} alt="Apple Touch Icon" class="w-full h-full object-cover rounded-2xl" />
                            {:else if branding.public_favicon?.url}
                                <img src={branding.public_favicon.url} alt="Favicon" class="w-10 h-10 object-contain" />
                            {:else}
                                <Smartphone class="w-8 h-8 text-white" />
                            {/if}
                        </div>
                        <span class="text-xs font-medium text-slate-300">LaraSvelte App</span>
                    </div>
                </Card>

                <!-- Public Header Logo Preview -->
                <Card title="Pratinjau Logo Header Website" description="Simulasi tampilan logo pada header navigasi publik">
                    <div class={`p-6 rounded-2xl border transition-colors ${
                        previewTheme === 'dark' ? 'bg-slate-950 border-slate-800 text-white' : 'bg-white border-slate-200 text-slate-900'
                    }`}>
                        <div class="flex items-center justify-between border-b border-slate-200/40 dark:border-slate-800/40 pb-4">
                            <div class="h-10 flex items-center">
                                {#if previewTheme === 'dark' && branding.public_logo_dark?.url}
                                    <img src={branding.public_logo_dark.url} alt="Logo Dark" class="h-8 object-contain" />
                                {:else if branding.public_logo_light?.url}
                                    <img src={branding.public_logo_light.url} alt="Logo Light" class="h-8 object-contain" />
                                {:else}
                                    <div class="flex items-center gap-2 font-bold text-lg text-indigo-600 dark:text-indigo-400">
                                        <Globe class="w-6 h-6" />
                                        <span>LaraSvelte</span>
                                    </div>
                                {/if}
                            </div>
                            <div class="flex items-center gap-4 text-xs font-semibold text-slate-500">
                                <span>Home</span>
                                <span>Blog</span>
                                <span>Kontak</span>
                            </div>
                        </div>
                    </div>
                </Card>

                <!-- Admin Dashboard Sidebar Logo Preview -->
                <Card title="Pratinjau Logo Admin Sidebar" description="Simulasi logo pada sidebar navigasi panel admin">
                    <div class={`p-6 rounded-2xl border transition-colors ${
                        previewTheme === 'dark' ? 'bg-slate-950 border-slate-800 text-white' : 'bg-white border-slate-200 text-slate-900'
                    }`}>
                        <div class="w-60 p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 space-y-4">
                            <div class="h-8 flex items-center">
                                {#if previewTheme === 'dark' && branding.admin_logo_dark?.url}
                                    <img src={branding.admin_logo_dark.url} alt="Admin Logo Dark" class="h-7 object-contain" />
                                {:else if branding.admin_logo_light?.url}
                                    <img src={branding.admin_logo_light.url} alt="Admin Logo Light" class="h-7 object-contain" />
                                {:else}
                                    <div class="flex items-center gap-2 font-bold text-base text-indigo-600 dark:text-indigo-400">
                                        <LayoutDashboard class="w-5 h-5" />
                                        <span>AdminPanel</span>
                                    </div>
                                {/if}
                            </div>
                            <div class="space-y-2 text-xs text-slate-500 font-medium">
                                <div class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 font-semibold">Dashboard</div>
                                <div class="p-2">Manajemen Konten</div>
                                <div class="p-2">Pengguna & Role</div>
                            </div>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    {/if}
</AppLayout>
