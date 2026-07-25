<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import Input from '@/Components/UI/Input.svelte';
    import Modal from '@/Components/UI/Modal.svelte';
    import DynamicSectionRenderer from '@/Components/Landing/DynamicSectionRenderer.svelte';
    import {
        Sparkles,
        Plus,
        Eye,
        EyeOff,
        Trash2,
        Copy,
        MoveUp,
        MoveDown,
        Monitor,
        Tablet,
        Smartphone,
        CheckCircle2,
        Globe,
        Upload,
        Settings,
        Layers,
        Paintbrush,
        Save,
        Rocket,
        Image as ImageIcon,
        ExternalLink,
        Check
    } from 'lucide-svelte';

    interface Props {
        sections: any[];
        themeSettings: any;
        seoSettings: any;
        mediaFiles: any[];
    }

    let { sections = [], themeSettings = {}, seoSettings = {}, mediaFiles = [] }: Props = $props();

    // svelte-ignore state_referenced_locally
    let selectedSectionId: number | null = $state(sections.length > 0 ? sections[0].id : null);
    let activeViewport: 'desktop' | 'tablet' | 'mobile' = $state('desktop');
    let activeInspectorTab: 'content' | 'styles' | 'seo' = $state('content');

    // Section Create Modal State
    let isAddModalOpen = $state(false);
    let newSectionType = $state('features');
    let newSectionName = $state('');

    // Media Modal State
    let isMediaModalOpen = $state(false);

    // Selected Section Getter
    const selectedSection = $derived(
        sections.find(s => s.id === selectedSectionId) || sections[0] || null
    );

    // Dynamic Form Binding for Inspector
    let editableSection = $state<any>(null);

    $effect(() => {
        if (selectedSection) {
            editableSection = JSON.parse(JSON.stringify(selectedSection));
        }
    });

    function selectSection(id: number) {
        selectedSectionId = id;
    }

    function saveCurrentSection() {
        if (!editableSection) return;
        router.put(`/admin/landing-builder/sections/${editableSection.id}`, {
            name: editableSection.name,
            title: editableSection.title,
            subtitle: editableSection.subtitle,
            description: editableSection.description,
            content: editableSection.content,
            settings: editableSection.settings,
            is_active: editableSection.is_active,
        }, {
            preserveScroll: true,
            preserveState: true,
        });
    }

    function toggleSectionActive(sec: any, e: MouseEvent) {
        e.stopPropagation();
        router.put(`/admin/landing-builder/sections/${sec.id}`, {
            ...sec,
            is_active: !sec.is_active,
        }, { preserveScroll: true, preserveState: true });
    }

    function moveSection(index: number, direction: 'up' | 'down') {
        const targetIndex = direction === 'up' ? index - 1 : index + 1;
        if (targetIndex < 0 || targetIndex >= sections.length) return;

        const newSections = [...sections];
        const temp = newSections[index];
        newSections[index] = newSections[targetIndex];
        newSections[targetIndex] = temp;

        const orders = newSections.map((sec, i) => ({ id: sec.id, order: i + 1 }));

        router.put('/admin/landing-builder/sections/reorder', { orders }, {
            preserveScroll: true,
            preserveState: true,
        });
    }

    function duplicateSection(id: number, e: MouseEvent) {
        e.stopPropagation();
        router.post(`/admin/landing-builder/sections/${id}/duplicate`, {}, {
            preserveScroll: true,
        });
    }

    function deleteSection(id: number, e: MouseEvent) {
        e.stopPropagation();
        if (confirm('Apakah Anda yakin ingin menghapus section ini?')) {
            router.delete(`/admin/landing-builder/sections/${id}`, {
                preserveScroll: true,
            });
        }
    }

    function createNewSection() {
        if (!newSectionName) return;
        router.post('/admin/landing-builder/sections', {
            type: newSectionType,
            name: newSectionName,
        }, {
            onSuccess: () => {
                isAddModalOpen = false;
                newSectionName = '';
            }
        });
    }

    function publishAll() {
        if (confirm('Publikasikan seluruh perubahan ke live landing page?')) {
            router.post('/admin/landing-builder/publish', {}, {
                preserveScroll: true,
            });
        }
    }
</script>

