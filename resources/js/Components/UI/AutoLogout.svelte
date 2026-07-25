<script lang="ts">
    import { onMount, onDestroy } from 'svelte';
    import { router, page } from '@inertiajs/svelte';
    import { toast } from 'svelte-sonner';
    import Modal from './Modal.svelte';
    import Button from './Button.svelte';
    import { ShieldAlert, Clock } from 'lucide-svelte';

    interface Props {
        timeoutMinutes?: number; // Inactivity limit in minutes (default 30)
        warningMinutes?: number; // Show warning modal X minutes before logout (default 2)
    }

    let { timeoutMinutes = 30, warningMinutes = 2 }: Props = $props();

    const TIMEOUT_MS = $derived(timeoutMinutes * 60 * 1000);
    const WARNING_MS = $derived((timeoutMinutes - warningMinutes) * 60 * 1000);
    const STORAGE_KEY = 'larasvelte_last_activity';

    let showWarningModal = $state(false);
    let secondsRemaining = $state(120);

    let checkInterval: ReturnType<typeof setInterval> | null = null;
    let countdownInterval: ReturnType<typeof setInterval> | null = null;
    let lastActivityTime = Date.now();

    function updateActivity() {
        const now = Date.now();
        // Throttle activity updates to local storage (at most once every 2 seconds)
        if (now - lastActivityTime > 2000) {
            lastActivityTime = now;
            try {
                localStorage.setItem(STORAGE_KEY, now.toString());
            } catch (e) {
                // Ignore storage errors
            }

            if (showWarningModal) {
                showWarningModal = false;
                if (countdownInterval) clearInterval(countdownInterval);
            }
        }
    }

    function checkInactivity() {
        // Only check if user is authenticated
        const user = page.props.auth?.user;
        if (!user) return;

        const stored = localStorage.getItem(STORAGE_KEY);
        const lastActive = stored ? parseInt(stored, 10) : lastActivityTime;
        const elapsed = Date.now() - lastActive;

        if (elapsed >= TIMEOUT_MS) {
            performLogout();
        } else if (elapsed >= WARNING_MS) {
            if (!showWarningModal) {
                showWarningModal = true;
                const remaining = Math.max(0, Math.floor((TIMEOUT_MS - elapsed) / 1000));
                secondsRemaining = remaining;
                startCountdown();
            }
        } else {
            if (showWarningModal) {
                showWarningModal = false;
                if (countdownInterval) clearInterval(countdownInterval);
            }
        }
    }

    function startCountdown() {
        if (countdownInterval) clearInterval(countdownInterval);
        countdownInterval = setInterval(() => {
            if (secondsRemaining > 1) {
                secondsRemaining--;
            } else {
                if (countdownInterval) clearInterval(countdownInterval);
                performLogout();
            }
        }, 1000);
    }

    function resetTimerFromModal() {
        const now = Date.now();
        lastActivityTime = now;
        try {
            localStorage.setItem(STORAGE_KEY, now.toString());
        } catch (e) {
            // Ignore
        }
        showWarningModal = false;
        if (countdownInterval) clearInterval(countdownInterval);
        toast.info('Sesi Anda berhasil diperpanjang.');
    }

    function performLogout() {
        if (checkInterval) clearInterval(checkInterval);
        if (countdownInterval) clearInterval(countdownInterval);
        showWarningModal = false;

        toast.error('Anda telah di-logout otomatis karena tidak ada aktivitas selama 30 menit.');
        router.post('/logout');
    }

    function handleStorageChange(event: StorageEvent) {
        if (event.key === STORAGE_KEY && event.newValue) {
            const newTime = parseInt(event.newValue, 10);
            if (!isNaN(newTime)) {
                lastActivityTime = newTime;
                if (showWarningModal && Date.now() - newTime < WARNING_MS) {
                    showWarningModal = false;
                    if (countdownInterval) clearInterval(countdownInterval);
                }
            }
        }
    }

    onMount(() => {
        secondsRemaining = warningMinutes * 60;
        const now = Date.now();
        lastActivityTime = now;
        try {
            localStorage.setItem(STORAGE_KEY, now.toString());
        } catch (e) {
            // Ignore
        }

        const events = ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'];
        events.forEach((evt) => window.addEventListener(evt, updateActivity, { passive: true }));
        window.addEventListener('storage', handleStorageChange);

        checkInterval = setInterval(checkInactivity, 5000); // Check every 5s
    });

    onDestroy(() => {
        const events = ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'];
        if (typeof window !== 'undefined') {
            events.forEach((evt) => window.removeEventListener(evt, updateActivity));
            window.removeEventListener('storage', handleStorageChange);
        }
        if (checkInterval) clearInterval(checkInterval);
        if (countdownInterval) clearInterval(countdownInterval);
    });
</script>

<Modal bind:open={showWarningModal} title="Peringatan Inaktivitas Sesi" maxWidth="md" onclose={resetTimerFromModal}>
    <div class="space-y-4">
        <div class="flex items-center gap-3 p-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-900">
            <ShieldAlert class="w-6 h-6 text-amber-600 dark:text-amber-400 shrink-0" />
            <p class="text-xs sm:text-sm">
                Anda tidak melakukan aktivitas selama beberapa waktu. Sesi login Anda akan di-logout otomatis demi keamanan.
            </p>
        </div>

        <div class="text-center py-4 bg-slate-50 dark:bg-slate-850 rounded-2xl border border-slate-200 dark:border-slate-800">
            <div class="inline-flex items-center justify-center gap-2 mb-1 text-slate-500 dark:text-slate-400 text-xs font-medium">
                <Clock class="w-4 h-4" />
                <span>Waktu Tersisa Before Logout</span>
            </div>
            <div class="text-4xl font-extrabold font-mono text-amber-600 dark:text-amber-400 tracking-wider">
                {Math.floor(secondsRemaining / 60)}:{String(secondsRemaining % 60).padStart(2, '0')}
            </div>
        </div>
    </div>

    {#snippet footer()}
        <Button variant="primary" onclick={resetTimerFromModal}>
            Saya Masih Aktif (Perpanjang Sesi)
        </Button>
    {/snippet}
</Modal>
