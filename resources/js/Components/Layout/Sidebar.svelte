<script lang="ts">
    import { page, Link } from '@inertiajs/svelte';
    import {
        LayoutDashboard,
        Users,
        ShieldCheck,
        Activity,
        Settings,
        User,
        Sparkles,
        FileText,
        Newspaper,
        Folder,
        Tag,
        MessageSquare,
        BarChart3,
        Calendar,
        Menu,
        Image,
        FileSpreadsheet,
        ArrowRightLeft,
        ChevronDown,
        ChevronRight,
        ChevronLeft,
        PanelLeftClose,
        PanelLeftOpen,
    } from 'lucide-svelte';
    import type { PageProps } from '@/lib/types';

    interface Props {
        isOpen?: boolean;
        onClose?: () => void;
    }

    let { isOpen = false, onClose }: Props = $props();

    const pageProps = $derived(page.props as unknown as PageProps);
    const user = $derived(pageProps.auth.user);
    const currentUrl = $derived(page.url);

    // Sidebar Collapsed (Icon-Only Mode) State
    let isCollapsed = $state(
        typeof localStorage !== 'undefined'
            ? localStorage.getItem('admin_sidebar_collapsed') === 'true'
            : false
    );

    function toggleCollapse() {
        isCollapsed = !isCollapsed;
        if (typeof localStorage !== 'undefined') {
            localStorage.setItem('admin_sidebar_collapsed', isCollapsed ? 'true' : 'false');
        }
    }

    function hasPermission(permission: string): boolean {
        if (!user) return false;
        if (user.roles?.includes('Super Admin')) return true;
        return user.permissions?.includes(permission) ?? false;
    }

    // Group Accordion State
    let openGroups = $state<Record<string, boolean>>({
        content: true,
        builder: true,
        seo: true,
        system: true,
    });

    // Auto expand group if active URL matches
    $effect(() => {
        if (currentUrl.startsWith('/admin/cms/posts') || currentUrl.startsWith('/admin/cms/pages') || currentUrl.startsWith('/admin/cms/categories') || currentUrl.startsWith('/admin/cms/tags') || currentUrl.startsWith('/admin/cms/calendar') || currentUrl.startsWith('/admin/cms/comments')) {
            openGroups.content = true;
        }
        if (currentUrl.startsWith('/admin/landing-builder') || currentUrl.startsWith('/admin/cms/menus') || currentUrl.startsWith('/admin/cms/forms')) {
            openGroups.builder = true;
        }
        if (currentUrl.startsWith('/admin/cms/redirects') || currentUrl.startsWith('/admin/cms/analytics')) {
            openGroups.seo = true;
        }
        if (currentUrl.startsWith('/admin/users') || currentUrl.startsWith('/admin/roles') || currentUrl.startsWith('/admin/activity-logs') || currentUrl.startsWith('/admin/settings')) {
            openGroups.system = true;
        }
    });

    function toggleGroup(groupKey: string) {
        openGroups[groupKey] = !openGroups[groupKey];
    }

    // Menu Structure Definition
    const menuGroups = $derived([
        {
            key: 'main',
            title: null,
            items: [
                {
                    name: 'Dashboard',
                    href: '/admin/dashboard',
                    icon: LayoutDashboard,
                    active: currentUrl.startsWith('/admin/dashboard'),
                    show: true,
                },
            ],
        },
        {
            key: 'content',
            title: 'Content Management',
            items: [
                {
                    name: 'Artikel & Blog',
                    href: '/admin/cms/posts',
                    icon: Newspaper,
                    active: currentUrl.startsWith('/admin/cms/posts'),
                    show: true,
                },
                {
                    name: 'Halaman Web',
                    href: '/admin/cms/pages',
                    icon: FileText,
                    active: currentUrl.startsWith('/admin/cms/pages'),
                    show: true,
                },
                {
                    name: 'Kategori Artikel',
                    href: '/admin/cms/categories',
                    icon: Folder,
                    active: currentUrl.startsWith('/admin/cms/categories'),
                    show: true,
                },
                {
                    name: 'Tag Artikel',
                    href: '/admin/cms/tags',
                    icon: Tag,
                    active: currentUrl.startsWith('/admin/cms/tags'),
                    show: true,
                },
                {
                    name: 'Kalender Publikasi',
                    href: '/admin/cms/calendar',
                    icon: Calendar,
                    active: currentUrl.startsWith('/admin/cms/calendar'),
                    show: true,
                },
                {
                    name: 'Komentar',
                    href: '/admin/cms/comments',
                    icon: MessageSquare,
                    active: currentUrl.startsWith('/admin/cms/comments'),
                    show: true,
                },
            ],
        },
        {
            key: 'builder',
            title: 'Website Builder',
            items: [
                {
                    name: 'Landing Builder',
                    href: '/admin/landing-builder',
                    icon: Sparkles,
                    active: currentUrl.startsWith('/admin/landing-builder'),
                    show: true,
                },
                {
                    name: 'Menu Navigasi',
                    href: '/admin/cms/menus',
                    icon: Menu,
                    active: currentUrl.startsWith('/admin/cms/menus'),
                    show: true,
                },
                {
                    name: 'Form Builder',
                    href: '/admin/cms/forms',
                    icon: FileSpreadsheet,
                    active: currentUrl.startsWith('/admin/cms/forms'),
                    show: true,
                },
            ],
        },
        {
            key: 'media',
            title: 'Media',
            items: [
                {
                    name: 'Media Library',
                    href: '/admin/cms/media',
                    icon: Image,
                    active: currentUrl.startsWith('/admin/cms/media'),
                    show: true,
                },
            ],
        },
        {
            key: 'seo',
            title: 'SEO & Optimization',
            items: [
                {
                    name: 'Redirect Manager',
                    href: '/admin/cms/redirects',
                    icon: ArrowRightLeft,
                    active: currentUrl.startsWith('/admin/cms/redirects'),
                    show: true,
                },
                {
                    name: 'CMS Analitik',
                    href: '/admin/cms/analytics',
                    icon: BarChart3,
                    active: currentUrl.startsWith('/admin/cms/analytics'),
                    show: true,
                },
            ],
        },
        {
            key: 'system',
            title: 'System Management',
            items: [
                {
                    name: 'Manajemen Pengguna',
                    href: '/admin/users',
                    icon: Users,
                    active: currentUrl.startsWith('/admin/users'),
                    show: hasPermission('users.view'),
                },
                {
                    name: 'Role & Izin (RBAC)',
                    href: '/admin/roles',
                    icon: ShieldCheck,
                    active: currentUrl.startsWith('/admin/roles'),
                    show: hasPermission('roles.view'),
                },
                {
                    name: 'Log Aktivitas',
                    href: '/admin/activity-logs',
                    icon: Activity,
                    active: currentUrl.startsWith('/admin/activity-logs'),
                    show: hasPermission('activity_logs.view'),
                },
                {
                    name: 'Pengaturan Situs',
                    href: '/admin/settings',
                    icon: Settings,
                    active: currentUrl.startsWith('/admin/settings'),
                    show: hasPermission('settings.view'),
                },
            ],
        },
        {
            key: 'account',
            title: 'Account',
            items: [
                {
                    name: 'Profil Saya',
                    href: '/profile',
                    icon: User,
                    active: currentUrl === '/profile',
                    show: true,
                },
            ],
        },
    ]);
