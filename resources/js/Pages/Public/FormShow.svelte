<script lang="ts">
    import { useForm, page } from '@inertiajs/svelte';
    import {
        FileSpreadsheet,
        CheckCircle2,
        XCircle,
        Send,
        Star,
        Upload,
        AlertCircle
    } from 'lucide-svelte';
    import type { FormItem, PageProps } from '@/lib/types';

    interface Props {
        form: FormItem;
    }

    let { form }: Props = $props();

    const pageProps = $derived(page.props as unknown as PageProps);
    const flash = $derived(pageProps.flash);
    const successSubmission = $derived((pageProps.flash as any)?.success_submission);

    function createInitialPayload(formObj: FormItem) {
        const payload: Record<string, any> = {};
        if (formObj.fields) {
            for (const field of formObj.fields) {
                if (field.type === 'checkboxes') {
                    payload[`field_${field.id}`] = [];
                } else {
                    payload[`field_${field.id}`] = '';
                }
            }
        }
        return payload;
    }

    const publicForm = useForm(createInitialPayload(form));

    function submitPublic(e: Event) {
        e.preventDefault();
        $publicForm.post(`/f/${form.slug}/submit`, {
            preserveScroll: true,
        });
    }

    function toggleCheckboxOption(fieldId: number, option: string) {
        const key = `field_${fieldId}`;
        const currentArr = $publicForm[key] || [];
        if (currentArr.includes(option)) {
            $publicForm[key] = currentArr.filter((item: string) => item !== option);
        } else {
            $publicForm[key] = [...currentArr, option];
        }
    }
</script>

<svelte:head>
    <title>{form.title}</title>
</svelte:head>

