<script lang="ts">
    import { useForm, Link } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import Input from '@/Components/UI/Input.svelte';
    import Modal from '@/Components/UI/Modal.svelte';
    import Badge from '@/Components/UI/Badge.svelte';
    import { FileSpreadsheet, Plus, Inbox, CheckCircle, ExternalLink, Code } from 'lucide-svelte';

    interface Props {
        forms?: any[];
    }

    let { forms = [] }: Props = $props();

    let isCreateOpen = $state(false);

    const form = useForm({
        name: '',
        description: '',
        submit_button_text: 'Kirim Pesan',
        fields: [
            { label: 'Nama Lengkap', name: 'name', type: 'text', required: true },
            { label: 'Alamat Email', name: 'email', type: 'email', required: true },
            { label: 'Pesan / Pertanyaan', name: 'message', type: 'textarea', required: true },
        ],
    });

    function submitForm(e: Event) {
        e.preventDefault();
        form.post('/admin/cms/forms', {
            onSuccess: () => {
                isCreateOpen = false;
                form.reset();
            }
        });
    }
</script>

<AppLayout title="Dynamic Form Builder">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <FileSpreadsheet class="w-5 h-5 text-indigo-500" />
                <span>Form Builder & Submissions Inbox</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Buat formulir kontak, pendaftaran, dan survei interaktif tanpa koding</p>
        </div>

        <Button variant="primary" size="md" onclick={() => isCreateOpen = true}>
            <Plus class="w-4 h-4 mr-1.5" />
            <span>Buat Form Baru</span>
        </Button>
    </div>

    <!-- Forms Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {#each forms as f}
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 flex flex-col justify-between hover:border-indigo-500/40 transition-all">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <Badge variant="success">AKTIF</Badge>
                        <span class="text-xs font-mono text-indigo-400 font-bold flex items-center gap-1">
                            <Inbox class="w-3.5 h-3.5" />
                            {f.submissions_count || 0} Pesan Masuk
                        </span>
                    </div>

                    <h3 class="font-bold text-base text-white">{f.name}</h3>
                    <p class="text-xs text-slate-400 line-clamp-2">{f.description || 'Tidak ada deskripsi'}</p>

                    <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-mono text-slate-400">
                        <span>Slug Endpoint: </span>
                        <span class="text-indigo-300">/forms/{f.slug}/submit</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between">
                    <span class="text-xs text-slate-400 font-mono">{(f.fields || []).length} Fields Input</span>
                    <Link href={`/admin/cms/forms/${f.id}/submissions`} class="inline-flex items-center gap-1 text-xs font-bold text-indigo-400 hover:text-indigo-300">
                        <Inbox class="w-3.5 h-3.5" />
                        <span>Lihat Inbox</span>
                    </Link>
                </div>
            </div>
        {/each}
    </div>

    <!-- Create Form Modal -->
    <Modal bind:open={isCreateOpen} title="Buat Formulir Baru" description="Isi rincian nama dan tombol untuk formulir baru">
        <form onsubmit={submitForm} class="space-y-4">
            <Input label="Nama Formulir" placeholder="Contoh: Form Pendaftaran Webinar" bind:value={form.name} required />
            <Input label="Deskripsi" placeholder="Penjelasan singkat untuk responden..." bind:value={form.description} />
            <Input label="Teks Tombol Kirim" placeholder="Kirim Pesan" bind:value={form.submit_button_text} />

            <div class="pt-2 flex justify-end gap-2">
                <Button variant="outline" size="sm" onclick={() => isCreateOpen = false}>Batal</Button>
                <Button variant="primary" size="sm" type="submit" disabled={form.processing}>Simpan Formulir</Button>
            </div>
        </form>
    </Modal>
</AppLayout>
