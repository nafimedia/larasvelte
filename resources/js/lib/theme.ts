export type Theme = 'light' | 'dark' | 'system';

export function getTheme(): Theme {
    if (typeof localStorage !== 'undefined') {
        const saved = localStorage.getItem('theme') as Theme;
        if (saved && ['light', 'dark', 'system'].includes(saved)) {
            return saved;
        }
    }
    return 'system';
}

export function isDarkMode(): boolean {
    const theme = getTheme();
    if (theme === 'dark') return true;
    if (theme === 'light') return false;
    return typeof window !== 'undefined' && window.matchMedia('(prefers-color-scheme: dark)').matches;
}

export function applyTheme(theme?: Theme) {
    const currentTheme = theme || getTheme();
    const isDark =
        currentTheme === 'dark' ||
        (currentTheme === 'system' &&
            typeof window !== 'undefined' &&
            window.matchMedia('(prefers-color-scheme: dark)').matches);

    if (typeof document !== 'undefined') {
        if (isDark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
}

export function setTheme(theme: Theme) {
    if (typeof localStorage !== 'undefined') {
        localStorage.setItem('theme', theme);
    }
    if (typeof document !== 'undefined') {
        document.cookie = `theme=${theme};path=/;max-age=31536000`;
    }
    applyTheme(theme);
}
