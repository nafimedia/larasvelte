<script lang="ts">
    import Modal from './Modal.svelte';
    import Button from './Button.svelte';
    import { AlertTriangle, Info, CheckCircle2 } from 'lucide-svelte';

    interface Props {
        open?: boolean;
        title?: string;
        message?: string;
        type?: 'danger' | 'warning' | 'info' | 'success';
        confirmText?: string;
        cancelText?: string;
        onconfirm?: () => void;
        oncancel?: () => void;
    }

    let {
        open = $bindable(false),
        title = 'Konfirmasi Tindakan',
        message = 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
        type = 'danger',
        confirmText = 'Ya, Lanjutkan',
        cancelText = 'Batal',
        onconfirm,
        oncancel
    }: Props = $props();

    function handleConfirm() {
        open = false;
        if (onconfirm) onconfirm();
    }

    function handleCancel() {
        open = false;
        if (oncancel) oncancel();
    }

    const typeIcons = {
        danger: AlertTriangle,
        warning: AlertTriangle,
        info: Info,
        success: CheckCircle2
    };

    const iconColors = {
        danger: 'text-rose-500 bg-rose-50 dark:bg-rose-950/50',
        warning: 'text-amber-500 bg-amber-50 dark:bg-amber-950/50',
        info: 'text-sky-500 bg-sky-50 dark:bg-sky-950/50',
        success: 'text-emerald-500 bg-emerald-50 dark:bg-emerald-950/50'
    };
</script>

<Modal bind:open maxWidth="sm" onclose={handleCancel}>
    <div class="flex items-start gap-4">
        <div class={`p-3 rounded-full shrink-0 ${iconColors[type]}`}>
            {#if type === 'danger' || type === 'warning'}
                <AlertTriangle class="w-6 h-6" />
            {:else if type === 'info'}
                <Info class="w-6 h-6" />
            {:else}
                <CheckCircle2 class="w-6 h-6" />
            {/if}
        </div>
        <div>
            <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">{title}</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{message}</p>
        </div>
    </div>

    {#snippet footer()}
        <Button variant="outline" size="sm" onclick={handleCancel}>{cancelText}</Button>
        <Button variant={type === 'danger' ? 'danger' : 'primary'} size="sm" onclick={handleConfirm}>{confirmText}</Button>
    {/snippet}
</Modal>
