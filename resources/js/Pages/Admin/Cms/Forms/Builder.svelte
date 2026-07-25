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
        Copy,
        ExternalLink,
        Save,
        Trash2,
        Copy as DuplicateIcon,
        GripVertical,
        MessageSquare,
        Download,
        CheckCircle2,
        XCircle,
        Code,
        ArrowLeft,
        Settings,
        Sparkles,
        Eye,
        Check,
        Star,
        Calendar,
        FileUp,
        List,
        CheckSquare,
        CircleDot,
        Type,
        AlignLeft
    } from 'lucide-svelte';
    import type { FormItem, FormFieldItem, FormSubmissionItem } from '@/lib/types';

    interface Props {
        form: FormItem;
    }

    let { form }: Props = $props();

    let activeTab = $state<'questions' | 'responses' | 'settings'>('questions');
    let selectedSubmission = $state<FormSubmissionItem | null>(null);

    // svelte-ignore state_referenced_locally
    const studioForm = useForm({
        title: form.title,
        slug: form.slug,
        description: form.description || '',
        theme_color: form.theme_color || '#6366F1',
        is_accepting_responses: form.is_accepting_responses,
        confirmation_message: form.confirmation_message || 'Terima kasih, tanggapan Anda telah berhasil disimpan.',
        require_login: form.require_login,
        fields: form.fields && form.fields.length > 0 ? form.fields.map(f => ({
            id: f.id,
            type: f.type,
            label: f.label,
            help_text: f.help_text || '',
            placeholder: f.placeholder || '',
            options: f.options && f.options.length > 0 ? [...f.options] : ['Opsi 1'],
            is_required: f.is_required,
            order: f.order,
        })) : [
            {
                type: 'text' as const,
                label: 'Pertanyaan Tanpa Judul',
                help_text: '',
                placeholder: '',
                options: ['Opsi 1'],
                is_required: false,
                order: 1,
            }
        ],
    });

    const fieldTypes = [
        { type: 'text', label: 'Jawaban Singkat', icon: Type },
        { type: 'paragraph', label: 'Paragraf (Teks Panjang)', icon: AlignLeft },
        { type: 'multiple_choice', label: 'Pilihan Ganda', icon: CircleDot },
        { type: 'checkboxes', label: 'Kotak Centang (Checkboxes)', icon: CheckSquare },
        { type: 'dropdown', label: 'Drop-down', icon: List },
        { type: 'file', label: 'Upload Berkas', icon: FileUp },
        { type: 'date', label: 'Tanggal', icon: Calendar },
        { type: 'rating', label: 'Skala Rating (1-5)', icon: Star },
    ];

    function addQuestion() {
        studioForm.fields.push({
            type: 'text',
            label: 'Pertanyaan Baru',
            help_text: '',
            placeholder: '',
            options: ['Opsi 1', 'Opsi 2'],
            is_required: false,
            order: studioForm.fields.length + 1,
        });
    }

    function duplicateQuestion(index: number) {
        const source = studioForm.fields[index];
        const copy: FormFieldItem = {
            type: source.type,
            label: `${source.label} (Salinan)`,
            help_text: source.help_text,
            placeholder: source.placeholder,
            options: source.options ? [...source.options] : ['Opsi 1'],
            is_required: source.is_required,
            order: studioForm.fields.length + 1,
        };
        studioForm.fields.splice(index + 1, 0, copy);
    }

    function removeQuestion(index: number) {
        if (studioForm.fields.length <= 1) {
            toast.error('Formulir harus memiliki minimal 1 pertanyaan.');
            return;
        }
        studioForm.fields.splice(index, 1);
    }

    function addOption(fieldIndex: number) {
        const field = studioForm.fields[fieldIndex];
        if (!field.options) field.options = [];
        field.options.push(`Opsi ${field.options.length + 1}`);
    }

    function removeOption(fieldIndex: number, optionIndex: number) {
        const field = studioForm.fields[fieldIndex];
        if (field.options && field.options.length > 1) {
            field.options.splice(optionIndex, 1);
        }
    }

    function saveStudio() {
        studioForm.put(`/admin/cms/forms/${form.id}`, {
            preserveScroll: true,
            onSuccess: () => toast.success('Formulir Studio berhasil disimpan!'),
        });
    }

    function toggleResponses() {
        router.patch(`/admin/cms/forms/${form.id}/toggle-responses`, {}, {
            preserveScroll: true,
            onSuccess: () => {
                studioForm.is_accepting_responses = !studioForm.is_accepting_responses;
            }
        });
    }

    function copyShareLink() {
        const url = `${window.location.origin}/f/${studioForm.slug}`;
        navigator.clipboard.writeText(url);
        toast.success('Link tautan formulir berhasil disalin!');
    }

    function copyEmbedCode() {
        const url = `${window.location.origin}/f/${studioForm.slug}`;
        const iframe = `<iframe src="${url}" width="100%" height="700" frameborder="0" marginheight="0" marginwidth="0">Loading…</iframe>`;
        navigator.clipboard.writeText(iframe);
        toast.success('Kode iframe embed berhasil disalin!');
    }
