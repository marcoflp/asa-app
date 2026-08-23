<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)" />
<meta name="theme-color" content="#09090b" media="(prefers-color-scheme: dark)" />
<meta name="mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="default" />
<meta name="apple-mobile-web-app-title" content="ASA" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="manifest" href="/manifest.json" />
<link rel="icon" href="/logo.png" type="image/png">
<link rel="apple-touch-icon" href="/logo.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

<script>
    (function () {
        function syncTheme() {
            var theme = localStorage.getItem('flux.appearance') || 'system';
            var isDark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            
            if (isDark) {
                document.documentElement.classList.add('dark');
                document.cookie = "flux_appearance=dark; path=/; max-age=31536000; SameSite=Lax";
            } else {
                document.documentElement.classList.remove('dark');
                document.cookie = "flux_appearance=light; path=/; max-age=31536000; SameSite=Lax";
            }
        }

        syncTheme();
        document.addEventListener('livewire:navigating', syncTheme);
        document.addEventListener('livewire:navigated', syncTheme);
    })();
</script>

@fluxAppearance
@vite(['resources/css/app.css', 'resources/js/app.js'])

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .catch(err => console.warn('SW error:', err));
        });
    }
</script>
