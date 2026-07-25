<script lang="ts">
    import { useForm, router } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import DataTable from '@/Components/DataTable/DataTable.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import Input from '@/Components/UI/Input.svelte';
    import Modal from '@/Components/UI/Modal.svelte';
    import Dialog from '@/Components/UI/Dialog.svelte';
    import Badge from '@/Components/UI/Badge.svelte';
    import Avatar from '@/Components/UI/Avatar.svelte';
    import FileUpload from '@/Components/UI/FileUpload.svelte';
    import { UserPlus, Edit3, Trash2, Shield, CheckCircle, XCircle } from 'lucide-svelte';
    import type { User, PaginatedData } from '@/lib/types';

    interface Props {
        users: PaginatedData<User>;
        roles: string[];
        filters: { search: string; role: string; status: string; per_page: number };
    }

    let { users, roles = [], filters }: Props = $props();

    // Modal & Dialog state
    let isCreateModalOpen = $state(false);
    let isEditModalOpen = $state(false);
    let isDeleteDialogOpen = $state(false);
    let selectedUser = $state<User | null>(null);

    // Form for Create User
    const createForm = useForm({
        name: '',
        email: '',
        password: '',
        role: 'User',
        is_active: true,
        avatar: null as File | null,
    });

    // Form for Edit User
    const editForm = useForm({
        name: '',
        email: '',
        password: '',
        role: 'User',
        is_active: true,
        avatar: null as File | null,
    });

    function openCreateModal() {
        createForm.reset();
        createForm.role = roles[0] || 'User';
        isCreateModalOpen = true;
    }

    function submitCreate(e: Event) {
        e.preventDefault();
        createForm.post('/admin/users', {
            onSuccess: () => {
                isCreateModalOpen = false;
                createForm.reset();
            },
        });
    }

    function openEditModal(user: User) {
        selectedUser = user;
        editForm.name = user.name;
        editForm.email = user.email;
        editForm.role = user.roles[0] || 'User';
        editForm.is_active = user.is_active;
        editForm.password = '';
        editForm.avatar = null;
        isEditModalOpen = true;
    }

    function submitEdit(e: Event) {
        e.preventDefault();
        if (!selectedUser) return;

        editForm.post(`/admin/users/${selectedUser.id}`, {
            onSuccess: () => {
                isEditModalOpen = false;
            },
        });
    }

    function toggleUserStatus(user: User) {
        router.patch(`/admin/users/${user.id}/toggle-status`, {}, { preserveScroll: true });
    }

    function confirmDelete(user: User) {
        selectedUser = user;
        isDeleteDialogOpen = true;
    }

    function executeDelete() {
        if (!selectedUser) return;
        router.delete(`/admin/users/${selectedUser.id}`, { preserveScroll: true });
    }

    const filterOptions = $derived([
        {
            label: 'Role',
            key: 'role',
            options: roles.map(r => ({ label: r, value: r }))
        },
        {
            label: 'Status',
            key: 'status',
            options: [
                { label: 'Aktif', value: '1' },
                { label: 'Non-Aktif', value: '0' }
            ]
        }
    ]);
</script>

