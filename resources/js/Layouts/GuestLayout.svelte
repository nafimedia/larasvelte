<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import ThemeToggle from '@/Components/UI/ThemeToggle.svelte';
    import { Toaster, toast } from 'svelte-sonner';
    import type { Snippet } from 'svelte';
    import type { PageProps } from '@/lib/types';

    interface Props {
        title?: string;
        children?: Snippet;
    }

    let { title = 'Authentication', children }: Props = $props();

    const pageProps = $derived(page.props as unknown as PageProps);
    const site = $derived(pageProps.site);
    const branding = $derived(pageProps.branding);
    const flash = $derived(pageProps.flash);

    $effect(() => {
        if (flash?.success) toast.success(flash.success);
        if (flash?.error) toast.error(flash.error);
        if (flash?.info) toast.info(flash.info);
    });
</script>

<svelte:head>
    <title>{title} - {site.name}</title>
    {#if branding?.admin_favicon || branding?.public_favicon}
        <link rel="icon" href={branding?.admin_favicon || branding?.public_favicon} />
    {/if}
    {#if branding?.public_apple_touch_icon}
        <link rel="apple-touch-icon" href={branding.public_apple_touch_icon} />
    {/if}
</svelte:head>

<div class="min-h-screen bg-gradient-to-br from-slate-100 via-indigo-50/30 to-slate-200 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 flex flex-col justify-between p-4 sm:p-6 transition-colors">
    <Toaster position="top-right" richColors />

    <!-- Top Bar -->
    <div class="flex items-center justify-between max-w-md w-full mx-auto">
        <div class="flex items-center gap-2">
            {#if branding?.admin_login_logo}
                <img src={branding.admin_login_logo} alt={site.name} class="h-8 object-contain" />
            {:else if branding?.admin_logo_dark || branding?.admin_logo_light}
                <img src={branding.admin_logo_dark} alt={site.name} class="h-8 object-contain hidden dark:block" />
                <img src={branding.admin_logo_light || branding.admin_logo_dark} alt={site.name} class={`h-8 object-contain ${branding.admin_logo_dark ? 'dark:hidden' : ''}`} />
            {:else}
                <div class="w-8 h-8 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-md shadow-indigo-500/20">
                    LS
                </div>
                <span class="font-bold text-slate-900 dark:text-slate-100">{site.name}</span>
            {/if}
        </div>
        <ThemeToggle />
    </div>

    <!-- Centered Card Container -->
    <div class="w-full max-w-md mx-auto my-auto py-8">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl p-6 sm:p-8 backdrop-blur-md">
            {#if children}
                {@render children()}
            {/if}
        </div>
    </div>

    <!-- Bottom Footer -->
    <div class="text-center text-xs text-slate-400 dark:text-slate-500">
        {site.name} &bull; Powered by Svelte 5 & Laravel 13
    </div>
</div>
