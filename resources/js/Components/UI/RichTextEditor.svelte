<script lang="ts">
    import {
        Bold,
        Italic,
        Underline,
        Strikethrough,
        Heading1,
        Heading2,
        Heading3,
        List,
        ListOrdered,
        Quote,
        Code,
        Link as LinkIcon,
        Image as ImageIcon,
        Table as TableIcon,
        Maximize2,
        Minimize2,
        Undo,
        Redo,
        FileText
    } from 'lucide-svelte';

    interface Props {
        value?: string;
        placeholder?: string;
        minHeight?: string;
        oninput?: (val: string) => void;
    }

    let {
        value = $bindable(''),
        placeholder = 'Tulis konten artikel atau halaman di sini...',
        minHeight = 'min-h-[350px]',
        oninput
    }: Props = $props();

    let isFullscreen = $state(false);
    let editorArea = $state<HTMLTextAreaElement | null>(null);

    // Compute Word & Character count dynamically with Svelte 5 $derived
    const characterCount = $derived(value ? value.length : 0);
    const wordCount = $derived(value ? value.trim().split(/\s+/).filter(Boolean).length : 0);

    function insertFormatting(prefix: string, suffix: string = '') {
        if (!editorArea) return;
        const start = editorArea.selectionStart;
        const end = editorArea.selectionEnd;
        const selectedText = value.substring(start, end);
        const replacement = prefix + (selectedText || 'Teks') + suffix;

        value = value.substring(0, start) + replacement + value.substring(end);
        if (oninput) oninput(value);

        setTimeout(() => {
            if (editorArea) {
                editorArea.focus();
                editorArea.setSelectionRange(start + prefix.length, start + prefix.length + (selectedText ? selectedText.length : 4));
            }
        }, 0);
    }

    function toggleFullscreen() {
        isFullscreen = !isFullscreen;
    }
</script>

<div class={`rounded-xl border border-slate-800 bg-slate-950 transition-all ${
    isFullscreen ? 'fixed inset-4 z-50 shadow-2xl flex flex-col' : 'w-full'
}`}>
    <!-- Editor Toolbar Header -->
    <div class="flex flex-wrap items-center justify-between gap-2 p-2.5 border-b border-slate-800 bg-slate-900/80 rounded-t-xl">
        <div class="flex flex-wrap items-center gap-1">
            <!-- Formatting Buttons -->
            <button
                type="button"
                onclick={() => insertFormatting('**', '**')}
                class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 text-xs flex items-center gap-1"
                title="Bold (Tebal)"
            >
                <Bold class="w-4 h-4" />
            </button>
            <button
                type="button"
                onclick={() => insertFormatting('*', '*')}
                class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 text-xs flex items-center gap-1"
                title="Italic (Miring)"
            >
                <Italic class="w-4 h-4" />
            </button>
            <button
                type="button"
                onclick={() => insertFormatting('<u>', '</u>')}
                class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 text-xs flex items-center gap-1"
                title="Underline (Garis Bawah)"
            >
                <Underline class="w-4 h-4" />
            </button>
            <button
                type="button"
                onclick={() => insertFormatting('~~', '~~')}
                class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 text-xs flex items-center gap-1"
                title="Strikethrough"
            >
                <Strikethrough class="w-4 h-4" />
            </button>

            <div class="h-4 w-px bg-slate-800 mx-1"></div>

            <!-- Headings -->
            <button
                type="button"
                onclick={() => insertFormatting('# ')}
                class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 text-xs"
                title="Heading 1"
            >
                <Heading1 class="w-4 h-4" />
            </button>
            <button
                type="button"
                onclick={() => insertFormatting('## ')}
                class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 text-xs"
                title="Heading 2"
            >
                <Heading2 class="w-4 h-4" />
            </button>
            <button
                type="button"
                onclick={() => insertFormatting('### ')}
                class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 text-xs"
                title="Heading 3"
            >
                <Heading3 class="w-4 h-4" />
            </button>

            <div class="h-4 w-px bg-slate-800 mx-1"></div>

            <!-- Lists & Blocks -->
            <button
                type="button"
                onclick={() => insertFormatting('- ')}
                class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 text-xs"
                title="Bullet List"
            >
                <List class="w-4 h-4" />
            </button>
            <button
                type="button"
                onclick={() => insertFormatting('1. ')}
                class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 text-xs"
                title="Numbered List"
            >
                <ListOrdered class="w-4 h-4" />
            </button>
            <button
                type="button"
                onclick={() => insertFormatting('> ')}
                class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 text-xs"
                title="Quote"
            >
                <Quote class="w-4 h-4" />
            </button>
            <button
                type="button"
                onclick={() => insertFormatting('```\n', '\n```')}
                class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 text-xs"
                title="Code Block"
            >
                <Code class="w-4 h-4" />
            </button>

            <div class="h-4 w-px bg-slate-800 mx-1"></div>

            <!-- Insertions -->
            <button
                type="button"
                onclick={() => insertFormatting('[', '](https://example.com)')}
                class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 text-xs"
                title="Insert Link"
            >
                <LinkIcon class="w-4 h-4" />
            </button>
            <button
                type="button"
                onclick={() => insertFormatting('![Alt Text](', ')')}
                class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 text-xs"
                title="Insert Image URL"
            >
                <ImageIcon class="w-4 h-4" />
            </button>
        </div>

        <div class="flex items-center gap-2 text-xs text-slate-400">
            <span class="flex items-center gap-1 font-mono">
                <FileText class="w-3.5 h-3.5 text-indigo-400" />
                {wordCount} kata | {characterCount} karakter
            </span>

            <button
                type="button"
                onclick={toggleFullscreen}
                class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800"
                title={isFullscreen ? 'Keluar Fullscreen' : 'Fullscreen'}
            >
                {#if isFullscreen}
                    <Minimize2 class="w-4 h-4" />
                {:else}
                    <Maximize2 class="w-4 h-4" />
                {/if}
            </button>
        </div>
    </div>

    <!-- Text Area Editor Canvas -->
    <textarea
        bind:this={editorArea}
        bind:value
        oninput={(e) => oninput && oninput((e.target as HTMLTextAreaElement).value)}
        {placeholder}
        class={`w-full p-4 bg-slate-950 text-slate-100 font-mono text-sm border-none focus:outline-none focus:ring-0 leading-relaxed resize-y ${
            isFullscreen ? 'flex-1 rounded-b-xl' : `${minHeight} rounded-b-xl`
        }`}
    ></textarea>
</div>
