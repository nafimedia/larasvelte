<script lang="ts">
    import { cn } from '@/lib/utils';
    import type { Snippet } from 'svelte';
    import { X } from 'lucide-svelte';

    interface Props {
        open?: boolean;
        title?: string;
        description?: string;
        maxWidth?: 'sm' | 'md' | 'lg' | 'xl' | '2xl';
        children?: Snippet;
        footer?: Snippet;
        onclose?: () => void;
    }

    let {
        open = $bindable(false),
        title,
        description,
        maxWidth = 'md',
        children,
        footer,
        onclose
    }: Props = $props();

    function handleClose() {
        open = false;
        if (onclose) onclose();
    }

    function handleKeydown(e: KeyboardEvent) {
        if (e.key === 'Escape' && open) {
            handleClose();
        }
    }

    const widths = {
        sm: 'max-w-sm',
        md: 'max-w-md',
        lg: 'max-w-lg',
        xl: 'max-w-xl',
        '2xl': 'max-w-2xl'
    };
</script>

<svelte:window onkeydown={handleKeydown} />

{#if open}
    <!-- Backdrop Overlay -->
    <button
        type="button"
        aria-label="Tutup modal"
        class="fixed inset-0 z-50 w-full h-full bg-slate-950/60 backdrop-blur-xs transition-all animate-in fade-in duration-200 cursor-default border-none outline-none"
        onclick={handleClose}
    ></button>

    <!-- Modal Box -->
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto pointer-events-none">
        <div
            class={cn(
                'w-full bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 transform transition-all animate-in zoom-in-95 duration-200 overflow-hidden pointer-events-auto',
                widths[maxWidth]
            )}
        >
            <!-- Header -->
            {#if title || description}
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-start justify-between gap-4">
                    <div>
                        {#if title}
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">{title}</h2>
                        {/if}
                        {#if description}
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{description}</p>
                        {/if}
                    </div>
                    <button
                        type="button"
                        onclick={handleClose}
                        class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                    >
                        <X class="w-5 h-5" />
                    </button>
                </div>
            {/if}

            <!-- Body -->
            <div class="p-6">
                {#if children}
                    {@render children()}
                {/if}
            </div>

            <!-- Footer -->
            {#if footer}
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                    {@render footer()}
                </div>
            {/if}
        </div>
    </div>
{/if}
