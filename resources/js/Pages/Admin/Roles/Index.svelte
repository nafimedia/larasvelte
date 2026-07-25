<script lang="ts">
    import { useForm, router } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import Input from '@/Components/UI/Input.svelte';
    import Modal from '@/Components/UI/Modal.svelte';
    import Dialog from '@/Components/UI/Dialog.svelte';
    import Badge from '@/Components/UI/Badge.svelte';
    import { ShieldCheck, Plus, Edit3, Trash2, CheckSquare } from 'lucide-svelte';
    import type { RoleItem, PermissionItem } from '@/lib/types';

    interface Props {
        roles: RoleItem[];
        allPermissions: PermissionItem[];
    }

    let { roles = [], allPermissions = [] }: Props = $props();

    let isCreateModalOpen = $state(false);
    let isEditModalOpen = $state(false);
    let isDeleteDialogOpen = $state(false);
    let selectedRole = $state<RoleItem | null>(null);

    const createForm = useForm({
        name: '',
        permissions: [] as string[],
    });

    const editForm = useForm({
        name: '',
        permissions: [] as string[],
    });

    function openCreateModal() {
        createForm.reset();
        isCreateModalOpen = true;
    }

    function submitCreate(e: Event) {
        e.preventDefault();
        createForm.post('/admin/roles', {
            onSuccess: () => {
                isCreateModalOpen = false;
                createForm.reset();
            },
        });
    }

    function openEditModal(role: RoleItem) {
        selectedRole = role;
        editForm.name = role.name;
        editForm.permissions = [...role.permissions];
        isEditModalOpen = true;
    }

    function submitEdit(e: Event) {
        e.preventDefault();
        if (!selectedRole) return;

        editForm.put(`/admin/roles/${selectedRole.id}`, {
            onSuccess: () => {
                isEditModalOpen = false;
            },
        });
    }

    function togglePermission(formObj: any, permName: string) {
        if (formObj.permissions.includes(permName)) {
            formObj.permissions = formObj.permissions.filter((p: string) => p !== permName);
        } else {
            formObj.permissions = [...formObj.permissions, permName];
        }
    }

    function confirmDelete(role: RoleItem) {
        selectedRole = role;
        isDeleteDialogOpen = true;
    }

    function executeDelete() {
        if (!selectedRole) return;
        router.delete(`/admin/roles/${selectedRole.id}`, { preserveScroll: true });
    }
</script>

