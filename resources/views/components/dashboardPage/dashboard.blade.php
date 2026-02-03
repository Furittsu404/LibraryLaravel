<div class="flex flex-col" x-data="Object.assign({ filterDays: @entangle('filterDays').live }, dashboardStats())" x-init="$watch('filterDays', () => {
    setTimeout(() => {
        if (window.createCharts) window.createCharts();
    }, 150);
});
init()">
    <script>
        // Expose dashboardStats before Alpine initializes so x-data can call it
        window.dashboardStats = function() {
            return {
                totalUsers: {{ (int) $totalUsers }},
                archivedUsers: {{ (int) $archivedUsers }},
                totalLogins: {{ (int) $totalLogins }},
                activeUsers: {{ (int) $activeUsers }},
                topStudents: @json($topStudentsByLogins),
                topCourses: @json($topCoursesByLogins),
                recentLogins: @json($recentLogins),
                pollingMs: 5000,
                inFlight: false,

                init() {
                    this.fetchStats();
                    this._interval = setInterval(() => {
                        if (document.visibilityState === 'visible') this.fetchStats();
                    }, this.pollingMs);
                    // Listen for Livewire events in the same browser session and refresh immediately
                    if (window.Livewire && typeof Livewire.on === 'function') {
                        Livewire.on('statsUpdated', () => {
                            this.fetchStats();
                        });
                        Livewire.on('reservationsUpdated', () => {
                            this.fetchStats();
                        });
                    }
                },

                async fetchStats() {
                    if (this.inFlight) return;
                    this.inFlight = true;

                    const daysEl = document.getElementById('dashboardFilterDays');
                    const days = daysEl ? parseInt(daysEl.textContent) || 30 : 30;

                    try {
                        const url = new URL("{{ route('admin.stats.json') }}", window.location.origin);
                        url.searchParams.set('days', days);
                        const res = await fetch(url.toString(), {
                            credentials: 'same-origin'
                        });
                        if (!res.ok) throw new Error('Network response not ok');
                        const json = await res.json();
                        if (json && json.success) {
                            this.totalUsers = json.totalUsers;
                            this.archivedUsers = json.archivedUsers;
                            this.totalLogins = json.totalLogins;
                            this.activeUsers = json.activeUsers;
                            if (Array.isArray(json.topStudents)) this.topStudents = json.topStudents;
                            if (Array.isArray(json.topCourses)) this.topCourses = json.topCourses;
                            if (Array.isArray(json.recentLogins)) this.recentLogins = json.recentLogins;
                        }
                    } catch (err) {
                        console.error('Failed to fetch dashboard stats', err);
                    } finally {
                        this.inFlight = false;
                    }
                },

                format(n) {
                    if (n === null || n === undefined) return '0';
                    return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                }
            }
        };
    </script>
    <div class="flex justify-between items-center p-4">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Dashboard</h1>
    </div>

    {{-- Shared Time Filter Controls for Statistics & Top Courses --}}
    <div class="px-4 pb-4">
        <div class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md p-4">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Dashboard Time Filter:</span>
                <div class="flex flex-wrap gap-2">
                    <button wire:click="setFilterDays(1)"
                        class="px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-md transition-colors {{ $filterDays === 1 ? 'bg-[#009639] dark:bg-[#00b347] text-white border-[#009639] dark:border-[#00b347]' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        1 Day
                    </button>
                    <button wire:click="setFilterDays(7)"
                        class="px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-md transition-colors {{ $filterDays === 7 ? 'bg-[#009639] dark:bg-[#00b347] text-white border-[#009639] dark:border-[#00b347]' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        7 Days
                    </button>
                    <button wire:click="setFilterDays(30)"
                        class="px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-md transition-colors {{ $filterDays === 30 ? 'bg-[#009639] dark:bg-[#00b347] text-white border-[#009639] dark:border-[#00b347]' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        1 Month
                    </button>
                    <button wire:click="setFilterDays(90)"
                        class="px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-md transition-colors {{ $filterDays === 90 ? 'bg-[#009639] dark:bg-[#00b347] text-white border-[#009639] dark:border-[#00b347]' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        3 Months
                    </button>
                    <button wire:click="setFilterDays(180)"
                        class="px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-md transition-colors {{ $filterDays === 180 ? 'bg-[#009639] dark:bg-[#00b347] text-white border-[#009639] dark:border-[#00b347]' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        6 Months
                    </button>
                    <button wire:click="setFilterDays(365)"
                        class="px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-md transition-colors {{ $filterDays === 365 ? 'bg-[#009639] dark:bg-[#00b347] text-white border-[#009639] dark:border-[#00b347]' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        12 Months
                    </button>
                </div>
                <div class="flex items-center gap-2 ml-auto">
                    <span class="text-xs text-gray-600 dark:text-gray-400">Custom:</span>
                    <input type="number" wire:model="customDays" placeholder="Days" min="1"
                        class="w-20 px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">
                    <button wire:click="applyCustomDays"
                        class="px-3 py-1.5 text-xs bg-[#009639] dark:bg-[#00b347] text-white rounded-md hover:bg-[#007a2e] dark:hover:bg-[#009639] transition-colors">
                        Apply
                    </button>
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                All dashboard data showing last <span id="dashboardFilterDays"
                    class="font-semibold">{{ $filterDays }}</span>
                {{ $filterDays === 1 ? 'day' : 'days' }} (Cards, Lists & Charts)
            </p>
        </div>
    </div>

    {{-- Statistics Cards Row (Alpine polling for numeric tiles only) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 px-4 pb-4">
        {{-- Registered Users Card (Blue Icon) --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg shadow-green-200 dark:shadow-green-900/50 border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Registered Users</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-1">
                        <span x-text="format(totalUsers)">{{ number_format($totalUsers) }}</span>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">Last {{ $filterDays }}
                        {{ $filterDays === 1 ? 'day' : 'days' }}</p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900/30 rounded-full p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-8 h-8 text-blue-600 dark:text-blue-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Archived Users Card (Red Icon) --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg shadow-green-200 dark:shadow-green-900/50 border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Archived Users</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-1">
                        <span x-text="format(archivedUsers)">{{ number_format($archivedUsers) }}</span>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">Total archived accounts</p>
                </div>
                <div class="bg-red-100 dark:bg-red-900/30 rounded-full p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-8 h-8 text-red-600 dark:text-red-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Logins Card (Yellow Icon) --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg shadow-green-200 dark:shadow-green-900/50 border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Logins</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-1">
                        <span x-text="format(totalLogins)">{{ number_format($totalLogins) }}</span>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">Last {{ $filterDays }}
                        {{ $filterDays === 1 ? 'day' : 'days' }}</p>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900/30 rounded-full p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-8 h-8 text-yellow-600 dark:text-yellow-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7.864 4.243A7.5 7.5 0 0 1 19.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 0 0 4.5 10.5a7.464 7.464 0 0 1-1.15 3.993m1.989 3.559A11.209 11.209 0 0 0 8.25 10.5a3.75 3.75 0 1 1 7.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 0 1-3.6 9.75m6.633-4.596a18.666 18.666 0 0 1-2.485 5.33" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Currently Inside Card (Green Icon) --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg shadow-green-200 dark:shadow-green-900/50 border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Currently Inside</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-1">
                        <span x-text="format(activeUsers)">{{ number_format($activeUsers) }}</span>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">Active in section now</p>
                </div>
                <div class="bg-green-100 dark:bg-green-900/30 rounded-full p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-8 h-8 text-green-600 dark:text-green-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Dashboard Grid Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 px-4 pb-4 auto-rows-auto">

        {{-- Top 10 Students by Logins --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md p-4">
            <h2
                class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center border-b border-gray-300 dark:border-gray-700 pb-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2 text-[#009639] dark:text-[#00b347]">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                </svg>
                Top 10 Students by Logins
            </h2>
            <div class="space-y-2 max-h-80 overflow-y-auto custom-scrollbar">
                <template x-for="(student, index) in topStudents" :key="student.id">
                    <div
                        class="flex items-center justify-between p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <div class="flex items-center gap-2 flex-1 min-w-0">
                            <span
                                class="flex items-center justify-center w-6 h-6 shrink-0 bg-[#009639] dark:bg-[#00b347] text-white rounded-full font-semibold text-xs"
                                x-text="index + 1"></span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate"
                                    x-text="(student.fname || '') + ' ' + (student.lname || '')"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate"
                                    x-text="student.course || 'N/A'"></p>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100 ml-2"
                            x-text="format(student.login_count)"></span>
                    </div>
                </template>
                <template x-if="!topStudents || topStudents.length === 0">
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No data available</p>
                </template>
            </div>
        </div>

        {{-- Top Courses by Logins --}}
        {{-- Top Courses by Logins --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md p-4">
            <h2
                class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center border-b border-gray-300 dark:border-gray-700 pb-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2 text-[#009639] dark:text-[#00b347]">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                </svg>
                Top Courses by Logins
            </h2>
            <div class="space-y-2 max-h-80 overflow-y-auto custom-scrollbar">
                <template x-for="(course, index) in topCourses" :key="index">
                    <div
                        class="flex items-center justify-between p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <div class="flex items-center gap-2">
                            <span
                                class="flex items-center justify-center w-6 h-6 bg-[#009639] dark:bg-[#00b347] text-white rounded-full font-semibold text-xs"
                                x-text="index + 1"></span>
                            <span class="text-sm text-gray-700 dark:text-gray-300"
                                x-text="course.course || 'N/A'"></span>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100"
                            x-text="format(course.login_count)"></span>
                    </div>
                </template>
                <template x-if="!topCourses || topCourses.length === 0">
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No data available</p>
                </template>
            </div>
        </div>

        {{-- Recent Logins --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md p-4">
            <h2
                class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center border-b border-gray-300 dark:border-gray-700 pb-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2 text-[#009639] dark:text-[#00b347]">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                </svg>
                Recent Logins
            </h2>
            <div class="space-y-2 max-h-80 overflow-y-auto custom-scrollbar">
                <template x-for="login in recentLogins" :key="login.id">
                    <div
                        class="flex gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold"
                                :class="login.sex === 'female' ?
                                    'bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300' :
                                    'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'"
                                x-text="( (login.fname||'').charAt(0) || '' ).toUpperCase() + ((login.lname||'').charAt(0) || '' ).toUpperCase()">
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate"
                                x-text="(login.lname || '') + ', ' + (login.fname || '') + (login.mname ? ' ' + login.mname : '')">
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400" x-text="login.course"></p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                    :class="{
                                        'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300': login
                                            .user_type === 'student',
                                        'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300': login
                                            .user_type === 'faculty',
                                        'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300': login
                                            .user_type === 'visitor'
                                    }"
                                    x-text="(login.user_type || '').charAt(0).toUpperCase() + (login.user_type || '').slice(1)"></span>
                                <span class="text-xs text-gray-500 dark:text-gray-400"
                                    x-text="login.login_time_human"></span>
                            </div>
                        </div>
                    </div>
                </template>
                <template x-if="!recentLogins || recentLogins.length === 0">
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No recent logins</p>
                </template>
            </div>
        </div>

        {{-- Reserved Rooms --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md p-4 row-span-2">
            <h2
                class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center border-b border-gray-300 dark:border-gray-700 pb-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2 text-[#009639] dark:text-[#00b347]">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
                Reserved Rooms
            </h2>
            <div class="space-y-3 max-h-80 overflow-y-auto custom-scrollbar">
                @forelse($reservedRooms as $room)
                    <div class="p-3 bg-red-50 rounded-lg border-l-4 border-red-600">
                        <h3 class="font-semibold text-gray-800">{{ $room->name }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $room->description }}</p>
                        <div class="flex justify-between items-center mt-2 text-xs text-gray-500">
                            <span>Start: {{ $room->start_datetime }}</span>
                            <span>End: {{ $room->end_datetime }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto text-gray-300 mb-2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                        </svg>
                        <p class="text-gray-500 text-sm">No rooms reserved</p>
                        <p class="text-gray-400 text-xs mt-1">(Feature coming soon)</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Login Timeline Chart (spans 3 columns) --}}
        <div
            class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md p-4 lg:col-span-3">
            <div class="flex flex-col gap-2 mb-3 border-b border-gray-300 dark:border-gray-700 pb-3">
                <div class="flex justify-between items-center">
                    <h2 class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor"
                            class="w-5 h-5 mr-2 text-[#009639] dark:text-[#00b347]">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                        </svg>
                        Login Timeline
                    </h2>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Showing data for the last <span class="font-semibold">{{ $filterDays }}</span>
                    {{ $filterDays === 1 ? 'day' : 'days' }}
                </p>
            </div>
            <div class="relative h-56">
                <canvas id="timelineChart"></canvas>
            </div>
        </div>

        {{-- Pie Charts Section --}}
        {{-- Course Distribution Pie Chart --}}
        <div
            class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md p-4 lg:col-span-2 lg:row-span-2">
            <h2
                class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center border-b border-gray-300 dark:border-gray-700 pb-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2 text-[#009639] dark:text-[#00b347]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                </svg>
                Registered Users by Course
            </h2>
            <div class="relative" style="height: 300px;">
                <canvas id="courseChart"></canvas>
            </div>
        </div>

        {{-- Gender Distribution Pie Chart --}}
        <div
            class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md p-4 lg:col-span-2 lg:row-span-2">
            <h2
                class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center border-b border-gray-300 dark:border-gray-700 pb-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2 text-[#009639] dark:text-[#00b347]">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
                Sex Distribution
            </h2>
            <div class="relative" style="height: 300px;">
                <canvas id="sexChart"></canvas>
            </div>
        </div>

    </div>

    {{-- Initialize dashboard with data --}}
    <script>
        // Function to initialize dashboard charts
        const initializeDashboardCharts = () => {
            if (typeof window.initializeDashboard === 'function') {
                window.initializeDashboard(
                    @json($timelineData),
                    @json($coursesByUsers),
                    @json($sexDistribution)
                );
            } else {
                // Retry if not loaded yet
                setTimeout(initializeDashboardCharts, 100);
            }
        };

        // Initialize on DOMContentLoaded (first page load)
        document.addEventListener('DOMContentLoaded', initializeDashboardCharts);

        // Reinitialize when Livewire navigates to this component
        document.addEventListener('livewire:navigated', initializeDashboardCharts);

        // Also listen for Livewire load event (for wire:navigate)
        window.addEventListener('livewire:load', initializeDashboardCharts);
    </script>
</div>
