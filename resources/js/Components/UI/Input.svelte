<script lang="ts">
    import { cn } from '@/lib/utils';

    interface Props {
        id?: string;
        name?: string;
        type?: string;
        value?: string | number;
        placeholder?: string;
        label?: string;
        error?: string;
        required?: boolean;
        disabled?: boolean;
        class?: string;
        oninput?: (e: Event) => void;
    }

    let {
        id,
        name,
        type = 'text',
        value = $bindable(''),
        placeholder = '',
        label,
        error,
        required = false,
        disabled = false,
        class: className = '',
        oninput
    }: Props = $props();
</script>

<div class="w-full space-y-1.5">
    {#if label}
        <label for={id} class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
            {label}
            {#if required}<span class="text-rose-500 ml-0.5">*</span>{/if}
        </label>
    {/if}
    <input
        {id}
        {name}
        {type}
        bind:value
        {placeholder}
        {required}
        {disabled}
        {oninput}
        class={cn(
            'w-full px-3.5 py-2 text-sm rounded-lg border bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-all disabled:opacity-50',
            error && 'border-rose-500 focus:ring-rose-500/30 focus:border-rose-500',
            className
        )}
    />
    {#if error}
        <p class="text-xs text-rose-500 font-medium">{error}</p>
    {/if}
</div>
