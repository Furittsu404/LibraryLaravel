<aside x-data="{
    collapsed: localStorage.getItem('sidebarCollapsed') === 'true',
    darkMode: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
    toggleSidebar() {
        this.collapsed = !this.collapsed;
        localStorage.setItem('sidebarCollapsed', this.collapsed);
        // Update main content margin
        this.updateMainContentMargin();
    },
    updateMainContentMargin() {
        const adminContent = document.getElementById('adminContent');
        if (adminContent) {
            if (this.collapsed) {
                adminContent.classList.remove('ml-64');
                adminContent.classList.add('ml-16');
            } else {
                adminContent.classList.remove('ml-16');
                adminContent.classList.add('ml-64');
            }
        }
    },
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
        document.documentElement.classList.toggle('dark', this.darkMode);

        // Trigger chart recreation if on dashboard
        if (typeof window.createCharts === 'function') {
            setTimeout(() => window.createCharts(), 100);
        }

        // Dispatch custom event for other components
        window.dispatchEvent(new Event('dark-mode-toggled'));
    }
}" x-init="document.documentElement.classList.toggle('dark', darkMode);
updateMainContentMargin();" :class="collapsed ? 'w-16' : 'w-64'"
    class="fixed bg-white dark:bg-gray-800 h-full shadow-md dark:shadow-gray-900 transition-all duration-300 ease-in-out overflow-hidden z-50">

    {{-- Header Section --}}
    <div class="flex bg-[#009639] dark:bg-[#007a2e] flex-col mb-2 p-4 transition-all duration-300"
        :class="'items-start'">
        <button @click="toggleSidebar"
            class="collapseSidebarBtn text-white p-1 mb-3 hover:bg-[#007a2e] dark:hover:bg-[#006225] rounded transition-colors"
            :class="collapsed ? '' : 'self-start'">
            <svg x-show="!collapsed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <svg x-show="collapsed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        <div class="flex flex-col items-center gap-3 mb-2 w-full relative">
            <img src="{{ asset('storage/images/LISO_LogoColored.png') }}" alt="Library Logo"
                :class="collapsed ? 'w-8 h-8' : 'w-24 h-24'" class="shrink-0 transition-all duration-300">
            <span
                class="text-lg font-bold whitespace-nowrap overflow-hidden transition-all duration-300 shrink-0 hidden"
                :class="collapsed ? 'text-[#009639] dark:text-[#00b347] scale-0 text-[0px] inline!' : 'text-white inline!'"
                x-transition>CLSU-LISO</span>
        </div>
    </div>

    {{-- Current Section Indicator --}}
    <div class="bg-green-700/50 border-y border-green-800/30 dark:border-green-600/30 mb-4">
        <div x-show="!collapsed" class="px-4 py-3">
            <div class="text-white/70 text-xs mb-1">Current Section:</div>
            <div class="text-white font-semibold text-sm mb-2 flex items-center gap-2">
                @php
                    $sectionCode = session('current_section', 'entrance');
                    $sectionIcons = [
                        'entrance' =>
                            '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>',
                        'periodicals' =>
                            '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>',
                        'humanities' =>
                            '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>',
                        'multimedia' =>
                            '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>',
                        'filipiniana' =>
                            '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>',
                        'makers' =>
                            '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>',
                        'science' =>
                            '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>',
                    ];
                @endphp
                {!! $sectionIcons[$sectionCode] ?? $sectionIcons['entrance'] !!}
                <span>{{ session('current_section_name', 'No Section') }}</span>
            </div>
            <a href="{{ route('select-section') }}"
                class="text-xs text-white/90 hover:text-white underline flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-3 h-3">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                </svg>
                Switch Section
            </a>
        </div>
        <div x-show="collapsed" class="text-center py-2 cursor-pointer"
            :title="'Current: ' + '{{ session('current_section_name', 'No Section') }}'"
            @click="window.location.href='{{ route('select-section') }}'">
            @php
                $collapsedSectionCode = session('current_section', 'entrance');
                $collapsedIcons = [
                    'entrance' =>
                        '<svg class="w-6 h-6 text-white mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>',
                    'periodicals' =>
                        '<svg class="w-6 h-6 text-white mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>',
                    'humanities' =>
                        '<svg class="w-6 h-6 text-white mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>',
                    'multimedia' =>
                        '<svg class="w-6 h-6 text-white mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>',
                    'filipiniana' =>
                        '<svg class="w-6 h-6 text-white mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>',
                    'makers' =>
                        '<svg class="w-6 h-6 text-white mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>',
                    'science' =>
                        '<svg class="w-6 h-6 text-white mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>',
                ];
            @endphp
            {!! $collapsedIcons[$collapsedSectionCode] ?? $collapsedIcons['entrance'] !!}
        </div>
    </div>

    {{-- Navigation Links --}}
    <nav class="flex flex-col py-2">
        <a href="{{ route('admin.dashboard.index') }}" wire:navigate :title="collapsed ? 'Dashboard' : ''"
            :class="'justify-start'"
            class="flex items-center text-gray-700 dark:text-gray-300 font-medium p-4 pl-5 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200 stroke-[1.5] {{ isset($activePage) && $activePage === 'dashboard' ? 'bg-gray-200 dark:bg-gray-700 font-bold stroke-2' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                class="w-6 h-6 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11l2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6" />
            </svg>
            <span class="ml-3 whitespace-nowrap overflow-hidden">Dashboard</span>
        </a>

        <a href="{{ route('admin.accounts.index') }}" wire:navigate :title="collapsed ? 'Accounts' : ''"
            :class="'justify-start'"
            class="flex items-center text-gray-700 dark:text-gray-300 font-medium p-4 pl-5 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200 stroke-[1.5] {{ isset($activePage) && $activePage === 'accounts' ? 'bg-gray-200 dark:bg-gray-700 font-bold stroke-2' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                class="w-6 h-6 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
            <span class="ml-3 whitespace-nowrap overflow-hidden">Accounts</span>
        </a>

        <a href="{{ route('admin.archive.index') }}" wire:navigate :title="collapsed ? 'Archive' : ''"
            :class="'justify-start'"
            class="flex items-center text-gray-700 dark:text-gray-300 font-medium p-4 pl-5 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200 stroke-[1.5] {{ isset($activePage) && $activePage === 'archive' ? 'bg-gray-200 dark:bg-gray-700 font-bold stroke-2' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                class="w-6 h-6 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
            </svg>
            <span class="ml-3 whitespace-nowrap overflow-hidden">Archive</span>
        </a>

        <a href="{{ route('admin.reserved-rooms.index') }}" wire:navigate :title="collapsed ? 'Reserved Rooms' : ''"
            :class="'justify-start'"
            class="flex items-center text-gray-700 dark:text-gray-300 font-medium p-4 pl-5 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200 stroke-[1.5] {{ isset($activePage) && $activePage === 'reserved-rooms' ? 'bg-gray-200 dark:bg-gray-700 font-bold stroke-2' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                class="w-6 h-6 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
            <span class="ml-3 whitespace-nowrap overflow-hidden">Reserved Rooms</span>
        </a>

        <a href="{{ route('admin.login-history.index') }}" wire:navigate :title="collapsed ? 'Login History' : ''"
            :class="'justify-start'"
            class="flex items-center text-gray-700 dark:text-gray-300 font-medium p-4 pl-5 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200 stroke-[1.5] {{ isset($activePage) && $activePage === 'login-history' ? 'bg-gray-200 dark:bg-gray-700 font-bold stroke-2' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                class="w-6 h-6 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span class="ml-3 whitespace-nowrap overflow-hidden">Login History</span>
        </a>

        <a href="{{ route('admin.reports.index') }}" wire:navigate :title="collapsed ? 'Report' : ''"
            :class="'justify-start'"
            class="flex items-center text-gray-700 dark:text-gray-300 font-medium p-4 pl-5 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200 stroke-[1.5] {{ isset($activePage) && $activePage === 'reports' ? 'bg-gray-200 dark:bg-gray-700 font-bold stroke-2' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                class="w-6 h-6 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            <span class="ml-3 whitespace-nowrap overflow-hidden">Report</span>
        </a>

        <a href="{{ route('admin.settings.index') }}" wire:navigate :title="collapsed ? 'Settings' : ''"
            :class="'justify-start'"
            class="flex items-center text-gray-700 dark:text-gray-300 font-medium p-4 pl-5 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200 stroke-[1.5] {{ isset($activePage) && $activePage === 'settings' ? 'bg-gray-200 dark:bg-gray-700 font-bold stroke-2' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                class="w-6 h-6 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
            <span class="ml-3 whitespace-nowrap overflow-hidden">Settings</span>
        </a>
    </nav>

    {{-- Dark Mode Toggle & Logout Section --}}
    <div class="absolute bottom-0 w-full border-t dark:border-gray-700">
        {{-- Dark Mode Toggle Button --}}
        <button @click="toggleDarkMode()" :title="collapsed ? 'Toggle Dark Mode' : ''" :class="'justify-start'"
            class="flex items-center w-full text-gray-700 dark:text-gray-300 font-medium p-4 pl-5 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200">
            <!-- Sun Icon (Light Mode) -->
            <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" class="w-6 h-6 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
            </svg>
            <!-- Moon Icon (Dark Mode) -->
            <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" class="w-6 h-6 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
            </svg>
            <span class="ml-3 whitespace-nowrap overflow-hidden">Dark Mode</span>
        </button>

        {{-- Logout Button --}}
        <form action="{{ route('logout') }}" method="POST" class="w-full">
            @csrf
            <button type="submit" :title="collapsed ? 'Logout' : ''" :class="'justify-start'"
                class="flex items-center w-full text-red-600 dark:text-red-400 font-medium p-4 pl-5 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    class="w-6 h-6 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                </svg>
                <span class="ml-3 whitespace-nowrap overflow-hidden">Logout</span>
            </button>
        </form>
    </div>
</aside>
