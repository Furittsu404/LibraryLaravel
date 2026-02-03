<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Library' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Dark Mode Script - Must run before page loads to prevent flash --}}
    <script>
        function applyTheme() {
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                    '(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            }
        }

        applyTheme();
        document.addEventListener('livewire:navigated', applyTheme);
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
