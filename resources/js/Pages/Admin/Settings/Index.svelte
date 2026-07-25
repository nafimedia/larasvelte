<script lang="ts">
    import { useForm } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import Input from '@/Components/UI/Input.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import { Save, Sliders, Globe, ShieldAlert } from 'lucide-svelte';
    import type { SettingItem } from '@/lib/types';

    interface Props {
        settings: SettingItem[];
    }

    let { settings = [] }: Props = $props();

    // svelte-ignore state_referenced_locally
    const form = useForm({
        settings: settings.map(s => ({ key: s.key, value: s.value }))
    });

    function submit(e: Event) {
        e.preventDefault();
        form.put('/admin/settings');
    }

    function getSetting(key: string): SettingItem | undefined {
        return settings.find(s => s.key === key);
    }

    function updateSettingValue(key: string, newValue: any) {
        const item = form.settings.find((s: any) => s.key === key);
        if (item) {
            item.value = String(newValue);
        }
    }

    function getFormValue(key: string): string {
        const item = form.settings.find((s: any) => s.key === key);
        return item ? item.value : '';
    }
</script>

<AppLayout title="Pengaturan Situs">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Pengaturan Situs & Aplikasi</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Konfigurasi nama aplikasi, email kontak, dan sistem secara global</p>
        </div>
    </div>

    <form onsubmit={submit} class="space-y-6 max-w-4xl">
        <!-- General Identity -->
        <Card title="Identitas Aplikasi" description="Informasi umum yang ditampilkan di header dan title">
            <div class="space-y-4">
                {#if getSetting('site_name')}
                    <Input
                        label={getSetting('site_name')?.label}
                        value={getFormValue('site_name')}
                        oninput={(e) => updateSettingValue('site_name', (e.target as HTMLInputElement).value)}
                        placeholder="Nama Aplikasi..."
                    />
                {/if}

                {#if getSetting('site_description')}
                    <div class="space-y-1.5">
                        <label for="site_description_input" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                            {getSetting('site_description')?.label}
                        </label>
                        <textarea
                            id="site_description_input"
                            rows="3"
                            class="w-full px-3.5 py-2 text-sm rounded-lg border bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                            value={getFormValue('site_description')}
                            oninput={(e) => updateSettingValue('site_description', (e.target as HTMLTextAreaElement).value)}
                        ></textarea>
                    </div>
                {/if}
            </div>
        </Card>

        <!-- System Controls -->
        <Card title="Kontrol Sistem & Fitur" description="Pengaturan fitur pendaftaran dan mode pemeliharaan">
            <div class="space-y-4">
                {#if getSetting('enable_registration')}
                    <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200 dark:border-slate-800">
                        <div>
                            <p class="text-xs font-semibold text-slate-900 dark:text-slate-100">{getSetting('enable_registration')?.label}</p>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">{getSetting('enable_registration')?.description}</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                checked={getFormValue('enable_registration') === 'true'}
                                onchange={(e) => updateSettingValue('enable_registration', (e.target as HTMLInputElement).checked ? 'true' : 'false')}
                                class="sr-only peer"
                            />
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-800 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                {/if}

                {#if getSetting('maintenance_mode')}
                    <div class="flex items-center justify-between p-3 rounded-xl border border-rose-200 dark:border-rose-950 bg-rose-50/50 dark:bg-rose-950/20">
                        <div>
                            <p class="text-xs font-semibold text-rose-900 dark:text-rose-300">{getSetting('maintenance_mode')?.label}</p>
                            <p class="text-[10px] text-rose-700/80 dark:text-rose-400">{getSetting('maintenance_mode')?.description}</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                checked={getFormValue('maintenance_mode') === 'true'}
                                onchange={(e) => updateSettingValue('maintenance_mode', (e.target as HTMLInputElement).checked ? 'true' : 'false')}
                                class="sr-only peer"
                            />
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-800 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600"></div>
                        </label>
                    </div>
                {/if}
            </div>
        </Card>

        <div class="flex justify-end">
            <Button type="submit" variant="primary" size="md" disabled={form.processing}>
                <Save class="w-4 h-4" />
                <span>{form.processing ? 'Menyimpan...' : 'Simpan Seluruh Pengaturan'}</span>
            </Button>
        </div>
    </form>
</AppLayout>