<AppLayout title="Manajemen Role & Permission (RBAC)">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Manajemen Peran & Izin (RBAC)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Konfigurasi hak akses pengguna dengan Spatie Laravel Permission</p>
        </div>
        <Button variant="primary" size="md" onclick={openCreateModal}>
            <Plus class="w-4 h-4" />
            <span>Tambah Role Baru</span>
        </Button>
    </div>

    <!-- Roles Grid Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {#each roles as role}
            <Card class="flex flex-col justify-between hover:border-indigo-300 dark:hover:border-indigo-800 transition-all">
                <div class="space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-indigo-50 dark:bg-indigo-950/60 rounded-xl text-indigo-600 dark:text-indigo-400">
                                <ShieldCheck class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-slate-100">{role.name}</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{role.users_count} Pengguna Terdaftar</p>
                            </div>
                        </div>

                        {#if !['Super Admin', 'Admin', 'User'].includes(role.name)}
                            <div class="flex items-center gap-1">
                                <Button variant="ghost" size="icon" onclick={() => openEditModal(role)}>
                                    <Edit3 class="w-4 h-4 text-slate-500" />
                                </Button>
                                <Button variant="ghost" size="icon" onclick={() => confirmDelete(role)}>
                                    <Trash2 class="w-4 h-4 text-rose-500" />
                                </Button>
                            </div>
                        {/if}
                    </div>

                    <!-- Permissions List -->
                    <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Izin Akses ({role.permissions.length}):</p>
                        <div class="flex flex-wrap gap-1.5 max-h-32 overflow-y-auto">
                            {#if role.name === 'Super Admin'}
                                <Badge variant="primary">Akses Penuh Seluruh Fitur (All Permissions)</Badge>
                            {:else if role.permissions.length === 0}
                                <span class="text-xs text-slate-400 italic">Belum ada izin khusus</span>
                            {:else}
                                {#each role.permissions as perm}
                                    <Badge variant="slate">{perm}</Badge>
                                {/each}
                            {/if}
                        </div>
                    </div>
                </div>

                {#snippet footer()}
                    <div class="flex items-center justify-between text-xs text-slate-400">
                        <span>Dibuat: {role.created_at}</span>
                        <Button variant="ghost" size="sm" onclick={() => openEditModal(role)}>Kelola Matrix Izin</Button>
                    </div>
                {/snippet}
            </Card>
        {/each}
    </div>

    <!-- Create Role Modal -->
    <Modal bind:open={isCreateModalOpen} title="Tambah Role Baru" description="Buat peran baru dan pilih hak aksesnya" maxWidth="lg">
        <form onsubmit={submitCreate} class="space-y-4">
            <Input
                label="Nama Role"
                placeholder="Contoh: Manager / Editor"
                bind:value={createForm.name}
                error={createForm.errors.name}
                required
            />

            <div class="space-y-2">
                <span class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                    Matriks Hak Akses (Permissions)
                </span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-60 overflow-y-auto p-3 border border-slate-200 dark:border-slate-800 rounded-xl bg-slate-50/50 dark:bg-slate-900/50">
                    {#each allPermissions as p}
                        <label class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-white dark:hover:bg-slate-800 transition-colors cursor-pointer text-xs">
                            <input
                                type="checkbox"
                                checked={createForm.permissions.includes(p.name)}
                                onchange={() => togglePermission(createForm, p.name)}
                                class="rounded border-slate-300 dark:border-slate-800 text-indigo-600 focus:ring-indigo-500"
                            />
                            <span class="font-mono text-slate-700 dark:text-slate-300">{p.name}</span>
                        </label>
                    {/each}
                </div>
            </div>
        </form>

        {#snippet footer()}
            <Button variant="outline" size="sm" onclick={() => isCreateModalOpen = false}>Batal</Button>
            <Button variant="primary" size="sm" onclick={submitCreate} disabled={createForm.processing}>Simpan Role</Button>
        {/snippet}
    </Modal>

    <!-- Edit Role Modal -->
    <Modal bind:open={isEditModalOpen} title={`Edit Role: ${selectedRole?.name}`} description="Perbarui matriks hak akses" maxWidth="lg">
        <form onsubmit={submitEdit} class="space-y-4">
            <Input
                label="Nama Role"
                bind:value={editForm.name}
                error={editForm.errors.name}
                required
            />

            <div class="space-y-2">
                <span class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                    Matriks Hak Akses (Permissions)
                </span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-60 overflow-y-auto p-3 border border-slate-200 dark:border-slate-800 rounded-xl bg-slate-50/50 dark:bg-slate-900/50">
                    {#each allPermissions as p}
                        <label class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-white dark:hover:bg-slate-800 transition-colors cursor-pointer text-xs">
                            <input
                                type="checkbox"
                                checked={editForm.permissions.includes(p.name)}
                                onchange={() => togglePermission(editForm, p.name)}
                                class="rounded border-slate-300 dark:border-slate-800 text-indigo-600 focus:ring-indigo-500"
                            />
                            <span class="font-mono text-slate-700 dark:text-slate-300">{p.name}</span>
                        </label>
                    {/each}
                </div>
            </div>
        </form>

        {#snippet footer()}
            <Button variant="outline" size="sm" onclick={() => isEditModalOpen = false}>Batal</Button>
            <Button variant="primary" size="sm" onclick={submitEdit} disabled={editForm.processing}>Simpan Perubahan</Button>
        {/snippet}
    </Modal>

    <!-- Delete Dialog -->
    <Dialog
        bind:open={isDeleteDialogOpen}
        title="Hapus Role"
        message={`Apakah Anda yakin ingin menghapus Role "${selectedRole?.name}"?`}
        type="danger"
        confirmText="Hapus Role"
        onconfirm={executeDelete}
    />
</AppLayout>
