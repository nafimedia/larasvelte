<script lang="ts">
    import { cn } from '@/lib/utils';
    import type { Snippet } from 'svelte';

    interface Props {
        class?: string;
        title?: string;
        description?: string;
        headerAction?: Snippet;
        children?: Snippet;
        footer?: Snippet;
    }

    let {
        class: className = '',
        title,
        description,
        headerAction,
        children,
        footer
    }: Props = $props();
</script>

<div class={cn('bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden transition-all', className)}>
    {#if title || description || headerAction}
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-4">
            <div>
                {#if title}
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">{title}</h3>
                {/if}
                {#if description}
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{description}</p>
                {/if}
            </div>
            {#if headerAction}
                <div>
                    {@render headerAction()}
                </div>
            {/if}
        </div>
    {/if}

    <div class="p-6">
        {#if children}
            {@render children()}
        {/if}
    </div>

    {#if footer}
        <div class="px-6 py-3.5 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-800/80">
            {@render footer()}
        </div>
    {/if}
</div>