<AppLayout title="Manajemen Pengguna">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Manajemen Pengguna</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola pengguna sistem, penetapan role, dan status akun</p>
        </div>
        <Button variant="primary" size="md" onclick={openCreateModal}>
            <UserPlus class="w-4 h-4" />
            <span>Tambah Pengguna Baru</span>
        </Button>
    </div>

    <!-- User Data Table -->
    <DataTable
        searchable={true}
        searchPlaceholder="Cari nama atau email..."
        searchValue={filters.search}
        filters={filters}
        filterOptions={filterOptions}
        pagination={users}
        exportFilename="daftar-pengguna.csv"
        exportData={users.data}
        routePath="/admin/users"
    >
        {#snippet header()}
            <tr>
                <th class="px-4 py-3">Pengguna</th>
                <th class="px-4 py-3">Role (Peran)</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Terdaftar</th>
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
        {/snippet}

        {#each users.data as user}
            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <Avatar src={user.avatar_url} alt={user.name} size="md" />
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-slate-100">{user.name}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{user.email}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-1">
                        {#each user.roles as role}
                            <Badge variant={role === 'Super Admin' ? 'primary' : role === 'Admin' ? 'info' : 'slate'}>
                                {role}
                            </Badge>
                        {/each}
                    </div>
                </td>
                <td class="px-4 py-3">
                    <button
                        type="button"
                        onclick={() => toggleUserStatus(user)}
                        class="cursor-pointer focus:outline-none"
                    >
                        {#if user.is_active}
                            <Badge variant="success" class="hover:opacity-80">
                                <CheckCircle class="w-3 h-3 mr-1" /> Aktif
                            </Badge>
                        {:else}
                            <Badge variant="danger" class="hover:opacity-80">
                                <XCircle class="w-3 h-3 mr-1" /> Non-Aktif
                            </Badge>
                        {/if}
                    </button>
                </td>
                <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                    {user.created_at}
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-1.5">
                        <Button variant="ghost" size="icon" onclick={() => openEditModal(user)} title="Edit Pengguna">
                            <Edit3 class="w-4 h-4 text-slate-600 dark:text-slate-400" />
                        </Button>
                        <Button variant="ghost" size="icon" onclick={() => confirmDelete(user)} title="Hapus Pengguna">
                            <Trash2 class="w-4 h-4 text-rose-500" />
                        </Button>
                    </div>
                </td>
            </tr>
        {/each}
    </DataTable>

    <!-- Create User Modal -->
    <Modal bind:open={isCreateModalOpen} title="Tambah Pengguna Baru" description="Isi formulir untuk mendaftarkan akun baru">
        <form onsubmit={submitCreate} class="space-y-4">
            <FileUpload
                label="Foto Profil (Avatar)"
                bind:file={createForm.avatar}
                error={createForm.errors.avatar}
            />

            <Input
                label="Nama Lengkap"
                placeholder="John Doe"
                bind:value={createForm.name}
                error={createForm.errors.name}
                required
            />

            <Input
                type="email"
                label="Alamat Email"
                placeholder="john@example.com"
                bind:value={createForm.email}
                error={createForm.errors.email}
                required
            />

            <Input
                type="password"
                label="Kata Sandi"
                placeholder="Minimal 8 karakter"
                bind:value={createForm.password}
                error={createForm.errors.password}
                required
            />

            <div class="space-y-1.5">
                <label for="create-user-role" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                    Pilih Role
                </label>
                <select
                    id="create-user-role"
                    bind:value={createForm.role}
                    class="w-full px-3.5 py-2 text-sm rounded-lg border bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/30"
                >
                    {#each roles as r}
                        <option value={r}>{r}</option>
                    {/each}
                </select>
            </div>
        </form>

        {#snippet footer()}
            <Button variant="outline" size="sm" onclick={() => isCreateModalOpen = false}>Batal</Button>
            <Button variant="primary" size="sm" onclick={submitCreate} disabled={createForm.processing}>Simpan Pengguna</Button>
        {/snippet}
    </Modal>

    <!-- Edit User Modal -->
    <Modal bind:open={isEditModalOpen} title="Edit Data Pengguna" description="Perbarui informasi pengguna dan role">
        <form onsubmit={submitEdit} class="space-y-4">
            <FileUpload
                label="Ubah Foto Profil (Opsional)"
                previewUrl={selectedUser?.avatar_url}
                bind:file={editForm.avatar}
                error={editForm.errors.avatar}
            />

            <Input
                label="Nama Lengkap"
                bind:value={editForm.name}
                error={editForm.errors.name}
                required
            />

            <Input
                type="email"
                label="Alamat Email"
                bind:value={editForm.email}
                error={editForm.errors.email}
                required
            />

            <Input
                type="password"
                label="Ubah Kata Sandi (Kosongkan jika tidak diubah)"
                placeholder="••••••••"
                bind:value={editForm.password}
                error={editForm.errors.password}
            />

            <div class="space-y-1.5">
                <label for="edit-user-role" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                    Role
                </label>
                <select
                    id="edit-user-role"
                    bind:value={editForm.role}
                    class="w-full px-3.5 py-2 text-sm rounded-lg border bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/30"
                >
                    {#each roles as r}
                        <option value={r}>{r}</option>
                    {/each}
                </select>
            </div>
        </form>

        {#snippet footer()}
            <Button variant="outline" size="sm" onclick={() => isEditModalOpen = false}>Batal</Button>
            <Button variant="primary" size="sm" onclick={submitEdit} disabled={editForm.processing}>Simpan Perubahan</Button>
        {/snippet}
    </Modal>

    <!-- Delete Confirmation Dialog -->
    <Dialog
        bind:open={isDeleteDialogOpen}
        title="Hapus Pengguna"
        message={`Apakah Anda yakin ingin menghapus pengguna "${selectedUser?.name}"? Tindakan ini tidak dapat dibatalkan.`}
        type="danger"
        confirmText="Hapus Permanen"
        onconfirm={executeDelete}
    />
</AppLayout>