</script>

<!-- Mobile Overlay Backdrop -->
{#if isOpen}
    <button
        type="button"
        aria-label="Tutup sidebar"
        class="fixed inset-0 z-40 w-full h-full bg-slate-950/60 backdrop-blur-xs lg:hidden transition-opacity border-none outline-none cursor-default"
        onclick={onClose}
    ></button>
{/if}

<!-- Sidebar Container -->
<aside
    class={`fixed top-0 bottom-0 left-0 z-40 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 transition-all duration-300 lg:translate-x-0 flex flex-col justify-between ${
        isOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full'
    } ${isCollapsed ? 'lg:w-20' : 'lg:w-64'}`}
>
    <!-- Top Part: Logo Banner & Nav Groups -->
    <div class="flex flex-col flex-1 min-h-0">
        <!-- Brand Banner -->
        <div class={`h-16 border-b border-slate-200 dark:border-slate-800 flex items-center px-4 justify-between shrink-0 ${isCollapsed ? 'justify-center' : 'px-6'}`}>
            <Link href="/admin/dashboard" class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-md shadow-indigo-500/25 shrink-0">
                    FK
                </div>
                {#if !isCollapsed}
                    <div class="truncate">
                        <h1 class="font-bold text-sm text-slate-900 dark:text-slate-100 leading-none">FairuzKit</h1>
                        <span class="text-[10px] text-indigo-500 font-semibold tracking-wider uppercase">Enterprise CMS</span>
                    </div>
                {/if}
            </Link>
        </div>

        <!-- Navigation Scrollable Body -->
        <nav class="flex-1 overflow-y-auto p-3 space-y-4 pr-1">
            {#each menuGroups as group}
                {#if group.items.some(i => i.show)}
                    <div class="space-y-1">
                        <!-- Group Header (Accordion Toggle) -->
                        {#if group.title && !isCollapsed}
                            <button
                                type="button"
                                onclick={() => toggleGroup(group.key)}
                                class="w-full flex items-center justify-between px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 hover:text-slate-300 transition-colors"
                            >
                                <span>{group.title}</span>
                                {#if openGroups[group.key]}
                                    <ChevronDown class="w-3 h-3 text-slate-500" />
                                {:else}
                                    <ChevronRight class="w-3 h-3 text-slate-500" />
                                {/if}
                            </button>
                        {:else if group.title && isCollapsed}
                            <div class="my-2 border-t border-slate-200 dark:border-slate-800/80"></div>
                        {/if}

                        <!-- Group Items List -->
                        {#if !group.title || openGroups[group.key] || isCollapsed}
                            <div class="space-y-1">
                                {#each group.items as item}
                                    {#if item.show}
                                        <Link
                                            href={item.href}
                                            onclick={onClose}
                                            title={isCollapsed ? item.name : undefined}
                                            class={`flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all group ${
                                                item.active
                                                    ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200/60 dark:border-indigo-800/60 shadow-xs'
                                                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-100'
                                            } ${isCollapsed ? 'justify-center px-0' : ''}`}
                                        >
                                            <item.icon
                                                class={`w-4 h-4 shrink-0 transition-transform duration-200 group-hover:scale-110 ${
                                                    item.active ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400'
                                                }`}
                                            />
                                            {#if !isCollapsed}
                                                <span class="truncate">{item.name}</span>
                                            {/if}
                                        </Link>
                                    {/if}
                                {/each}
                            </div>
                        {/if}
                    </div>
                {/if}
            {/each}
        </nav>
    </div>

    <!-- Bottom Part: Collapse Toggle Button (Desktop Only) -->
    <div class="hidden lg:block border-t border-slate-200 dark:border-slate-800 p-3 shrink-0">
        <button
            type="button"
            onclick={toggleCollapse}
            class="w-full flex items-center justify-center gap-2 p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-100 transition-all text-xs font-semibold"
            title={isCollapsed ? 'Perluas Sidebar' : 'Kecilkan Sidebar'}
        >
            {#if isCollapsed}
                <PanelLeftOpen class="w-4 h-4 text-indigo-400" />
            {:else}
                <PanelLeftClose class="w-4 h-4 text-slate-400" />
                <span>Kecilkan Sidebar</span>
            {/if}
        </button>
    </div>
</aside>
