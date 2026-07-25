<script lang="ts">
    import { useForm, router } from '@inertiajs/svelte';
    import { toast } from 'svelte-sonner';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import Input from '@/Components/UI/Input.svelte';
    import Dialog from '@/Components/UI/Dialog.svelte';
    import {
        FileSpreadsheet,
        Plus,
        ExternalLink,
        Copy,
        PenLine,
        Trash2,
        MessageSquare,
        CheckCircle2,
        XCircle,
        Search,
        Sparkles
    } from 'lucide-svelte';
    import type { FormItem } from '@/lib/types';

    interface Props {
        forms: FormItem[];
    }

    let { forms = [] }: Props = $props();

    let isCreateOpen = $state(false);
    let searchQuery = $state('');

    const createForm = useForm({
        title: '',
        description: '',
        theme_color: '#6366F1',
    });

    const filteredForms = $derived(
        forms.filter(f =>
            f.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
            (f.description && f.description.toLowerCase().includes(searchQuery.toLowerCase()))
        )
    );

    function submitCreate(e: Event) {
        e.preventDefault();
        $createForm.post('/admin/cms/forms', {
            onSuccess: () => {
                isCreateOpen = false;
                $createForm.reset();
            },
        });
    }

    function copyPublicUrl(slug: string) {
        const url = `${window.location.origin}/f/${slug}`;
        navigator.clipboard.writeText(url);
        toast.success('URL Formulir Publik berhasil disalin ke clipboard!');
    }

    function deleteForm(formItem: FormItem) {
        if (confirm(`Apakah Anda yakin ingin menghapus formulir '${formItem.title}' beserta seluruh tanggapannya?`)) {
            router.delete(`/admin/cms/forms/${formItem.id}`, {
                onSuccess: () => toast.success('Formulir berhasil dihapus!'),
            });
        }
    }
</script>

<AppLayout title="Dynamic Form Builder Studio">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <FileSpreadsheet class="w-5 h-5 text-indigo-500" />
                <span>Form Builder Studio</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Kelola dan buat formulir interaktif ala Google Forms untuk kontak, survei, dan pendaftaran
            </p>
        </div>

        <Button onclick={() => isCreateOpen = true} class="cursor-pointer gap-2 shrink-0">
            <Plus class="w-4 h-4" />
            <span>Buat Formulir Baru</span>
        </Button>
    </div>

    <!-- Search & Filters -->
    <div class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="relative w-full sm:w-80">
            <Search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
            <input
                type="text"
                placeholder="Cari formulir..."
                bind:value={searchQuery}
                class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
            />
        </div>

        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">
            Total: {forms.length} Formulir
        </div>
    </div>

    <!-- Forms Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {#each filteredForms as formItem (formItem.id)}
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between space-y-4 hover:shadow-md transition-shadow">
                <div class="space-y-3">
                    <!-- Top Bar: Accent indicator & Responses Badge -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span
                                class="w-3 h-3 rounded-full shrink-0 shadow-xs"
                                style="background-color: {formItem.theme_color || '#6366F1'}"
                            ></span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                {formItem.slug}
                            </span>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                <MessageSquare class="w-3 h-3" />
                                <span>{formItem.submissions_count ?? formItem.submissions?.length ?? 0} Respon</span>
                            </span>

                            {#if formItem.is_accepting_responses}
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800" title="Menerima Tanggapan">
                                    <CheckCircle2 class="w-3 h-3" />
                                </span>
                            {:else}
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800" title="Ditutup">
                                    <XCircle class="w-3 h-3" />
                                </span>
                            {/if}
                        </div>
                    </div>

                    <!-- Title & Description -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 line-clamp-1">
                            {formItem.title}
                        </h3>
                        {#if formItem.description}
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2 leading-relaxed">
                                {formItem.description}
                            </p>
                        {/if}
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1">
                        <button
                            type="button"
                            onclick={() => copyPublicUrl(formItem.slug)}
                            class="p-2 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 transition-colors cursor-pointer"
                            title="Salin URL Publik"
                        >
                            <Copy class="w-4 h-4" />
                        </button>
                        <a
                            href="/f/{formItem.slug}"
                            target="_blank"
                            class="p-2 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 transition-colors"
                            title="Buka Pratinjau Publik"
                        >
                            <ExternalLink class="w-4 h-4" />
                        </a>
                        <button
                            type="button"
                            onclick={() => deleteForm(formItem)}
                            class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition-colors cursor-pointer"
                            title="Hapus Formulir"
                        >
                            <Trash2 class="w-4 h-4" />
                        </button>
                    </div>

                    <a
                        href="/admin/cms/forms/{formItem.id}/builder"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs transition-colors"
                    >
                        <PenLine class="w-3.5 h-3.5" />
                        <span>Edit di Studio</span>
                    </a>
                </div>
            </div>
        {:else}
            <div class="col-span-full py-12 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-3">
                <FileSpreadsheet class="w-10 h-10 text-slate-400 mx-auto stroke-1" />
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada formulir</p>
                <p class="text-xs text-slate-400">Buat formulir pertama Anda untuk mulai mengumpulkan respon</p>
                <Button onclick={() => isCreateOpen = true} class="cursor-pointer gap-2 mx-auto">
                    <Plus class="w-4 h-4" />
                    <span>Buat Formulir Baru</span>
                </Button>
            </div>
        {/each}
    </div>

    <!-- Create Form Modal Dialog -->
    <Dialog open={isCreateOpen} onClose={() => isCreateOpen = false} title="Buat Formulir Baru">
        <form onsubmit={submitCreate} class="space-y-4">
            <Input
                label="Judul Formulir"
                placeholder="mis. Formulir Pendaftaran Event 2026"
                bind:value={$createForm.title}
                error={$createForm.errors.title}
                required
            />

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                    Deskripsi Singkat (Opsional)
                </label>
                <textarea
                    placeholder="Berikan petunjuk atau latar belakang pengisian formulir..."
                    bind:value={$createForm.description}
                    rows="3"
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                ></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <Button type="button" variant="outline" onclick={() => isCreateOpen = false}>Batal</Button>
                <Button type="submit" loading={$createForm.processing}>Buat & Buka Studio</Button>
            </div>
        </form>
    </Dialog>
</AppLayout>
