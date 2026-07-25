<script lang="ts">
    import { onMount } from 'svelte';
    import { Sun, Moon, Monitor, Check } from 'lucide-svelte';
    import { getTheme, setTheme, applyTheme, type Theme } from '@/lib/theme';

    interface Props {
        class?: string;
    }

    let { class: className = '' }: Props = $props();

    let currentTheme = $state<Theme>('system');
    let isOpen = $state(false);

    onMount(() => {
        currentTheme = getTheme();
        applyTheme(currentTheme);

        // Listen for system theme changes if set to system
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        const handleChange = () => {
            if (getTheme() === 'system') {
                applyTheme('system');
            }
        };

        mediaQuery.addEventListener('change', handleChange);
        return () => mediaQuery.removeEventListener('change', handleChange);
    });

    function selectTheme(theme: Theme) {
        currentTheme = theme;
        setTheme(theme);
        isOpen = false;
    }
</script>

<div class={`relative inline-block text-left ${className}`}>
    <!-- Toggle Button -->
    <button
        type="button"
        onclick={() => isOpen = !isOpen}
        class="flex items-center justify-center p-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-500/50 transition-all shadow-xs"
        title="Ubah Tema Tampilan (Light / Dark / System)"
        aria-label="Toggle Theme"
    >
        {#if currentTheme === 'light'}
            <Sun class="w-4 h-4 text-amber-500 transition-transform duration-300 rotate-0 hover:rotate-45" />
        {:else if currentTheme === 'dark'}
            <Moon class="w-4 h-4 text-indigo-400 transition-transform duration-300 rotate-0 hover:-rotate-12" />
        {:else}
            <Monitor class="w-4 h-4 text-slate-400 transition-transform duration-300 hover:scale-110" />
        {/if}
    </button>

    <!-- Dropdown Menu -->
    {#if isOpen}
        <button
            type="button"
            tabindex="-1"
            class="fixed inset-0 z-40 w-full h-full border-none cursor-default bg-transparent"
            onclick={() => isOpen = false}
            aria-label="Close theme menu"
        ></button>

        <div class="absolute right-0 mt-2 w-40 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl z-50 p-1.5 space-y-1 text-xs font-semibold">
            <button
                type="button"
                onclick={() => selectTheme('light')}
                class={`w-full flex items-center justify-between px-3 py-2 rounded-lg transition-all ${
                    currentTheme === 'light'
                        ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 font-bold'
                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                }`}
            >
                <div class="flex items-center gap-2">
                    <Sun class="w-3.5 h-3.5 text-amber-500" />
                    <span>Mode Terang</span>
                </div>
                {#if currentTheme === 'light'}
                    <Check class="w-3.5 h-3.5 text-amber-500" />
                {/if}
            </button>

            <button
                type="button"
                onclick={() => selectTheme('dark')}
                class={`w-full flex items-center justify-between px-3 py-2 rounded-lg transition-all ${
                    currentTheme === 'dark'
                        ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-bold'
                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                }`}
            >
                <div class="flex items-center gap-2">
                    <Moon class="w-3.5 h-3.5 text-indigo-400" />
                    <span>Mode Gelap</span>
                </div>
                {#if currentTheme === 'dark'}
                    <Check class="w-3.5 h-3.5 text-indigo-400" />
                {/if}
            </button>

            <button
                type="button"
                onclick={() => selectTheme('system')}
                class={`w-full flex items-center justify-between px-3 py-2 rounded-lg transition-all ${
                    currentTheme === 'system'
                        ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold'
                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                }`}
            >
                <div class="flex items-center gap-2">
                    <Monitor class="w-3.5 h-3.5 text-slate-400" />
                    <span>Sistem Otomatis</span>
                </div>
                {#if currentTheme === 'system'}
                    <Check class="w-3.5 h-3.5 text-slate-400" />
                {/if}
            </button>
        </div>
    {/if}
</div>
