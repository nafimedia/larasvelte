<script lang="ts">
    import {
        Zap,
        ShieldCheck,
        Layers,
        Lock,
        Smartphone,
        Code2,
        CheckCircle2,
        ArrowRight,
        Rocket,
        Terminal,
        Copy,
        Check,
        Sparkle,
        ChevronRight,
        HelpCircle,
        CheckCircle,
        Star
    } from 'lucide-svelte';

    interface Props {
        section: any;
        previewMode?: boolean;
    }

    let { section, previewMode = false }: Props = $props();

    const iconMap: Record<string, any> = {
        Zap,
        ShieldCheck,
        Layers,
        Lock,
        Smartphone,
        Code2,
        CheckCircle2,
        Star
    };

    let copied = $state(false);
    const installCommand = 'npx create-fairuzkit my-app';

    function copyCommand() {
        navigator.clipboard.writeText(installCommand);
        copied = true;
        setTimeout(() => copied = false, 2000);
    }
</script>

{#if section && section.is_active}
    <div
        id={section.section_id}
        class={`relative transition-all ${section.settings?.paddingTop || 'py-20'} ${
            section.settings?.background === 'slate-900/50' ? 'bg-slate-900/50 border-y border-slate-800/80 backdrop-blur-md' :
            section.settings?.background === 'slate-900/30' ? 'bg-slate-900/30 border-y border-slate-800/60' : ''
        }`}
    >
        <div class={`mx-auto px-4 sm:px-6 lg:px-8 ${
            section.settings?.containerWidth === '4xl' ? 'max-w-4xl' :
            section.settings?.containerWidth === '5xl' ? 'max-w-5xl' : 'max-w-7xl'
        }`}>

            <!-- HERO SECTION -->
            {#if section.type === 'hero'}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                    <div class="lg:col-span-7 space-y-8 text-center lg:text-left">
                        {#if section.content?.badge}
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-xs font-medium backdrop-blur-md shadow-inner">
                                <Sparkle class="w-3.5 h-3.5 text-amber-400 animate-spin" style="animation-duration: 4s;" />
                                <span>{section.content.badge}</span>
                                <ChevronRight class="w-3.5 h-3.5 text-indigo-400" />
                            </div>
                        {/if}

                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.15]">
                            {section.title} <br class="hidden sm:inline" />
                            <span class="bg-gradient-to-r from-indigo-400 via-purple-300 to-pink-400 bg-clip-text text-transparent">
                                {section.subtitle}
                            </span>
                        </h1>

                        <p class="text-base sm:text-lg text-slate-400 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                            {section.description}
                        </p>

                        <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                            {#if section.content?.primary_btn_text}
                                <a
                                    href={section.content.primary_btn_url || '/login'}
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-7 py-3.5 rounded-xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 hover:from-indigo-500 hover:to-pink-500 text-white font-semibold text-sm shadow-xl shadow-indigo-500/30 hover:shadow-indigo-500/50 transition-all duration-300 hover:-translate-y-0.5"
                                >
                                    <Rocket class="w-4 h-4" />
                                    <span>{section.content.primary_btn_text}</span>
                                </a>
                            {/if}

                            {#if section.content?.secondary_btn_text}
                                <a
                                    href={section.content.secondary_btn_url || '#quickstart'}
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white font-semibold text-sm transition-all duration-300"
                                >
                                    <Terminal class="w-4 h-4 text-indigo-400" />
                                    <span>{section.content.secondary_btn_text}</span>
                                </a>
                            {/if}
                        </div>
                    </div>

                    <div class="lg:col-span-5 relative flex justify-center">
                        <div class="relative rounded-3xl p-3 bg-gradient-to-b from-indigo-500/20 via-slate-800/40 to-slate-900/80 border border-slate-700/60 shadow-2xl backdrop-blur-2xl max-w-md w-full">
                            <div class="overflow-hidden rounded-2xl relative group">
                                <img
                                    src={section.content?.hero_image || '/images/hero-hijab.png'}
                                    alt="Hero Section Mascot"
                                    class="w-full h-auto object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-60"></div>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- STATS SECTION -->
            {:else if section.type === 'stats'}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    {#each (section.content?.items || []) as item}
                        <div class="space-y-1">
                            <p class="text-3xl sm:text-4xl font-extrabold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
                                {item.value}
                            </p>
                            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">
                                {item.label}
                            </p>
                        </div>
                    {/each}
                </div>

            <!-- FEATURES SECTION -->
            {:else if section.type === 'features'}
                <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                    <span class="text-xs font-semibold uppercase tracking-widest text-indigo-400 bg-indigo-500/10 px-3 py-1 rounded-full border border-indigo-500/20">
                        Features
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white">{section.title}</h2>
                    <p class="text-slate-400 text-sm sm:text-base">{section.subtitle}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    {#each (section.content?.items || []) as f}
                        <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800/80 hover:border-indigo-500/40 hover:bg-slate-900/90 transition-all duration-300 group hover:-translate-y-1">
                            <div class="w-12 h-12 rounded-xl border flex items-center justify-center mb-6 text-indigo-400 bg-indigo-500/10 border-indigo-500/20">
                                {#if iconMap[f.icon]}
                                    {@const IconComp = iconMap[f.icon]}
                                    <IconComp class="w-6 h-6" />
                                {:else}
                                    <Zap class="w-6 h-6" />
                                {/if}
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2 group-hover:text-indigo-300 transition-colors">
                                {f.title}
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">{f.desc}</p>
                        </div>
                    {/each}
                </div>

            <!-- FAQ SECTION -->
            {:else if section.type === 'faq'}
                <div class="text-center max-w-2xl mx-auto space-y-3 mb-12">
                    <h2 class="text-3xl font-extrabold text-white">{section.title}</h2>
                    <p class="text-slate-400 text-sm">{section.subtitle}</p>
                </div>

                <div class="space-y-4 max-w-3xl mx-auto">
                    {#each (section.content?.items || []) as faq}
                        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2">
                            <div class="flex items-center gap-3 font-semibold text-white text-base">
                                <HelpCircle class="w-5 h-5 text-indigo-400 shrink-0" />
                                <span>{faq.question}</span>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-400 pl-8 leading-relaxed">{faq.answer}</p>
                        </div>
                    {/each}
                </div>

            <!-- PRICING SECTION -->
            {:else if section.type === 'pricing'}
                <div class="text-center max-w-2xl mx-auto space-y-3 mb-14">
                    <h2 class="text-3xl font-extrabold text-white">{section.title}</h2>
                    <p class="text-slate-400 text-sm">{section.subtitle}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                    {#each (section.content?.plans || []) as plan}
                        <div class={`p-8 rounded-3xl border space-y-6 ${plan.is_popular ? 'bg-gradient-to-b from-indigo-950/60 to-slate-900 border-indigo-500/50 shadow-2xl relative' : 'bg-slate-900/60 border-slate-800'}`}>
                            {#if plan.is_popular}
                                <span class="absolute -top-3.5 right-6 px-3 py-1 rounded-full bg-indigo-600 text-white text-[10px] font-bold uppercase tracking-widest">Paling Populer</span>
                            {/if}
                            <div>
                                <h3 class="text-xl font-bold text-white">{plan.name}</h3>
                                <div class="mt-4 flex items-baseline gap-2">
                                    <span class="text-4xl font-extrabold text-white">{plan.price}</span>
                                    <span class="text-xs text-slate-400">/{plan.period}</span>
                                </div>
                            </div>
                            <ul class="space-y-3 text-xs text-slate-300">
                                {#each (plan.features || []) as ft}
                                    <li class="flex items-center gap-2">
                                        <CheckCircle class="w-4 h-4 text-emerald-400 shrink-0" />
                                        <span>{ft}</span>
                                    </li>
                                {/each}
                            </ul>
                            <a href="/register" class={`block text-center w-full py-3 rounded-xl font-semibold text-xs transition-all ${plan.is_popular ? 'bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-500/25' : 'bg-slate-800 hover:bg-slate-700 text-white'}`}>
                                {plan.button_text || 'Pilih Paket'}
                            </a>
                        </div>
                    {/each}
                </div>

            <!-- TESTIMONIALS SECTION -->
            {:else if section.type === 'testimonials'}
                <div class="text-center max-w-2xl mx-auto space-y-3 mb-14">
                    <h2 class="text-3xl font-extrabold text-white">{section.title}</h2>
                    <p class="text-slate-400 text-sm">{section.subtitle}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {#each (section.content?.items || []) as t}
                        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
                            <p class="text-xs sm:text-sm text-slate-300 italic leading-relaxed">"{t.comment}"</p>
                            <div class="flex items-center gap-3 pt-2 border-t border-slate-800">
                                <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center font-bold text-white text-sm">
                                    {t.name?.charAt(0) || 'U'}
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-white">{t.name}</h4>
                                    <p class="text-[10px] text-slate-400">{t.role}</p>
                                </div>
                            </div>
                        </div>
                    {/each}
                </div>

            <!-- CUSTOM HTML SECTION -->
            {:else}
                {@html section.content?.html_content || section.description || '<p>Section content</p>'}
            {/if}

        </div>
    </div>
{/if}
