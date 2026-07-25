<script lang="ts">
    import { cn } from '@/lib/utils';
    import type { Snippet } from 'svelte';
    import type { HTMLButtonAttributes } from 'svelte/elements';

    interface Props extends HTMLButtonAttributes {
        type?: 'button' | 'submit' | 'reset';
        variant?: 'primary' | 'secondary' | 'danger' | 'ghost' | 'outline' | 'success';
        size?: 'sm' | 'md' | 'lg' | 'icon';
        disabled?: boolean;
        loading?: boolean;
        class?: string;
        children?: Snippet;
        onclick?: (e: MouseEvent) => void;
    }

    let {
        type = 'button',
        variant = 'primary',
        size = 'md',
        disabled = false,
        loading = false,
        class: className = '',
        children,
        onclick,
        ...restProps
    }: Props = $props();

    const baseStyle = 'inline-flex items-center justify-center font-medium rounded-lg transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed active:scale-[0.98] cursor-pointer';

    const variants = {
        primary: 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm shadow-indigo-500/20 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600',
        secondary: 'bg-slate-100 hover:bg-slate-200 text-slate-900 focus:ring-slate-400 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-100',
        danger: 'bg-rose-600 hover:bg-rose-700 text-white shadow-sm shadow-rose-500/20 focus:ring-rose-500 dark:bg-rose-500 dark:hover:bg-rose-600',
        success: 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm shadow-emerald-500/20 focus:ring-emerald-500 dark:bg-emerald-500 dark:hover:bg-emerald-600',
        outline: 'border border-slate-300 hover:bg-slate-100 text-slate-700 focus:ring-slate-400 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800',
        ghost: 'hover:bg-slate-100 text-slate-700 focus:ring-slate-400 dark:hover:bg-slate-800 dark:text-slate-300'
    };

    const sizes = {
        sm: 'px-3 py-1.5 text-xs font-semibold gap-1.5',
        md: 'px-4 py-2 text-sm gap-2',
        lg: 'px-5 py-2.5 text-base font-semibold gap-2.5',
        icon: 'p-2 rounded-lg'
    };
</script>

<button
    {type}
    {disabled}
    {onclick}
    class={cn(baseStyle, variants[variant], sizes[size], className)}
    {...restProps}
>
    {#if children}
        {@render children()}
    {/if}
</button>
