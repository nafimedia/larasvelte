<script lang="ts">
    import { useForm, router } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import Input from '@/Components/UI/Input.svelte';
    import Modal from '@/Components/UI/Modal.svelte';
    import FileUpload from '@/Components/UI/FileUpload.svelte';
    import { Image as ImageIcon, Upload, Trash2, Search, ExternalLink, Copy, Check } from 'lucide-svelte';

    interface Props {
        mediaFiles?: any;
        filters?: any;
    }

    let { mediaFiles, filters = {} }: Props = $props();

    let isUploadOpen = $state(false);
    let selectedMedia = $state<any>(null);
    let copiedUrl = $state(false);

    const uploadForm = useForm({
        file: null as File | null,
        alt_text: '',
        caption: '',
    });

    function submitUpload(e: Event) {
        e.preventDefault();
        uploadForm.post('/admin/cms/media/upload', {
            onSuccess: () => {
                isUploadOpen = false;
                uploadForm.reset();
            }
        });
    }

    function copyMediaUrl(url: string) {
        navigator.clipboard.writeText(url);
        copiedUrl = true;
        setTimeout(() => copiedUrl = false, 2000);
    }

    function deleteMedia(id: number) {
        if (confirm('Hapus file media ini? Tindakan ini tidak dapat dibatalkan.')) {
            router.delete(`/admin/cms/media/${id}`, {
                onSuccess: () => selectedMedia = null
            });
        }
    }
</script>

<AppLayout title="Media Library (Asset Manager)">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <ImageIcon class="w-5 h-5 text-indigo-500" />
                <span>Media Library & Asset Manager</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola berkas media gambar, dokumen PDF, dan aset digital website</p>
        </div>

        <Button variant="primary" size="md" onclick={() => isUploadOpen = true}>
            <Upload class="w-4 h-4 mr-1.5" />
            <span>Unggah Media Baru</span>
        </Button>
    </div>

    <!-- Media Grid -->
    <Card>
        {#if !mediaFiles?.data || mediaFiles.data.length === 0}
            <div class="p-12 text-center text-slate-500 space-y-3">
                <ImageIcon class="w-12 h-12 mx-auto text-slate-600" />
                <p class="text-xs">Belum ada file media yang diunggah di galeri Asset Manager.</p>
                <Button variant="outline" size="sm" onclick={() => isUploadOpen = true}>Unggah Gambar Pertama</Button>
            </div>
        {:else}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                {#each mediaFiles.data as item}
                    <button
                        type="button"
                        onclick={() => selectedMedia = item}
                        class="group relative rounded-xl border border-slate-800 bg-slate-900 overflow-hidden hover:border-indigo-500/50 transition-all text-left focus:ring-2 focus:ring-indigo-500"
                    >
                        <div class="h-28 w-full bg-slate-950 flex items-center justify-center overflow-hidden">
                            {#if item.mime_type?.startsWith('image/')}
                                <img src={item.path} alt={item.alt_text || item.original_name} class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            {:else}
                                <div class="text-indigo-400 font-mono text-[10px] font-bold uppercase">{item.mime_type || 'FILE'}</div>
                            {/if}
                        </div>
                        <div class="p-2.5 space-y-0.5">
                            <p class="text-[11px] font-bold text-white truncate">{item.original_name}</p>
                            <p class="text-[10px] text-slate-400 font-mono">{(item.size / 1024).toFixed(1)} KB</p>
                        </div>
                    </button>
                {/each}
            </div>
        {/if}
    </Card>

    <!-- Upload Modal -->
    <Modal bind:open={isUploadOpen} title="Unggah File Media Baru" description="Pilih berkas gambar atau dokumen dari komputer Anda">
        <form onsubmit={submitUpload} class="space-y-4">
            <input
                type="file"
                required
                onchange={(e: any) => uploadForm.file = e.target.files[0]}
                class="w-full p-3 rounded-xl border border-slate-800 bg-slate-900 text-xs text-slate-300 font-mono"
            />
            <Input label="Alt Text (SEO Image Description)" placeholder="Contoh: Banner ilustrasi FairuzKit" bind:value={uploadForm.alt_text} />
            <Input label="Caption / Keterangan" placeholder="Keterangan singkat gambar..." bind:value={uploadForm.caption} />
            <div class="pt-2 flex justify-end gap-2">
                <Button variant="outline" size="sm" onclick={() => isUploadOpen = false}>Batal</Button>
                <Button variant="primary" size="sm" type="submit" disabled={uploadForm.processing}>Unggah Sekarang</Button>
            </div>
        </form>
    </Modal>

    <!-- Detail View Modal -->
    {#if selectedMedia}
        <Modal open={true} title="Detail Media File" description={selectedMedia.original_name}>
            <div class="space-y-4 text-xs">
                <div class="h-48 w-full rounded-xl bg-slate-950 flex items-center justify-center overflow-hidden border border-slate-800">
                    <img src={selectedMedia.path} alt={selectedMedia.original_name} class="max-h-full max-w-full object-contain" />
                </div>

                <div class="space-y-2 font-mono p-3 rounded-xl bg-slate-900 border border-slate-800">
                    <div class="flex items-center justify-between text-slate-300">
                        <span>URL Media:</span>
                        <button type="button" onclick={() => copyMediaUrl(selectedMedia.path)} class="text-indigo-400 font-bold flex items-center gap-1">
                            {#if copiedUrl}<Check class="w-3.5 h-3.5 text-emerald-400" /> Tersalin{:else}<Copy class="w-3.5 h-3.5" /> Salin URL{/if}
                        </button>
                    </div>
                    <p class="text-[11px] text-indigo-300 truncate select-all">{selectedMedia.path}</p>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <Button variant="danger" size="sm" onclick={() => deleteMedia(selectedMedia.id)}>
                        <Trash2 class="w-4 h-4 mr-1" />
                        <span>Hapus File</span>
                    </Button>
                    <Button variant="outline" size="sm" onclick={() => selectedMedia = null}>Tutup</Button>
                </div>
            </div>
        </Modal>
    {/if}
</AppLayout>