<div class="min-h-screen bg-slate-100 dark:bg-slate-950 py-10 px-4 flex flex-col items-center">
    <div class="w-full max-w-2xl space-y-6">
        <!-- Top Google Forms Style Card -->
        <div
            class="p-8 rounded-2xl bg-white dark:bg-slate-900 border-t-8 border border-slate-200 dark:border-slate-800 shadow-md space-y-4"
            style="border-top-color: {form.theme_color || '#6366F1'}"
        >
            <h1 class="text-2xl font-black text-slate-900 dark:text-slate-100">
                {form.title}
            </h1>
            {#if form.description}
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    {form.description}
                </p>
            {/if}

            <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-400 font-medium">
                <span class="text-rose-500 font-semibold">* Wajib diisi</span>
                <span>Dipersembahkan oleh LaraSvelte CMS</span>
            </div>
        </div>

        <!-- Success Confirmation View -->
        {#if successSubmission}
            <div class="p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-md text-center space-y-4">
                <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto">
                    <CheckCircle2 class="w-8 h-8" />
                </div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">Tanggapan Diterima!</h2>
                <p class="text-xs text-slate-600 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
                    {successSubmission}
                </p>
                <a
                    href="/f/{form.slug}"
                    class="inline-block pt-2 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline"
                >
                    Kirim tanggapan lain
                </a>
            </div>
        {:else if !form.is_accepting_responses}
            <!-- Closed Form View -->
            <div class="p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-md text-center space-y-3">
                <div class="w-14 h-14 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto">
                    <XCircle class="w-7 h-7" />
                </div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">Formulir Ini Sudah Ditutup</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Formulir "{form.title}" saat ini sudah tidak lagi menerima tanggapan baru. Hubungi pemilik formulir jika menurut Anda ini adalah kesalahan.
                </p>
            </div>
        {:else}
            <!-- Form Questions Form -->
            <form onsubmit={submitPublic} class="space-y-6">
                {#each form.fields || [] as field (field.id)}
                    {@const fieldKey = `field_${field.id}`}
                    {@const fieldError = $publicForm.errors[fieldKey]}

                    <div class={`p-6 rounded-2xl bg-white dark:bg-slate-900 border shadow-sm space-y-3 transition-colors ${
                        fieldError ? 'border-rose-300 dark:border-rose-900 ring-2 ring-rose-500/10' : 'border-slate-200 dark:border-slate-800'
                    }`}>
                        <label class="block text-sm font-bold text-slate-900 dark:text-slate-100">
                            {field.label}
                            {#if field.is_required}
                                <span class="text-rose-500 font-bold ml-1">*</span>
                            {/if}
                        </label>

                        {#if field.help_text}
                            <p class="text-xs text-slate-400">{field.help_text}</p>
                        {/if}

                        <!-- Short Text Input -->
                        {#if field.type === 'text'}
                            <input
                                type="text"
                                placeholder={field.placeholder || 'Jawaban Anda'}
                                bind:value={$publicForm[fieldKey]}
                                class="w-full text-xs px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                            />
                        {/if}

                        <!-- Paragraph Input -->
                        {#if field.type === 'paragraph'}
                            <textarea
                                placeholder={field.placeholder || 'Jawaban Anda'}
                                bind:value={$publicForm[fieldKey]}
                                rows="3"
                                class="w-full text-xs px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                            ></textarea>
                        {/if}

                        <!-- Multiple Choice (Radio) -->
                        {#if field.type === 'multiple_choice'}
                            <div class="space-y-2 pt-1">
                                {#each field.options || [] as opt}
                                    <label class="flex items-center gap-3 cursor-pointer text-xs text-slate-700 dark:text-slate-300">
                                        <input
                                            type="radio"
                                            name={fieldKey}
                                            value={opt}
                                            bind:group={$publicForm[fieldKey]}
                                            class="text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <span>{opt}</span>
                                    </label>
                                {/each}
                            </div>
                        {/if}

                        <!-- Checkboxes (Multi Select) -->
                        {#if field.type === 'checkboxes'}
                            <div class="space-y-2 pt-1">
                                {#each field.options || [] as opt}
                                    {@const isChecked = ($publicForm[fieldKey] || []).includes(opt)}
                                    <label class="flex items-center gap-3 cursor-pointer text-xs text-slate-700 dark:text-slate-300">
                                        <input
                                            type="checkbox"
                                            checked={isChecked}
                                            onchange={() => toggleCheckboxOption(field.id!, opt)}
                                            class="rounded text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <span>{opt}</span>
                                    </label>
                                {/each}
                            </div>
                        {/if}

                        <!-- Dropdown Select -->
                        {#if field.type === 'dropdown'}
                            <select
                                bind:value={$publicForm[fieldKey]}
                                class="w-full text-xs px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                            >
                                <option value="">-- Pilih Salah Satu --</option>
                                {#each field.options || [] as opt}
                                    <option value={opt}>{opt}</option>
                                {/each}
                            </select>
                        {/if}

                        <!-- File Upload -->
                        {#if field.type === 'file'}
                            <input
                                type="file"
                                onchange={(e) => {
                                    const target = e.target as HTMLInputElement;
                                    if (target.files && target.files[0]) {
                                        $publicForm[fieldKey] = target.files[0];
                                    }
                                }}
                                class="w-full text-xs px-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none"
                            />
                        {/if}

                        <!-- Date Picker -->
                        {#if field.type === 'date'}
                            <input
                                type="date"
                                bind:value={$publicForm[fieldKey]}
                                class="text-xs px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none"
                            />
                        {/if}

                        <!-- Linear Rating Scale (1-5) -->
                        {#if field.type === 'rating'}
                            <div class="flex items-center gap-4 py-2">
                                {#each [1, 2, 3, 4, 5] as score}
                                    <label class="flex flex-col items-center gap-1 cursor-pointer">
                                        <input
                                            type="radio"
                                            name={fieldKey}
                                            value={score}
                                            bind:group={$publicForm[fieldKey]}
                                            class="text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <span class="text-xs font-bold text-slate-600 dark:text-slate-400">{score}</span>
                                    </label>
                                {/each}
                            </div>
                        {/if}

                        <!-- Validation Error -->
                        {#if fieldError}
                            <p class="text-[11px] font-semibold text-rose-500 flex items-center gap-1 mt-1">
                                <AlertCircle class="w-3.5 h-3.5" />
                                <span>Pertanyaan ini wajib diisi.</span>
                            </p>
                        {/if}
                    </div>
                {/each}

                <!-- Submit Button Bar -->
                <div class="flex items-center justify-between pt-2">
                    <button
                        type="submit"
                        disabled={$publicForm.processing}
                        class="px-8 py-3 rounded-xl text-xs font-bold text-white shadow-md hover:shadow-lg transition-all cursor-pointer flex items-center gap-2"
                        style="background-color: {form.theme_color || '#6366F1'}"
                    >
                        <Send class="w-4 h-4" />
                        <span>{$publicForm.processing ? 'Mengirim...' : 'Kirim Tanggapan'}</span>
                    </button>

                    <button
                        type="button"
                        onclick={() => $publicForm.reset()}
                        class="text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 font-semibold"
                    >
                        Kosongkan Formulir
                    </button>
                </div>
            </form>
        {/if}
    </div>
</div>
