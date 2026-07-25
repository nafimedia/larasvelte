<script lang="ts">
    import { useForm, Link } from '@inertiajs/svelte';
    import GuestLayout from '@/Layouts/GuestLayout.svelte';
    import Input from '@/Components/UI/Input.svelte';
    import Button from '@/Components/UI/Button.svelte';
    import { LogIn, KeyRound } from 'lucide-svelte';

    const form = useForm({
        email: 'admin@example.com',
        password: 'password',
        remember: true,
    });

    function submit(e: Event) {
        e.preventDefault();
        form.post('/login', {
            onFinish: () => form.reset('password'),
        });
    }
</script>

<GuestLayout title="Masuk Akun">
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">Selamat Datang Kembali</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Masukan email & kata sandi untuk mengakses dashboard</p>
    </div>

    <form onsubmit={submit} class="space-y-4">
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
            placeholder="••••••••"
            bind:value={form.password}
            error={form.errors.password}
            required
        />

        <div class="flex items-center justify-between text-xs">
            <label class="flex items-center gap-2 text-slate-600 dark:text-slate-400 cursor-pointer">
                <input
                    type="checkbox"
                    bind:checked={form.remember}
                    class="rounded border-slate-300 dark:border-slate-800 text-indigo-600 focus:ring-indigo-500"
                />
                <span>Ingat saya</span>
            </label>
        </div>

        <Button
            type="submit"
            variant="primary"
            class="w-full"
            disabled={form.processing}
        >
            <LogIn class="w-4 h-4" />
            <span>{form.processing ? 'Memproses...' : 'Masuk ke Dashboard'}</span>
        </Button>
    </form>

    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500 dark:text-slate-400">
        Belum memiliki akun?
        <Link href="/register" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
            Daftar di sini
        </Link>
    </div>

    <!-- Quick Demo Credentials Box -->
    <div class="mt-6 p-3 bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200/60 dark:border-indigo-800/60 rounded-xl text-xs space-y-1">
        <p class="font-bold text-indigo-900 dark:text-indigo-300 flex items-center gap-1.5">
            <KeyRound class="w-3.5 h-3.5" /> Akun Demo Bawaan:
        </p>
        <div class="font-mono text-[11px] text-indigo-700 dark:text-indigo-400">
            <p>Admin: <span class="font-bold">admin@example.com</span> / <span class="font-bold">password</span></p>
            <p>User: <span class="font-bold">user@example.com</span> / <span class="font-bold">password</span></p>
        </div>
    </div>
</GuestLayout>
