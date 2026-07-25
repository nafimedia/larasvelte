<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import Navbar from '@/Components/Layout/Navbar.svelte';
    import Sidebar from '@/Components/Layout/Sidebar.svelte';
    import AutoLogout from '@/Components/UI/AutoLogout.svelte';
    import { Toaster, toast } from 'svelte-sonner';
    import type { Snippet } from 'svelte';
    import type { PageProps } from '@/lib/types';

    interface Props {
        title?: string;
        children?: Snippet;
    }

    let { title = 'Dashboard', children }: Props = $props();

    let isSidebarOpen = $state(false);

    const pageProps = $derived(page.props as unknown as PageProps);
    const site = $derived(pageProps.site);
    const branding = $derived(pageProps.branding);
    const flash = $derived(pageProps.flash);

    $effect(() => {
        if (flash?.success) {
            toast.success(flash.success);
        }
        if (flash?.error) {
            toast.error(flash.error);
        }
        if (flash?.warning) {
            toast.warning(flash.warning);
        }
        if (flash?.info) {
            toast.info(flash.info);
        }
    });
</script>

<svelte:head>
    <title>{title} - {site?.name || 'LaraSvelte'}</title>
    {#if branding?.admin_favicon || branding?.public_favicon}
        <link rel="icon" href={branding?.admin_favicon || branding?.public_favicon} />
    {/if}
    {#if branding?.public_apple_touch_icon}
        <link rel="apple-touch-icon" href={branding.public_apple_touch_icon} />
    {/if}
</svelte:head>

<div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 flex flex-col font-sans transition-colors">
    <!-- Auto Logout Handler for Inactive Users (30 mins) -->
    <AutoLogout timeoutMinutes={30} warningMinutes={2} />

    <!-- Sonner Toaster -->
    <Toaster position="top-right" richColors closeButton />

    <!-- Sidebar -->
    <Sidebar isOpen={isSidebarOpen} onClose={() => isSidebarOpen = false} />

    <!-- Main Content Wrapper -->
    <div class="lg:pl-64 flex flex-col flex-1 min-h-screen">
        <!-- Navbar -->
        <Navbar onToggleSidebar={() => isSidebarOpen = !isSidebarOpen} />

        <!-- Main Body Page Area -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto space-y-6">
            {#if children}
                {@render children()}
            {/if}
        </main>

        <!-- Footer -->
        <footer class="py-4 px-6 border-t border-slate-200 dark:border-slate-800 text-center text-xs text-slate-400 dark:text-slate-500">
            &copy; {new Date().getFullYear()} LaraSvelte Starterkit. Built with Laravel 13, Svelte 5 & Tailwind CSS v4.
        </footer>
    </div>
</div>