</script>

<AppLayout title="Google Forms Builder Studio">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200 dark:border-slate-800">
        <div class="flex items-center gap-3">
            <a
                href="/admin/cms/forms"
                class="p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                title="Kembali ke Daftar Formulir"
            >
                <ArrowLeft class="w-5 h-5" />
            </a>

            <div>
                <div class="flex items-center gap-2">
                    <input
                        type="text"
                        bind:value={studioForm.title}
                        class="text-lg font-bold text-slate-900 dark:text-slate-100 bg-transparent border-b border-transparent hover:border-slate-300 focus:border-indigo-500 focus:outline-none px-1"
                    />
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-400 mt-0.5">
                    <span>URL: /f/{studioForm.slug}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Theme Color Selector -->
            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                <span class="text-xs text-slate-500 font-medium">Tema:</span>
                <input
                    type="color"
                    bind:value={studioForm.theme_color}
                    class="w-6 h-6 rounded-md cursor-pointer border-0 bg-transparent"
                />
            </div>

            <a
                href="/f/{form.slug}"
                target="_blank"
                class="p-2 rounded-xl text-slate-500 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                title="Pratinjau Formulir Publik"
            >
                <Eye class="w-5 h-5" />
            </a>

            <Button onclick={saveStudio} loading={studioForm.processing} class="cursor-pointer gap-2">
                <Save class="w-4 h-4" />
                <span>Simpan</span>
            </Button>
        </div>
    </div>

    <!-- 3-Tab Google Forms Navigation -->
    <div class="flex items-center justify-center border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl shadow-xs">
        <button
            type="button"
            onclick={() => activeTab = 'questions'}
            class={`px-6 py-3 text-xs font-bold transition-all border-b-2 cursor-pointer flex items-center gap-2 ${
                activeTab === 'questions'
                    ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400'
                    : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900'
            }`}
        >
            <FileSpreadsheet class="w-4 h-4" />
            <span>Pertanyaan</span>
        </button>

        <button
            type="button"
            onclick={() => activeTab = 'responses'}
            class={`px-6 py-3 text-xs font-bold transition-all border-b-2 cursor-pointer flex items-center gap-2 ${
                activeTab === 'responses'
                    ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400'
                    : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900'
            }`}
        >
            <MessageSquare class="w-4 h-4" />
            <span>Tanggapan ({form.submissions?.length ?? 0})</span>
        </button>

        <button
            type="button"
            onclick={() => activeTab = 'settings'}
            class={`px-6 py-3 text-xs font-bold transition-all border-b-2 cursor-pointer flex items-center gap-2 ${
                activeTab === 'settings'
                    ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400'
                    : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900'
            }`}
        >
            <Settings class="w-4 h-4" />
            <span>Setelan</span>
        </button>
    </div>

    <!-- TAB 1: PERTANYAAN (QUESTION STUDIO) -->
    {#if activeTab === 'questions'}
        <div class="max-w-3xl mx-auto space-y-6">
            <!-- Form Header Card (Google Forms Style Top Card) -->
            <div
                class="p-6 rounded-2xl bg-white dark:bg-slate-900 border-t-8 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4"
                style="border-top-color: {studioForm.theme_color}"
            >
                <input
                    type="text"
                    bind:value={studioForm.title}
                    placeholder="Judul Formulir"
                    class="w-full text-2xl font-black text-slate-900 dark:text-slate-100 bg-transparent border-b border-slate-200 dark:border-slate-800 focus:border-indigo-500 focus:outline-none pb-2"
                />
                <textarea
                    bind:value={studioForm.description}
                    placeholder="Deskripsi formulir (petunjuk pengisian)..."
                    rows="2"
                    class="w-full text-xs text-slate-600 dark:text-slate-400 bg-transparent border-b border-slate-200 dark:border-slate-800 focus:border-indigo-500 focus:outline-none pb-1"
                ></textarea>
            </div>

            <!-- Question Cards List -->
            <div class="space-y-4">
                {#each studioForm.fields as field, index (index)}
                    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4 relative group hover:border-indigo-300 transition-colors">
                        <!-- Top Grip Handle & Question Number -->
                        <div class="flex items-center justify-between text-slate-400">
                            <div class="flex items-center gap-2">
                                <GripVertical class="w-4 h-4 cursor-grab" />
                                <span class="text-xs font-bold text-slate-500">Pertanyaan #{index + 1}</span>
                            </div>

                            <!-- Question Type Selector Dropdown -->
                            <select
                                bind:value={field.type}
                                class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                            >
                                {#each fieldTypes as ft}
                                    <option value={ft.type}>{ft.label}</option>
                                {/each}
                            </select>
                        </div>

                        <!-- Question Label Input -->
                        <div class="space-y-2">
                            <input
                                type="text"
                                bind:value={field.label}
                                placeholder="Tuliskan pertanyaan di sini..."
                                class="w-full text-sm font-bold text-slate-900 dark:text-slate-100 bg-slate-50 dark:bg-slate-950 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 focus:border-indigo-500 focus:outline-none"
                            />
                            <input
                                type="text"
                                bind:value={field.help_text}
                                placeholder="Teks bantuan / petunjuk tambahan (opsional)..."
                                class="w-full text-xs text-slate-500 bg-transparent px-2 py-1 border-b border-transparent hover:border-slate-200 focus:border-indigo-500 focus:outline-none"
                            />
                        </div>

                        <!-- Choice Options Editor (Multiple Choice, Checkboxes, Dropdown) -->
                        {#if ['multiple_choice', 'checkboxes', 'dropdown'].includes(field.type)}
                            <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-850">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Opsi Pilihan:</span>
                                {#each field.options || [] as opt, optIdx (optIdx)}
                                    <div class="flex items-center gap-2">
                                        {#if field.type === 'multiple_choice'}
                                            <CircleDot class="w-4 h-4 text-slate-400 shrink-0" />
                                        {:else if field.type === 'checkboxes'}
                                            <CheckSquare class="w-4 h-4 text-slate-400 shrink-0" />
                                        {:else}
                                            <List class="w-4 h-4 text-slate-400 shrink-0" />
                                        {/if}

                                        <input
                                            type="text"
                                            bind:value={field.options[optIdx]}
                                            class="w-full text-xs px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                                        />

                                        <button
                                            type="button"
                                            onclick={() => removeOption(index, optIdx)}
                                            class="p-1 text-slate-400 hover:text-rose-600 transition-colors cursor-pointer"
                                            title="Hapus Opsi"
                                        >
                                            <XCircle class="w-4 h-4" />
                                        </button>
                                    </div>
                                {/each}

                                <button
                                    type="button"
                                    onclick={() => addOption(index)}
                                    class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-1 pt-1 cursor-pointer"
                                >
                                    <Plus class="w-3.5 h-3.5" />
                                    <span>Tambah Opsi</span>
                                </button>
                            </div>
                        {/if}

                        <!-- Rating Scale Preview -->
                        {#if field.type === 'rating'}
                            <div class="flex items-center gap-2 py-2 text-amber-500">
                                {#each [1, 2, 3, 4, 5] as star}
                                    <Star class="w-5 h-5 fill-amber-400 text-amber-400" />
                                {/each}
                                <span class="text-xs font-semibold text-slate-400 ml-2">(Skala Rating 1 - 5)</span>
                            </div>
                        {/if}

                        <!-- Footer Card Actions -->
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-4">
                            <button
                                type="button"
                                onclick={() => duplicateQuestion(index)}
                                class="p-2 text-slate-400 hover:text-indigo-600 transition-colors cursor-pointer"
                                title="Duplikat Pertanyaan"
                            >
                                <DuplicateIcon class="w-4 h-4" />
                            </button>

                            <button
                                type="button"
                                onclick={() => removeQuestion(index)}
                                class="p-2 text-slate-400 hover:text-rose-600 transition-colors cursor-pointer"
                                title="Hapus Pertanyaan"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>

                            <div class="h-4 w-px bg-slate-200 dark:bg-slate-800"></div>

                            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700 dark:text-slate-300">
                                <span>Wajib Diisi</span>
                                <input
                                    type="checkbox"
                                    bind:checked={field.is_required}
                                    class="rounded text-indigo-600 focus:ring-indigo-500"
                                />
                            </label>
                        </div>
                    </div>
                {/each}
            </div>

            <!-- Bottom Floating Add Toolbar -->
            <div class="flex justify-center pt-2">
                <Button onclick={addQuestion} class="cursor-pointer gap-2 shadow-lg rounded-full px-6">
                    <Plus class="w-4 h-4" />
                    <span>Tambah Pertanyaan</span>
                </Button>
            </div>
        </div>
    {/if}

    <!-- TAB 2: RESPON & TANGGAPAN -->
    {#if activeTab === 'responses'}
        <div class="space-y-6">
            <!-- Responses Summary Bar -->
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100">
                        {form.submissions?.length ?? 0} Tanggapan
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {studioForm.is_accepting_responses ? 'Formulir aktif menerima tanggapan baru' : 'Formulir ditutup untuk tanggapan baru'}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        onclick={toggleResponses}
                        class={`px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-2 ${
                            studioForm.is_accepting_responses
                                ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 border border-emerald-200'
                                : 'bg-rose-50 dark:bg-rose-950/50 text-rose-600 border border-rose-200'
                        }`}
                    >
                        {#if studioForm.is_accepting_responses}
                            <CheckCircle2 class="w-4 h-4" />
                            <span>Menerima Tanggapan</span>
                        {:else}
                            <XCircle class="w-4 h-4" />
                            <span>Formulir Ditutup</span>
                        {/if}
                    </button>

                    <a
                        href="/admin/cms/forms/{form.id}/export"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 hover:bg-slate-800 shadow-xs transition-colors"
                    >
                        <Download class="w-4 h-4" />
                        <span>Unduh CSV</span>
                    </a>
                </div>
            </div>

            <!-- Submissions Table -->
            {#if form.submissions && form.submissions.length > 0}
                <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 dark:bg-slate-950 text-slate-500 font-bold border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th class="p-4"># ID</th>
                                    <th class="p-4">Waktu Kirim</th>
                                    <th class="p-4">IP Address</th>
                                    <th class="p-4">Ringkasan Jawaban Pertama</th>
                                    <th class="p-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                {#each form.submissions as sub (sub.id)}
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/50 transition-colors">
                                        <td class="p-4 font-mono font-bold text-indigo-600">#{sub.id}</td>
                                        <td class="p-4 text-slate-600 dark:text-slate-400">
                                            {new Date(sub.created_at).toLocaleString('id-ID')}
                                        </td>
                                        <td class="p-4 text-slate-500 font-mono">{sub.ip_address || '-'}</td>
                                        <td class="p-4 text-slate-900 dark:text-slate-100 font-medium max-w-xs truncate">
                                            {Object.values(sub.response_data)[0] || '-'}
                                        </td>
                                        <td class="p-4 text-right">
                                            <button
                                                type="button"
                                                onclick={() => selectedSubmission = sub}
                                                class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400 font-semibold hover:bg-indigo-100 transition-colors cursor-pointer"
                                            >
                                                Lihat Detail
                                            </button>
                                        </td>
                                    </tr>
                                {/each}
                            </tbody>
                        </table>
                    </div>
                </div>
            {:else}
                <div class="py-12 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-2">
                    <MessageSquare class="w-10 h-10 text-slate-400 mx-auto stroke-1" />
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada tanggapan masuk</p>
                    <p class="text-xs text-slate-400">Sebarkan link formulir publik Anda untuk mengumpulkan respon pertama</p>
                </div>
            {/if}
        </div>
    {/if}

    <!-- TAB 3: SETELAN & SHARE -->
    {#if activeTab === 'settings'}
        <div class="max-w-2xl mx-auto space-y-6">
            <Card class="p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Setelan Umum Formulir</h3>

                <Input
                    label="Slug URL Formulir Publik"
                    bind:value={studioForm.slug}
                    error={studioForm.errors.slug}
                    required
                />

                <div class="space-y-1.5">
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Pesan Konfirmasi Setelah Submit
                    </span>
                    <textarea
                        bind:value={studioForm.confirmation_message}
                        rows="3"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                    ></textarea>
                </div>

                <label class="flex items-center gap-3 pt-2 cursor-pointer">
                    <input
                        type="checkbox"
                        bind:checked={studioForm.require_login}
                        class="rounded text-indigo-600 focus:ring-indigo-500"
                    />
                    <div>
                        <p class="text-xs font-bold text-slate-900 dark:text-slate-100">Wajibkan Login</p>
                        <p class="text-[11px] text-slate-400">Pengguna harus memiliki akun dan login untuk mengisi formulir ini.</p>
                    </div>
                </label>
            </Card>

            <Card class="p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Bagikan & Embed</h3>

                <div class="space-y-2">
                    <span class="text-xs font-semibold text-slate-600">Link Tautan Publik</span>
                    <div class="flex items-center gap-2">
                        <input
                            type="text"
                            readonly
                            value={`${window.location.origin}/f/${studioForm.slug}`}
                            class="w-full text-xs px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 font-mono text-slate-600 dark:text-slate-300"
                        />
                        <Button type="button" onclick={copyShareLink} class="shrink-0 gap-1.5 cursor-pointer">
                            <Copy class="w-4 h-4" />
                            <span>Salin</span>
                        </Button>
                    </div>
                </div>

                <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-xs font-semibold text-slate-600">Kode Embed iFrame</span>
                    <div class="flex items-center gap-2">
                        <input
                            type="text"
                            readonly
                            value={`<iframe src="${window.location.origin}/f/${studioForm.slug}" width="100%" height="700" frameborder="0"></iframe>`}
                            class="w-full text-xs px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 font-mono text-slate-600 dark:text-slate-300 truncate"
                        />
                        <Button type="button" variant="outline" onclick={copyEmbedCode} class="shrink-0 gap-1.5 cursor-pointer">
                            <Code class="w-4 h-4" />
                            <span>Salin Embed</span>
                        </Button>
                    </div>
                </div>
            </Card>
        </div>
    {/if}

    <!-- Submission Detail Modal -->
    <Dialog open={selectedSubmission !== null} onClose={() => selectedSubmission = null} title="Detail Tanggapan Respon">
        {#if selectedSubmission}
            <div class="space-y-4 text-xs">
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-950 flex justify-between text-slate-500 font-mono">
                    <span>ID: #{selectedSubmission.id}</span>
                    <span>{new Date(selectedSubmission.created_at).toLocaleString('id-ID')}</span>
                </div>

                <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                    {#each form.fields || [] as field}
                        {@const val = selectedSubmission.response_data[`field_${field.id}`]}
                        <div class="p-3 rounded-xl bg-slate-50/50 dark:bg-slate-850/50 border border-slate-200/60 dark:border-slate-800 space-y-1">
                            <p class="font-bold text-slate-900 dark:text-slate-100">{field.label}</p>
                            {#if typeof val === 'object' && val !== null && val.url}
                                <a href={val.url} target="_blank" class="text-indigo-600 font-bold hover:underline inline-flex items-center gap-1">
                                    <FileUp class="w-3.5 h-3.5" />
                                    <span>Unduh Berkas Lampiran ({val.name})</span>
                                </a>
                            {:else if Array.isArray(val)}
                                <p class="text-slate-600 dark:text-slate-300 font-medium">{val.join(', ')}</p>
                            {:else}
                                <p class="text-slate-600 dark:text-slate-300 font-medium">{val || '-'}</p>
                            {/if}
                        </div>
                    {/each}
                </div>
            </div>
        {/if}
    </Dialog>
</AppLayout>
