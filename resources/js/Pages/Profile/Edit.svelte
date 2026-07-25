<script lang="ts">
    import { useForm } from '@inertiajs/svelte';
    import AppLayout from '@/Layouts/AppLayout.svelte';
    import Card from '@/Components/UI/Card.svelte';
    import Input from '@/Components/UI/Input.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import FileUpload from '@/Components/UI/FileUpload.svelte';
    import { Save, KeyRound, User as UserIcon } from 'lucide-svelte';

    interface Props {
        user: {
            id: number;
            name: string;
            email: string;
            avatar_url: string;
            created_at: string;
        };
    }

    let { user }: Props = $props();

    // svelte-ignore state_referenced_locally
    const profileForm = useForm({
        name: user.name,
        email: user.email,
        avatar: null as File | null,
    });

    const passwordForm = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    function submitProfile(e: Event) {
        e.preventDefault();
        profileForm.post('/profile', {
            preserveScroll: true,
        });
    }

    function submitPassword(e: Event) {
        e.preventDefault();
        passwordForm.put('/profile/password', {
            preserveScroll: true,
            onSuccess: () => passwordForm.reset(),
        });
    }
</script>

<AppLayout title="Profil Saya">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Profil Saya</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola informasi pribadi, foto avatar, dan keamanan kata sandi</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-5xl">
        <!-- Profile Detail & Avatar Form -->
        <Card title="Informasi Profil & Avatar" description="Perbarui nama, email, dan foto profil Anda">
            <form onsubmit={submitProfile} class="space-y-4">
                <FileUpload
                    label="Foto Profil (Avatar)"
                    previewUrl={user.avatar_url}
                    bind:file={profileForm.avatar}
                    error={profileForm.errors.avatar}
                />

                <Input
                    label="Nama Lengkap"
                    bind:value={profileForm.name}
                    error={profileForm.errors.name}
                    required
                />

                <Input
                    type="email"
                    label="Alamat Email"
                    bind:value={profileForm.email}
                    error={profileForm.errors.email}
                    required
                />

                <div class="flex justify-end pt-2">
                    <Button type="submit" variant="primary" size="sm" disabled={profileForm.processing}>
                        <Save class="w-4 h-4" />
                        <span>{profileForm.processing ? 'Menyimpan...' : 'Simpan Profil'}</span>
                    </Button>
                </div>
            </form>
        </Card>

        <!-- Password Change Form -->
        <Card title="Ubah Kata Sandi" description="Pastikan akun Anda menggunakan kata sandi yang aman">
            <form onsubmit={submitPassword} class="space-y-4">
                <Input
                    type="password"
                    label="Kata Sandi Saat Ini"
                    placeholder="••••••••"
                    bind:value={passwordForm.current_password}
                    error={passwordForm.errors.current_password}
                    required
                />

                <Input
                    type="password"
                    label="Kata Sandi Baru"
                    placeholder="Minimal 8 karakter"
                    bind:value={passwordForm.password}
                    error={passwordForm.errors.password}
                    required
                />

                <Input
                    type="password"
                    label="Konfirmasi Kata Sandi Baru"
                    placeholder="Ulangi kata sandi baru"
                    bind:value={passwordForm.password_confirmation}
                    error={passwordForm.errors.password_confirmation}
                    required
                />

                <div class="flex justify-end pt-2">
                    <Button type="submit" variant="primary" size="sm" disabled={passwordForm.processing}>
                        <KeyRound class="w-4 h-4" />
                        <span>{passwordForm.processing ? 'Memproses...' : 'Ubah Kata Sandi'}</span>
                    </Button>
                </div>
            </form>
        </Card>
    </div>
</AppLayout>
