<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import { toast } from 'svelte-sonner';
    import Button from './Button.svelte';
    import Dialog from './Dialog.svelte';
    import { UploadCloud, Trash2, Image as ImageIcon, CheckCircle, AlertCircle, RefreshCw } from 'lucide-svelte';
    import type { BrandingAsset } from '@/lib/types';

    interface Props {
        asset: BrandingAsset;
        recommendedSize?: string;
        acceptedFormats?: string;
    }

    let { asset, recommendedSize, acceptedFormats = '.png, .jpg, .jpeg, .svg, .webp, .ico' }: Props = $props();

    let isDragging = $state(false);
    let selectedFile = $state<File | null>(null);
    let previewUrl = $state<string | null>(null);
    let isUploading = $state(false);
    let showDeleteConfirm = $state(false);

    $effect(() => {
        if (!selectedFile) {
            previewUrl = asset.url;
        }
    });

    function handleFileSelect(file: File) {
        if (!file) return;

        // Validation
        const validExtensions = ['png', 'jpg', 'jpeg', 'svg', 'webp', 'ico'];
        const ext = file.name.split('.').pop()?.toLowerCase();

        if (!ext || !validExtensions.includes(ext)) {
            toast.error(`Format berkas .${ext} tidak didukung. Silakan gunakan PNG, JPG, SVG, WebP, atau ICO.`);
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            toast.error('Ukuran berkas melebihi batas maksimal 2MB.');
            return;
        }

        selectedFile = file;
        previewUrl = URL.createObjectURL(file);
    }

    function handleDrop(e: DragEvent) {
        e.preventDefault();
        isDragging = false;
        if (e.dataTransfer?.files && e.dataTransfer.files.length > 0) {
            handleFileSelect(e.dataTransfer.files[0]);
        }
    }

    function handleInputChange(e: Event) {
        const target = e.target as HTMLInputElement;
        if (target.files && target.files.length > 0) {
            handleFileSelect(target.files[0]);
        }
    }

    function uploadFile() {
        if (!selectedFile) return;

        isUploading = true;
        const formData = new FormData();
        formData.append('key', asset.key);
        formData.append('file', selectedFile);

        router.post('/admin/settings/branding/upload', formData, {
            onSuccess: () => {
                isUploading = false;
                selectedFile = null;
                toast.success(`Aset ${asset.label} berhasil disimpan!`);
            },
            onError: (err) => {
                isUploading = false;
                toast.error(Object.values(err)[0] || 'Gagal mengunggah berkas.');
            }
        });
    }

    function cancelSelection() {
        selectedFile = null;
        previewUrl = asset.url;
    }

    function confirmDelete() {
        showDeleteConfirm = true;
    }

    function executeDelete() {
        router.delete(`/admin/settings/branding/${asset.key}`, {
            onSuccess: () => {
                showDeleteConfirm = false;
                selectedFile = null;
                previewUrl = null;
                toast.success(`Aset ${asset.label} berhasil dihapus.`);
            }
        });
    }
</script>

<div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between space-y-4">
    <!-- Header info -->
    <div>
        <div class="flex items-start justify-between gap-2">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">{asset.label}</h3>
            {#if recommendedSize}
                <span class="px-2 py-0.5 text-[10px] font-mono font-medium rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 shrink-0">
                    {recommendedSize}
                </span>
            {/if}
        </div>
        {#if asset.description}
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{asset.description}</p>
        {/if}
    </div>

    <!-- Preview & Drop Area -->
    <div
        class={`relative rounded-xl border-2 border-dashed p-4 flex flex-col items-center justify-center transition-all ${
            isDragging
                ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-950/20'
                : 'border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-950/40'
        }`}
        ondragover={(e) => { e.preventDefault(); isDragging = true; }}
        ondragleave={() => isDragging = false}
        ondrop={handleDrop}
        role="region"
        aria-label="Area drag and drop gambar"
    >
        {#if previewUrl}
            <!-- Image preview box -->
            <div class="relative group flex flex-col items-center justify-center py-2 space-y-2">
                <div class="p-3 rounded-xl bg-white/80 dark:bg-slate-900/80 border border-slate-200/80 dark:border-slate-800/80 shadow-xs flex items-center justify-center max-h-24 max-w-full overflow-hidden">
                    <img src={previewUrl} alt={asset.label} class="max-h-20 max-w-full object-contain" />
                </div>
                {#if selectedFile}
                    <span class="text-[10px] text-amber-600 dark:text-amber-400 font-medium animate-pulse">
                        Pratinjau sebelum simpan
                    </span>
                {/if}
            </div>
        {:else}
            <!-- Empty state -->
            <div class="py-6 flex flex-col items-center text-center space-y-2 text-slate-400 dark:text-slate-500">
                <ImageIcon class="w-8 h-8 stroke-1" />
                <p class="text-xs font-medium">Belum ada logo/icon diunggah</p>
            </div>
        {/if}

        <label class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors cursor-pointer shadow-2xs">
            <UploadCloud class="w-3.5 h-3.5 text-indigo-500" />
            <span>{previewUrl ? 'Ganti Logo' : 'Pilih Berkas'}</span>
            <input type="file" accept={acceptedFormats} onchange={handleInputChange} class="hidden" />
        </label>
    </div>

    <!-- Actions Footer -->
    <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
        <div>
            {#if selectedFile}
                <button
                    type="button"
                    onclick={cancelSelection}
                    class="text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 underline cursor-pointer"
                >
                    Batal
                </button>
            {:else if asset.url}
                <button
                    type="button"
                    onclick={confirmDelete}
                    class="inline-flex items-center gap-1 text-xs text-rose-500 hover:text-rose-600 font-medium cursor-pointer"
                >
                    <Trash2 class="w-3.5 h-3.5" />
                    <span>Hapus</span>
                </button>
            {/if}
        </div>

        {#if selectedFile}
            <Button variant="primary" size="sm" onclick={uploadFile} disabled={isUploading}>
                {#if isUploading}
                    <RefreshCw class="w-3.5 h-3.5 animate-spin" />
                    <span>Mengunggah...</span>
                {:else}
                    <CheckCircle class="w-3.5 h-3.5" />
                    <span>Simpan Logo</span>
                {/if}
            </Button>
        {/if}
    </div>
</div>

<!-- Delete Confirmation Modal -->
<Dialog
    open={showDeleteConfirm}
    title={`Hapus ${asset.label}`}
    message={`Apakah Anda yakin ingin menghapus aset ${asset.label}? Tindakan ini akan mengembalikan logo ke tampilan default.`}
    type="danger"
    confirmText="Ya, Hapus Logo"
    cancelText="Batal"
    onconfirm={executeDelete}
    oncancel={() => showDeleteConfirm = false}
/>
