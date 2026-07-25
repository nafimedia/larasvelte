<script lang="ts">
    import { cn } from '@/lib/utils';

    interface Props {
        src?: string;
        alt?: string;
        size?: 'sm' | 'md' | 'lg' | 'xl';
        class?: string;
    }

    let {
        src,
        alt = 'User Avatar',
        size = 'md',
        class: className = ''
    }: Props = $props();

    const sizes = {
        sm: 'w-8 h-8 text-xs',
        md: 'w-10 h-10 text-sm',
        lg: 'w-14 h-14 text-base',
        xl: 'w-20 h-20 text-lg'
    };

    const fallbackSrc = $derived(
        src || `https://ui-avatars.com/api/?name=${encodeURIComponent(alt)}&color=6366F1&background=EEF2FF`
    );
</script>

<img
    src={fallbackSrc}
    {alt}
    class={cn('rounded-full object-cover ring-2 ring-white dark:ring-slate-800 shadow-xs', sizes[size], className)}
    onerror={(e) => {
        (e.target as HTMLImageElement).src = `https://ui-avatars.com/api/?name=${encodeURIComponent(alt)}&color=6366F1&background=EEF2FF`;
    }}
/>
