<script lang="ts">
    import { useForm, Link } from '@inertiajs/svelte';
    import GuestLayout from '@/Layouts/GuestLayout.svelte';
    import Input from '@/Components/UI/Input.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import { UserPlus } from 'lucide-svelte';

    const form = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    function submit(e: Event) {
        e.preventDefault();
        form.post('/register', {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    }
</script>

<GuestLayout title="Daftar Akun Baru">
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">Buat Akun Pengguna</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Lengkapi data di bawah untuk mendaftar akun baru</p>
    </div>

    <form onsubmit={submit} class="space-y-4">
        <Input
            id="name"
            label="Nama Lengkap"
            placeholder="John Doe"
            bind:value={form.name}
            error={form.errors.name}
            required
        />

        <Input
            id="email"
            type="email"
            label="Alamat Email"
            placeholder="nama@perusahaan.com"
            bind:value={form.email}
            error={form.errors.email}
            required
        />

        <Input
            id="password"
            type="password"
            label="Kata Sandi"
            placeholder="Minimal 8 karakter"
            bind:value={form.password}
            error={form.errors.password}
            required
        />

        <Input
            id="password_confirmation"
            type="password"
            label="Konfirmasi Kata Sandi"
            placeholder="Ulangi kata sandi"
            bind:value={form.password_confirmation}
            error={form.errors.password_confirmation}
            required
        />

        <Button
            type="submit"
            variant="primary"
            class="w-full"
            disabled={form.processing}
        >
            <UserPlus class="w-4 h-4" />
            <span>{form.processing ? 'Mendaftarkan...' : 'Daftar Sekarang'}</span>
        </Button>
    </form>

    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500 dark:text-slate-400">
        Sudah memiliki akun?
        <Link href="/login" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
            Masuk ke sini
        </Link>
    </div>
</GuestLayout>
