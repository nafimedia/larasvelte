<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
        <title inertia>{{ config('app.name', 'LaraSvelte') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&family=JetBrains Mono:wght@400;500;600&display=swap" rel="stylesheet">

        <!-- Favicon & Touch Icon -->
        @php
            $branding = \Illuminate\Support\Facades\Schema::hasTable('site_settings')
                ? \App\Http\Controllers\Admin\BrandingController::getCachedBranding()
                : [];
        @endphp
        @if (!empty($branding['public_favicon']))
            <link rel="icon" href="{{ $branding['public_favicon'] }}" />
        @endif
        @if (!empty($branding['public_apple_touch_icon']))
            <link rel="apple-touch-icon" href="{{ $branding['public_apple_touch_icon'] }}" />
        @endif

        <!-- Anti-FOUC Theme Initializer -->
        <script>
            (function() {
                try {
                    const theme = localStorage.getItem('theme') || 'system';
                    const isDark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                    if (isDark) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                } catch (e) {}
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.ts'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen">
        @inertia
    </body>
</html>
