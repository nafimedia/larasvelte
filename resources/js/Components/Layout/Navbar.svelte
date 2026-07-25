<script lang="ts">
    import { page, Link } from '@inertiajs/svelte';
    import ThemeToggle from '../UI/ThemeToggle.svelte';
    import Avatar from '../UI/Avatar.svelte';
    import { Menu, LogOut, User as UserIcon, Shield, ChevronDown } from 'lucide-svelte';
    import type { PageProps } from '@/lib/types';

    interface Props {
        onToggleSidebar?: () => void;
    }

    let { onToggleSidebar }: Props = $props();

    let isUserMenuOpen = $state(false);

    const pageProps = $derived(page.props as unknown as PageProps);
    const user = $derived(pageProps.auth.user);
    const site = $derived(pageProps.site);
    const branding = $derived(pageProps.branding);
</script>

<header class="sticky top-0 z-30 h-16 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 flex items-center justify-between transition-colors">
    <!-- Left Section: Toggle Sidebar & App Title -->
    <div class="flex items-center gap-3">
        <button
            type="button"
            onclick={onToggleSidebar}
            class="p-2 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors lg:hidden cursor-pointer"
            aria-label="Toggle Sidebar"
        >
            <Menu class="w-5 h-5" />
        </button>

        <div class="flex items-center gap-2">
            {#if branding?.admin_logo_dark || branding?.admin_logo_light}
                <div class="h-8 flex items-center">
                    {#if branding.admin_logo_dark}
                        <img src={branding.admin_logo_dark} alt={site.name} class="h-7 object-contain hidden dark:block" />
                    {/if}
                    <img src={branding.admin_logo_light || branding.admin_logo_dark} alt={site.name} class={`h-7 object-contain ${branding.admin_logo_dark ? 'dark:hidden' : ''}`} />
                </div>
            {:else}
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white font-bold text-sm shadow-md shadow-indigo-500/20">
                    {site.name.substring(0, 2).toUpperCase()}
                </div>
                <span class="font-bold text-base text-slate-900 dark:text-slate-100 tracking-tight hidden sm:inline-block">
                    {site.name}
                </span>
            {/if}
        </div>
    </div>

    <!-- Right Section: Theme Toggle & User Menu Dropdown -->
    <div class="flex items-center gap-2">
        <ThemeToggle />

        {#if user}
            <div class="relative">
                <button
                    type="button"
                    onclick={() => isUserMenuOpen = !isUserMenuOpen}
                    class="flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer border-none outline-none"
                >
                    <Avatar src={user.avatar_url} alt={user.name} size="sm" />
                    <div class="text-left hidden md:block leading-tight">
                        <p class="text-xs font-semibold text-slate-900 dark:text-slate-100">{user.name}</p>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 capitalize">{user.roles[0] ?? 'User'}</p>
                    </div>
                    <ChevronDown class="w-4 h-4 text-slate-400 hidden md:block" />
                </button>

                {#if isUserMenuOpen}
                    <!-- Backdrop -->
                    <button
                        type="button"
                        aria-label="Tutup menu pengguna"
                        class="fixed inset-0 z-40 w-full h-full border-none outline-none cursor-default bg-transparent"
                        onclick={() => isUserMenuOpen = false}
                    ></button>

                    <!-- Dropdown Content -->
                    <div class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 rounded-xl shadow-xl border border-slate-200 dark:border-slate-800 z-50 py-2 animate-in fade-in zoom-in-95 duration-150">
                        <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-800">
                            <p class="text-xs font-semibold text-slate-900 dark:text-slate-100">{user.name}</p>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate mt-0.5">{user.email}</p>
                        </div>

                        <div class="py-1">
                            <Link
                                href="/profile"
                                class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
                                onclick={() => isUserMenuOpen = false}
                            >
                                <UserIcon class="w-4 h-4 text-slate-400" />
                                <span>Profil Saya</span>
                            </Link>
                        </div>

                        <div class="border-t border-slate-100 dark:border-slate-800 pt-1">
                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors text-left"
                            >
                                <LogOut class="w-4 h-4 text-rose-500" />
                                <span>Keluar Akun</span>
                            </Link>
                        </div>
                    </div>
                {/if}
            </div>
        {/if}
    </div>
</header>
