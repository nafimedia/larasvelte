<script lang="ts">
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import Badge from '@/Components/UI/Badge.svelte';
    import Avatar from '@/Components/UI/Avatar.svelte';
    import { Users, ShieldCheck, Activity, CheckCircle2, ArrowUpRight, Zap, Code, Shield } from 'lucide-svelte';
    import type { ActivityItem } from '@/lib/types';

    interface Props {
        stats: {
            total_users: number;
            active_users: number;
            total_roles: number;
            recent_activities_count: number;
        };
        recentActivities: ActivityItem[];
        usersByRole: { name: string; users_count: number }[];
    }

    let { stats, recentActivities = [], usersByRole = [] }: Props = $props();
</script>

<AppLayout title="Dashboard Utama">
    <!-- Header Hero Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-800 p-6 sm:p-8 text-white shadow-xl shadow-indigo-500/10">
        <div class="relative z-10 space-y-2 max-w-2xl">
            <Badge variant="primary" class="bg-white/10 text-white border-white/20 backdrop-blur-xs">
                Laravel 13 &bull; Svelte 5 &bull; Tailwind v4
            </Badge>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Selamat Datang di LaraSvelte Starterkit!</h1>
            <p class="text-xs sm:text-sm text-indigo-100/90 leading-relaxed">
                Fondasi starter kit tingkat produksi lengkap dengan Role-Based Access Control (RBAC), Spatie Media Library, Audit Logs, Data Table, dan API Sanctum Siap Pakai.
            </p>
        </div>
        <div class="absolute -right-8 -bottom-8 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    <!-- Stats Grid Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <Card class="hover:border-indigo-300 dark:hover:border-indigo-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Pengguna</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100 mt-1">{stats.total_users}</h3>
                </div>
                <div class="p-3 bg-indigo-50 dark:bg-indigo-950/60 rounded-xl text-indigo-600 dark:text-indigo-400">
                    <Users class="w-6 h-6" />
                </div>
            </div>
        </Card>

        <Card class="hover:border-emerald-300 dark:hover:border-emerald-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pengguna Aktif</p>
                    <h3 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{stats.active_users}</h3>
                </div>
                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/60 rounded-xl text-emerald-600 dark:text-emerald-400">
                    <CheckCircle2 class="w-6 h-6" />
                </div>
            </div>
        </Card>

        <Card class="hover:border-purple-300 dark:hover:border-purple-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Peran & RBAC</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100 mt-1">{stats.total_roles} Role</h3>
                </div>
                <div class="p-3 bg-purple-50 dark:bg-purple-950/60 rounded-xl text-purple-600 dark:text-purple-400">
                    <ShieldCheck class="w-6 h-6" />
                </div>
            </div>
        </Card>

        <Card class="hover:border-amber-300 dark:hover:border-amber-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aktivitas (7 Hari)</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100 mt-1">{stats.recent_activities_count} Event</h3>
                </div>
                <div class="p-3 bg-amber-50 dark:bg-amber-950/60 rounded-xl text-amber-600 dark:text-amber-400">
                    <Activity class="w-6 h-6" />
                </div>
            </div>
        </Card>
    </div>

    <!-- Main Content Split Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Audit Log Feed (2 Cols) -->
        <div class="lg:col-span-2">
            <Card title="Aktivitas Terkini Sistem" description="Riwayat log tindakan pengguna dan audit trail otomatis">
                <div class="space-y-4">
                    {#if recentActivities.length === 0}
                        <p class="text-xs text-slate-400 text-center py-6">Belum ada aktivitas tercatat</p>
                    {:else}
                        {#each recentActivities as act}
                            <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                                <Avatar src={act.causer_avatar} alt={act.causer_name} size="sm" />
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-slate-900 dark:text-slate-100">{act.causer_name}</p>
                                    <p class="text-xs text-slate-600 dark:text-slate-300 truncate mt-0.5">{act.description}</p>
                                </div>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 shrink-0 font-mono">{act.created_at}</span>
                            </div>
                        {/each}
                    {/if}
                </div>
            </Card>
        </div>

        <!-- Role Distribution & Tech Stack Info (1 Col) -->
        <div class="space-y-6">
            <!-- User Distribution -->
            <Card title="Distribusi Pengguna per Role">
                <div class="space-y-3">
                    {#each usersByRole as item}
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-medium text-slate-700 dark:text-slate-300">{item.name}</span>
                            <Badge variant="slate">{item.users_count} Pengguna</Badge>
                        </div>
                    {/each}
                </div>
            </Card>

            <!-- Features Checklist Card -->
            <Card title="Fitur Siap Pakai" description="Sudah dikonfigurasi dan siap dipakai">
                <ul class="space-y-2 text-xs text-slate-600 dark:text-slate-300">
                    <li class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-medium">
                        <CheckCircle2 class="w-4 h-4" /> Svelte 5 Runes Reactive State
                    </li>
                    <li class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-medium">
                        <CheckCircle2 class="w-4 h-4" /> Spatie RBAC Role & Permission Matrix
                    </li>
                    <li class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-medium">
                        <CheckCircle2 class="w-4 h-4" /> Spatie Media Library Avatar Upload
                    </li>
                    <li class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-medium">
                        <CheckCircle2 class="w-4 h-4" /> Audit Activity Log Traceability
                    </li>
                    <li class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-medium">
                        <CheckCircle2 class="w-4 h-4" /> TanStack Data Table + CSV Export
                    </li>
                    <li class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-medium">
                        <CheckCircle2 class="w-4 h-4" /> Sanctum REST API Endpoints
                    </li>
                </ul>
            </Card>
        </div>
    </div>
</AppLayout>