<AppLayout title="Visual Landing Builder Studio">
    <!-- Top Toolbar Header -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 mb-6 shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-pink-500 p-0.5 shadow-md">
                <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
                    <Sparkles class="w-5 h-5 text-indigo-400" />
                </div>
            </div>
            <div>
                <h1 class="text-lg font-extrabold text-white flex items-center gap-2">
                    FairuzKit Visual Studio
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        Page Builder v1.0
                    </span>
                </h1>
                <p class="text-xs text-slate-400">Edit, urutkan, dan kustomisasi landing page secara visual real-time</p>
            </div>
        </div>

        <!-- Viewport Controls & Action Buttons -->
        <div class="flex flex-wrap items-center gap-3">
            <div class="bg-slate-950 p-1 rounded-xl border border-slate-800 flex items-center gap-1">
                <button
                    onclick={() => activeViewport = 'desktop'}
                    class={`p-2 rounded-lg text-xs font-medium transition-colors ${activeViewport === 'desktop' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-400 hover:text-white'}`}
                    title="Desktop View (100%)"
                >
                    <Monitor class="w-4 h-4" />
                </button>
                <button
                    onclick={() => activeViewport = 'tablet'}
                    class={`p-2 rounded-lg text-xs font-medium transition-colors ${activeViewport === 'tablet' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-400 hover:text-white'}`}
                    title="Tablet View (768px)"
                >
                    <Tablet class="w-4 h-4" />
                </button>
                <button
                    onclick={() => activeViewport = 'mobile'}
                    class={`p-2 rounded-lg text-xs font-medium transition-colors ${activeViewport === 'mobile' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-400 hover:text-white'}`}
                    title="Mobile View (375px)"
                >
                    <Smartphone class="w-4 h-4" />
                </button>
            </div>

            <a
                href="/"
                target="_blank"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 transition-colors"
            >
                <Globe class="w-3.5 h-3.5" />
                <span>Lihat Live Site</span>
                <ExternalLink class="w-3 h-3 ml-0.5" />
            </a>

            <Button variant="primary" size="sm" onclick={publishAll}>
                <Rocket class="w-4 h-4 mr-1.5" />
                <span>Publikasikan Halaman</span>
            </Button>
        </div>
    </div>

    <!-- Main Studio 3-Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- LEFT PANEL: Section Navigator (3 cols) -->
        <div class="lg:col-span-3 bg-slate-900 border border-slate-800 rounded-2xl p-4 space-y-4 shadow-lg">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2 text-xs font-bold text-white uppercase tracking-wider">
                    <Layers class="w-4 h-4 text-indigo-400" />
                    <span>Daftar Section ({sections.length})</span>
                </div>
                <button
                    onclick={() => isAddModalOpen = true}
                    class="p-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-xs flex items-center gap-1"
                    title="Tambah Section Baru"
                >
                    <Plus class="w-4 h-4" />
                </button>
            </div>

            <div class="space-y-2 max-h-[600px] overflow-y-auto pr-1">
                {#each sections as sec, i (sec.id)}
                    <div
                        onclick={() => selectSection(sec.id)}
                        role="button"
                        tabindex="0"
                        onkeydown={(e) => e.key === 'Enter' && selectSection(sec.id)}
                        class={`p-3 rounded-xl border text-xs transition-all cursor-pointer flex items-center justify-between gap-2 ${
                            sec.id === selectedSectionId
                                ? 'bg-indigo-950/60 border-indigo-500 text-white shadow-md ring-1 ring-indigo-500/50'
                                : 'bg-slate-950/60 border-slate-800 text-slate-300 hover:bg-slate-800/60'
                        }`}
                    >
                        <div class="flex items-center gap-2 truncate">
                            <div class="flex flex-col gap-0.5">
                                <button
                                    type="button"
                                    onclick={(e) => { e.stopPropagation(); moveSection(i, 'up'); }}
                                    disabled={i === 0}
                                    class="p-0.5 text-slate-500 hover:text-indigo-400 disabled:opacity-30"
                                >
                                    <MoveUp class="w-3 h-3" />
                                </button>
                                <button
                                    type="button"
                                    onclick={(e) => { e.stopPropagation(); moveSection(i, 'down'); }}
                                    disabled={i === sections.length - 1}
                                    class="p-0.5 text-slate-500 hover:text-indigo-400 disabled:opacity-30"
                                >
                                    <MoveDown class="w-3 h-3" />
                                </button>
                            </div>
                            <div class="truncate">
                                <p class="font-bold truncate">{sec.name}</p>
                                <span class="text-[10px] text-indigo-400 capitalize">{sec.type}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1">
                            <button
                                type="button"
                                onclick={(e) => toggleSectionActive(sec, e)}
                                class={`p-1.5 rounded-lg transition-colors ${sec.is_active ? 'text-emerald-400 hover:bg-emerald-500/10' : 'text-slate-500 hover:bg-slate-800'}`}
                                title={sec.is_active ? 'Nonaktifkan' : 'Aktifkan'}
                            >
                                {#if sec.is_active}
                                    <Eye class="w-3.5 h-3.5" />
                                {:else}
                                    <EyeOff class="w-3.5 h-3.5" />
                                {/if}
                            </button>
                            <button
                                type="button"
                                onclick={(e) => duplicateSection(sec.id, e)}
                                class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800"
                                title="Duplikat"
                            >
                                <Copy class="w-3.5 h-3.5" />
                            </button>
                            <button
                                type="button"
                                onclick={(e) => deleteSection(sec.id, e)}
                                class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-500/10"
                                title="Hapus"
                            >
                                <Trash2 class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>
                {/each}
            </div>
        </div>

        <!-- CENTER PANEL: Interactive Canvas Preview (6 cols) -->
        <div class="lg:col-span-6 space-y-4">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-3 flex items-center justify-between text-xs text-slate-400">
                <span class="font-medium flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                    Live Canvas Preview ({activeViewport.toUpperCase()})
                </span>
                <span class="text-[10px] text-slate-500">Klik section mana saja untuk diedit</span>
            </div>

            <!-- Viewport Container Frame -->
            <div class="flex justify-center w-full overflow-x-auto py-2">
                <div class={`transition-all duration-300 bg-slate-950 rounded-2xl border border-slate-800 shadow-2xl overflow-hidden min-h-[650px] ${
                    activeViewport === 'mobile' ? 'w-[375px]' :
                    activeViewport === 'tablet' ? 'w-[768px]' : 'w-full'
                }`}>
                    {#if sections.length === 0}
                        <div class="p-12 text-center text-slate-500 space-y-3">
                            <Layers class="w-12 h-12 mx-auto text-slate-600" />
                            <p>Belum ada section. Klik tombol "+" di panel kiri untuk membuat section pertama.</p>
                        </div>
                    {:else}
                        <div class="divide-y divide-slate-800/40">
                            {#each sections as sec (sec.id)}
                                <div
                                    onclick={() => selectSection(sec.id)}
                                    role="button"
                                    tabindex="0"
                                    onkeydown={(e) => e.key === 'Enter' && selectSection(sec.id)}
                                    class={`relative transition-all cursor-pointer group ${
                                        sec.id === selectedSectionId ? 'ring-2 ring-indigo-500 bg-indigo-950/20' : 'hover:ring-1 hover:ring-indigo-500/40'
                                    }`}
                                >
                                    <!-- Selected Indicator Banner -->
                                    {#if sec.id === selectedSectionId}
                                        <div class="absolute top-2 right-4 z-30 px-3 py-1 rounded-full bg-indigo-600 text-white text-[10px] font-bold shadow-md flex items-center gap-1">
                                            <Check class="w-3 h-3" />
                                            <span>Active Section</span>
                                        </div>
                                    {/if}

                                    <DynamicSectionRenderer section={sec} previewMode={true} />
                                </div>
                            {/each}
                        </div>
                    {/if}
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Property Inspector & Property Editor (3 cols) -->
        <div class="lg:col-span-3 bg-slate-900 border border-slate-800 rounded-2xl p-4 space-y-4 shadow-lg">
            {#if editableSection}
                <!-- Tab Navigation Header -->
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-1 bg-slate-950 p-1 rounded-xl border border-slate-800 w-full">
                        <button
                            onclick={() => activeInspectorTab = 'content'}
                            class={`flex-1 py-1.5 text-xs font-semibold rounded-lg transition-colors flex items-center justify-center gap-1 ${activeInspectorTab === 'content' ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white'}`}
                        >
                            <Paintbrush class="w-3.5 h-3.5" />
                            <span>Konten</span>
                        </button>
                        <button
                            onclick={() => activeInspectorTab = 'styles'}
                            class={`flex-1 py-1.5 text-xs font-semibold rounded-lg transition-colors flex items-center justify-center gap-1 ${activeInspectorTab === 'styles' ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white'}`}
                        >
                            <Settings class="w-3.5 h-3.5" />
                            <span>Style</span>
                        </button>
                    </div>
                </div>

                <!-- Inspector Body Form -->
                <div class="space-y-4 max-h-[600px] overflow-y-auto pr-1">

                    {#if activeInspectorTab === 'content'}
                        <!-- Name & Section Slug -->
                        <Input
                            label="Nama Section (Internal)"
                            bind:value={editableSection.name}
                        />

                        <Input
                            label="Judul Utama (Title)"
                            bind:value={editableSection.title}
                        />

                        <Input
                            label="Subjudul (Subtitle)"
                            bind:value={editableSection.subtitle}
                        />

                        <div class="space-y-1.5">
                            <label for="sec-desc" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Deskripsi</label>
                            <textarea
                                id="sec-desc"
                                rows="3"
                                class="w-full px-3 py-2 text-xs rounded-lg border border-slate-800 bg-slate-950 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                                bind:value={editableSection.description}
                            ></textarea>
                        </div>

                        <!-- Specific Hero Section Controls -->
                        {#if editableSection.type === 'hero'}
                            <div class="pt-3 border-t border-slate-800 space-y-3">
                                <p class="text-xs font-bold text-indigo-400 uppercase tracking-wider">Pengaturan Hero Banner</p>
                                <Input label="Pill Badge Text" bind:value={editableSection.content.badge} />
                                <Input label="Teks Tombol Utama" bind:value={editableSection.content.primary_btn_text} />
                                <Input label="URL Tombol Utama" bind:value={editableSection.content.primary_btn_url} />
                                <Input label="Teks Tombol Sekunder" bind:value={editableSection.content.secondary_btn_text} />
                                <Input label="URL Gambar Hero Mascot" bind:value={editableSection.content.hero_image} />
                            </div>
                        {/if}

                    {:else}
                        <!-- Styles & Layout Tab -->
                        <div class="space-y-4 text-xs">
                            <div class="space-y-1.5">
                                <label for="bg-style" class="block font-semibold uppercase text-slate-400">Warna Background</label>
                                <select
                                    id="bg-style"
                                    bind:value={editableSection.settings.background}
                                    class="w-full px-3 py-2 rounded-lg border border-slate-800 bg-slate-950 text-white"
                                >
                                    <option value="transparent">Transparent / Transparan</option>
                                    <option value="slate-900/50">Dark Panel (slate-900/50)</option>
                                    <option value="slate-900/30">Subtle Dark (slate-900/30)</option>
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label for="pad-style" class="block font-semibold uppercase text-slate-400">Padding Atas/Bawah</label>
                                <select
                                    id="pad-style"
                                    bind:value={editableSection.settings.paddingTop}
                                    class="w-full px-3 py-2 rounded-lg border border-slate-800 bg-slate-950 text-white"
                                >
                                    <option value="py-12">Kecil (py-12)</option>
                                    <option value="py-20">Sedang (py-20)</option>
                                    <option value="py-28">Besar (py-28)</option>
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label for="width-style" class="block font-semibold uppercase text-slate-400">Lebar Container</label>
                                <select
                                    id="width-style"
                                    bind:value={editableSection.settings.containerWidth}
                                    class="w-full px-3 py-2 rounded-lg border border-slate-800 bg-slate-950 text-white"
                                >
                                    <option value="4xl">4XL (Ringkas)</option>
                                    <option value="5xl">5XL (Sedang)</option>
                                    <option value="7xl">7XL (Lebar Standard)</option>
                                </select>
                            </div>
                        </div>
                    {/if}

                    <div class="pt-4 border-t border-slate-800">
                        <Button variant="primary" size="sm" class="w-full" onclick={saveCurrentSection}>
                            <Save class="w-4 h-4 mr-1.5" />
                            <span>Simpan Perubahan Section</span>
                        </Button>
                    </div>

                </div>
            {:else}
                <div class="p-8 text-center text-slate-500 text-xs">
                    <p>Pilih section untuk mengedit properti.</p>
                </div>
            {/if}
        </div>

    </div>

    <!-- Create Section Modal -->
    <Modal bind:open={isAddModalOpen} title="Tambah Section Baru" description="Pilih jenis komponen section yang ingin ditambahkan">
        <div class="space-y-4">
            <Input label="Nama Section (Contoh: Fitur Utama SaaS)" bind:value={newSectionName} placeholder="Ketik nama section..." />

            <div class="space-y-1.5">
                <label for="sec-type" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Jenis Template Section</label>
                <select id="sec-type" bind:value={newSectionType} class="w-full px-3.5 py-2 text-sm rounded-lg border bg-slate-900 border-slate-800 text-white">
                    <option value="hero">Hero Banner Utama</option>
                    <option value="features">Grid Fitur & Keunggulan</option>
                    <option value="stats">Bar Statistik & Metrik</option>
                    <option value="testimonials">Ulasan / Testimoni Pelanggan</option>
                    <option value="pricing">Paket Harga & Langganan</option>
                    <option value="faq">Pertanyaan Umum (FAQ)</option>
                    <option value="custom_html">Custom HTML Section</option>
                </select>
            </div>
        </div>

        {#snippet footer()}
            <Button variant="outline" size="sm" onclick={() => isAddModalOpen = false}>Batal</Button>
            <Button variant="primary" size="sm" onclick={createNewSection}>Buat Section Sekarang</Button>
        {/snippet}
    </Modal>
</AppLayout>
