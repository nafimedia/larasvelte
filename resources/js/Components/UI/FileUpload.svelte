<script lang="ts">
    import { UploadCloud, X, Image as ImageIcon } from 'lucide-svelte';

    interface Props {
        file?: File | null;
        previewUrl?: string;
        label?: string;
        error?: string;
        accept?: string;
        maxSizeMB?: number;
        onchange?: (file: File | null) => void;
    }

    let {
        file = $bindable(null),
        previewUrl = '',
        label = 'Upload Image',
        error = '',
        accept = 'image/*',
        maxSizeMB = 2,
        onchange
    }: Props = $props();

    let isDragging = $state(false);
    let preview = $state('');

    $effect(() => {
        if (file) {
            preview = URL.createObjectURL(file);
        } else {
            preview = previewUrl;
        }
    });

    function handleFileSelected(selectedFile: File | null) {
        if (!selectedFile) return;

        if (selectedFile.size > maxSizeMB * 1024 * 1024) {
            alert(`Ukuran file melebihi batas ${maxSizeMB}MB`);
            return;
        }

        file = selectedFile;
        if (onchange) onchange(file);
    }

    function handleDrop(e: DragEvent) {
        e.preventDefault();
        isDragging = false;
        if (e.dataTransfer?.files && e.dataTransfer.files[0]) {
            handleFileSelected(e.dataTransfer.files[0]);
        }
    }

    function handleInputChange(e: Event) {
        const target = e.target as HTMLInputElement;
        if (target.files && target.files[0]) {
            handleFileSelected(target.files[0]);
        }
    }

    function removeFile() {
        file = null;
        preview = previewUrl;
        if (onchange) onchange(null);
    }
</script>

<div class="w-full space-y-1.5">
    {#if label}
        <span class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
            {label}
        </span>
    {/if}

    <div
        class={`relative border-2 border-dashed rounded-xl p-4 text-center transition-all ${
            isDragging
                ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-950/20'
                : 'border-slate-300 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 hover:bg-slate-100 dark:hover:bg-slate-900'
        }`}
        ondragover={(e) => { e.preventDefault(); isDragging = true; }}
        ondragleave={() => { isDragging = false; }}
        ondrop={handleDrop}
        role="region"
        aria-label="Upload Area"
    >
        {#if preview}
            <div class="relative inline-block group">
                <img
                    src={preview}
                    alt="Preview"
                    class="w-24 h-24 object-cover rounded-xl shadow-xs ring-2 ring-indigo-500/20"
                />
                <button
                    type="button"
                    onclick={removeFile}
                    class="absolute -top-2 -right-2 bg-rose-600 text-white p-1 rounded-full shadow-md hover:bg-rose-700 transition-colors"
                    title="Hapus foto"
                >
                    <X class="w-3.5 h-3.5" />
                </button>
            </div>
        {:else}
            <label class="flex flex-col items-center justify-center cursor-pointer py-2">
                <div class="p-3 bg-white dark:bg-slate-800 rounded-full shadow-xs text-indigo-600 dark:text-indigo-400 mb-2">
                    <UploadCloud class="w-6 h-6" />
                </div>
                <span class="text-xs font-medium text-slate-700 dark:text-slate-300">
                    <span class="text-indigo-600 dark:text-indigo-400 font-semibold">Pilih file</span> atau tarik file ke sini
                </span>
                <span class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">
                    PNG, JPG, WEBP hingga {maxSizeMB}MB
                </span>
                <input
                    type="file"
                    {accept}
                    class="hidden"
                    onchange={handleInputChange}
                />
            </label>
        {/if}
    </div>

    {#if error}
        <p class="text-xs text-rose-500 font-medium">{error}</p>
    {/if}
</div>
