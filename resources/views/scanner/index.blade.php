<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CLSU Library and Information Services Office - Attendance System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }

        .scan-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .5;
            }
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen" x-data="scannerApp()">

    <!-- Settings Button (Minimal, Top Right Corner) -->
    <button @click="showPasswordModal = true"
        class="fixed top-6 right-6 w-10 h-10 bg-white hover:bg-gray-50 rounded-lg shadow-md hover:shadow-lg opacity-40 hover:opacity-100 transition-all duration-200 z-40 border border-gray-200">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="w-5 h-5 mx-auto text-gray-600">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
    </button>

    <!-- Password Verification Modal -->
    <div x-show="showPasswordModal" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="closePasswordModal()"
        class="fixed inset-0 backdrop-blur-lg bg-black/30 z-50 flex items-center justify-center p-4">

        <div x-show="showPasswordModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95" @click.stop
            class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full border-t-4 border-green-600">

            <!-- Icon -->
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-8 h-8 text-green-600">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <div class="text-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Admin Verification</h3>
                <p class="text-gray-600">Enter admin password to access section settings</p>
            </div>

            <!-- Error Message -->
            <div x-show="passwordError" x-cloak class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-600 text-center" x-text="passwordError"></p>
            </div>

            <!-- Password Input -->
            <form @submit.prevent="verifyPassword()">
                <div class="mb-6">
                    <label for="admin-password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Admin Password
                    </label>
                    <input type="password" id="admin-password" x-ref="passwordInput" x-model="adminPassword" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                        placeholder="Enter password"
                        :disabled="isVerifying">
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button type="button" @click="closePasswordModal()"
                        class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors duration-200"
                        :disabled="isVerifying">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        :disabled="isVerifying">
                        <svg x-show="isVerifying" class="animate-spin h-5 w-5 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="isVerifying ? 'Verifying...' : 'Verify'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 shadow-sm">
            <div class="container mx-auto px-6 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-6">
                        <img src="{{ asset('storage/images/CLSU-logo.png') }}" alt="CLSU Logo" class="h-16 w-16">
                        <div class="border-l border-gray-300 pl-6">
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">CLSU - Library and Information
                                Services Office
                            </h1>
                        </div>
                    </div>
                    <img src="{{ asset('storage/images/LISO_LogoColored.png') }}" alt="LISO Logo" class="h-16 w-16">
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 container mx-auto px-6 py-8 max-w-7xl">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Column: Scanner -->
                <div class="lg:col-span-2">
                    <!-- Section Info Bar -->
                    <div class="bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg p-4 mb-6 shadow-md">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium opacity-90">Current Section</p>
                                <p class="text-xl font-bold" x-text="sectionName"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium opacity-90">Date & Time</p>
                                <p class="text-base font-semibold" x-text="currentDate"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Scanner Card -->
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-8">
                        <div class="text-center mb-8">
                            <div
                                class="inline-flex items-center justify-center w-20 h-20 bg-green-50 rounded-full mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-green-600">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">Please Scan Your School ID</h2>
                        </div>

                        <form @submit.prevent="scanBarcode" class="max-w-md mx-auto">
                            <div class="relative">
                                <input type="text" x-model="barcode" x-ref="barcodeInput" :disabled="scanningLocked"
                                    class="w-full text-xl font-mono bg-gray-50 border-2 border-gray-300 focus:border-green-500 focus:bg-white rounded-lg px-6 py-4 focus:outline-none focus:ring-4 focus:ring-green-100 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                    autocomplete="off" autofocus>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 scan-pulse">
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                </div>
                            </div>
                        </form>

                        <!-- Status Message -->
                        <div x-show="message" x-transition class="mt-6 rounded-lg border-l-4 p-4"
                            :class="{
                                'bg-green-50 border-green-500 text-green-900': messageType === 'login',
                                'bg-blue-50 border-blue-500 text-blue-900': messageType === 'logout',
                                'bg-red-50 border-red-500 text-red-900': messageType === 'error'
                            }">
                            <p class="font-semibold" x-text="message"></p>
                            <div x-show="currentUser" class="mt-3 pt-3 border-t border-current border-opacity-20">
                                <p class="text-sm"><span class="font-medium">Name:</span> <span
                                        x-text="currentUser.name"></span></p>
                                <p class="text-sm mt-1"><span class="font-medium">Course:</span> <span
                                        x-text="currentUser.course"></span> • <span
                                        x-text="currentUser.user_type"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Registration Card (Entrance Section Only) -->
                    @if ($currentSection === 'entrance')
                        <div
                            class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-start gap-4">
                                <div class="shrink-0">
                                    <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-green-600">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">New User?</h3>
                                    <p class="text-gray-600 text-sm mb-4">Register your account to access the library
                                        and
                                        all its services.</p>
                                    <a href="{{ route('student.registration') }}"
                                        class="inline-flex items-center gap-2 bg-green-600 text-white font-medium px-5 py-2.5 rounded-lg hover:bg-green-700 transition-colors">
                                        <span>Register Now</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Room Reservation Card -->
                    <div
                        class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-green-600">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Need to Reserve a Room?</h3>
                                <p class="text-gray-600 text-sm mb-4">Book discussion rooms, study areas, and
                                    conference
                                    spaces easily online.</p>
                                <a href="{{ route('student.reservations') }}"
                                    class="inline-flex items-center gap-2 bg-green-600 text-white font-medium px-5 py-2.5 rounded-lg hover:bg-green-700 transition-colors">
                                    <span>Reserve Now</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Recent Activity -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 sticky top-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-900">Today's Activity</h3>
                            <div class="flex items-center gap-2">
                                <span class="relative flex h-2 w-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                </span>
                                <span class="text-sm font-semibold text-green-600"
                                    x-text="activeUsers + ' Inside'"></span>
                            </div>
                        </div>

                        <div class="space-y-3 max-h-[calc(100vh-280px)] overflow-y-auto pr-2">
                            <template x-for="(login, index) in recentLogins" :key="index">
                                <div
                                    class="flex items-start gap-3 p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors fade-in border border-gray-200">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-semibold text-sm"
                                        x-text="login.name.split(' ').map(n => n[0]).join('').substring(0, 2)">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-900 text-sm truncate" x-text="login.name">
                                        </p>
                                        <p class="text-xs text-gray-600 truncate" x-text="login.course"></p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-xs text-gray-500" x-text="login.login_time"></span>
                                            <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                                                :class="login.status === 'logged_in' ? 'bg-green-100 text-green-700' :
                                                    'bg-gray-200 text-gray-600'"
                                                x-text="login.status === 'logged_in' ? 'Active' : 'Exited'">
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div x-show="recentLogins.length === 0" class="text-center py-12 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1" stroke="currentColor" class="w-12 h-12 mx-auto mb-3 opacity-50">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                </svg>
                                <p class="text-sm">No activity yet</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-4">
            <div class="container mx-auto px-6 text-center text-sm text-gray-600">
                <p>&copy; 2026 Central Luzon State University. All rights reserved.</p>
            </div>
        </footer>
    </div>

    <!-- Archive Modal -->
    <div x-show="messageType === 'archived'" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 backdrop-blur-lg z-50 flex items-center justify-center p-4">

        <div x-show="messageType === 'archived'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full border-t-4 border-orange-500">

            <!-- Icon -->
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-8 h-8 text-orange-600">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                </div>
            </div>

            <!-- Message -->
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2" x-text="message"></h3>
                <p class="text-gray-600 text-lg mb-6" x-text="description"></p>

                <!-- Visual Countdown -->
                <div class="mb-4">
                    <div class="flex items-center justify-center gap-2 text-sm text-gray-500 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Closing in <span class="font-bold text-lg" x-text="archiveCountdown"></span>
                            seconds</span>
                    </div>
                    <div class="w-full bg-orange-200 rounded-full h-2">
                        <div class="bg-orange-600 h-2 rounded-full transition-all duration-1000"
                            :style="`width: ${(archiveCountdown / 4) * 100}%`"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Selector Sidebar -->
    <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="sidebarOpen = false" class="fixed inset-0 backdrop-blur-lg z-40">
    </div>

    <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed right-0 top-0 h-full w-96 bg-white shadow-2xl z-50 overflow-y-auto border-l border-gray-200">

        <div class="p-6">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Section Settings</h3>
                    <p class="text-sm text-gray-600 mt-1">Select active library section</p>
                </div>
                <button @click="sidebarOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-2">
                @foreach ([['code' => 'entrance', 'name' => 'Entrance'], ['code' => 'exit', 'name' => 'Exit'], ['code' => 'periodicals', 'name' => 'Periodicals'], ['code' => 'humanities', 'name' => 'Humanities'], ['code' => 'multimedia', 'name' => 'Multimedia'], ['code' => 'filipiniana', 'name' => 'Filipiniana'], ['code' => 'makers', 'name' => 'Maker Space'], ['code' => 'science', 'name' => 'Science & Technology']] as $section)
                    <button @click="changeSection('{{ $section['code'] }}', '{{ $section['name'] }}')"
                        class="w-full text-left p-4 rounded-lg border transition-all group"
                        :class="currentSection === '{{ $section['code'] }}' ?
                            'border-green-500 bg-green-50 shadow-sm' :
                            'border-gray-200 hover:border-green-300 hover:bg-gray-50'">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-gray-900">{{ $section['name'] }}</p>
                            <svg x-show="currentSection === '{{ $section['code'] }}'"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-5 h-5 text-green-600">
                                <path fill-rule="evenodd"
                                    d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        function scannerApp() {
            return {
                barcode: '',
                message: '',
                messageType: '',
                description: '',
                currentUser: null,
                recentLogins: @json($recentLogins),
                activeUsers: {{ $activeUsers }},
                currentSection: '{{ $currentSection }}',
                sectionName: '{{ $sectionName }}',
                sidebarOpen: false,
                currentDate: '',
                archiveCountdown: 4,
                archiveCountdownInterval: null,
                showPasswordModal: false,
                adminPassword: '',
                passwordError: '',
                isVerifying: false,
                // cooldown to prevent accidental double scans
                lastScanAt: 0,
                scanCooldownMs: 400, // milliseconds
                scanningLocked: false,

                init() {
                    this.updateDateTime();
                    setInterval(() => this.updateDateTime(), 1000);
                    setInterval(() => this.refreshLogins(), 5000); // Refresh every 5 seconds

                    // Keep focus on barcode input
                    this.$watch('message', () => {
                        if (this.message) {
                            const isArchived = this.messageType === 'archived';
                            const timeout = isArchived ? 4000 : 3000;

                            if (isArchived) {
                                // Start countdown for archived modal
                                this.startArchiveCountdown();
                            } else {
                                setTimeout(() => {
                                    this.message = '';
                                    this.description = '';
                                    this.messageType = '';
                                    this.currentUser = null;
                                    this.$refs.barcodeInput.focus();
                                }, timeout);
                            }
                        }
                    });

                    // Auto-focus password input when modal opens
                    this.$watch('showPasswordModal', (value) => {
                        if (value) {
                            this.$nextTick(() => {
                                this.$refs.passwordInput?.focus();
                            });
                        }
                    });
                },

                startArchiveCountdown() {
                    this.archiveCountdown = 4;
                    if (this.archiveCountdownInterval) {
                        clearInterval(this.archiveCountdownInterval);
                    }
                    this.archiveCountdownInterval = setInterval(() => {
                        this.archiveCountdown--;
                        if (this.archiveCountdown <= 0) {
                            clearInterval(this.archiveCountdownInterval);
                            // Add 2-second grace period before closing
                            setTimeout(() => {
                                this.message = '';
                                this.description = '';
                                this.messageType = '';
                                this.currentUser = null;
                                this.$refs.barcodeInput.focus();
                            }, 2000);
                        }
                    }, 1000);
                },

                playSound(type) {
                    // Create audio context for different sounds
                    const audioContext = new(window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);

                    // Different sounds for different outcomes
                    if (type === 'success') {
                        // Success: Two ascending beeps (login/logout success)
                        gainNode.gain.value = 0.3;
                        oscillator.frequency.value = 800;
                        oscillator.start(audioContext.currentTime);
                        oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                        oscillator.frequency.setValueAtTime(1000, audioContext.currentTime + 0.1);
                        oscillator.stop(audioContext.currentTime + 0.2);
                    } else if (type === 'archived') {
                        // Archive: Three medium beeps (warning) - louder
                        gainNode.gain.value = 0.7;
                        oscillator.frequency.value = 800;
                        oscillator.start(audioContext.currentTime);
                        oscillator.stop(audioContext.currentTime + 0.1);

                        setTimeout(() => {
                            const osc2 = audioContext.createOscillator();
                            const gain2 = audioContext.createGain();
                            osc2.connect(gain2);
                            gain2.connect(audioContext.destination);
                            gain2.gain.value = 0.7;
                            osc2.frequency.value = 600;
                            osc2.start(audioContext.currentTime);
                            osc2.stop(audioContext.currentTime + 0.1);
                        }, 150);

                        setTimeout(() => {
                            const osc3 = audioContext.createOscillator();
                            const gain3 = audioContext.createGain();
                            osc3.connect(gain3);
                            gain3.connect(audioContext.destination);
                            gain3.gain.value = 0.7;
                            osc3.frequency.value = 800;
                            osc3.start(audioContext.currentTime);
                            osc3.stop(audioContext.currentTime + 0.1);
                        }, 300);
                    } else if (type === 'error') {
                        // Error: Low descending beep (not found or other errors) - louder
                        gainNode.gain.value = 0.7;
                        oscillator.frequency.value = 400;
                        oscillator.start(audioContext.currentTime);
                        oscillator.frequency.setValueAtTime(600, audioContext.currentTime);
                        oscillator.frequency.setValueAtTime(400, audioContext.currentTime + 0.15);
                        oscillator.stop(audioContext.currentTime + 0.3);
                    }
                },

                updateDateTime() {
                    const now = new Date();
                    const options = {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    };
                    this.currentDate = now.toLocaleDateString('en-US', options);
                },

                async scanBarcode() {
                    if (!this.barcode.trim()) return;

                    const now = Date.now();
                    if (this.scanningLocked || (now - this.lastScanAt) < this.scanCooldownMs) {
                        // user attempted to scan too quickly
                        this.message = 'Please wait a moment before scanning again.';
                        this.messageType = 'error';
                        this.playSound('error');
                        this.$refs.barcodeInput.focus();
                        return;
                    }

                    const barcodeValue = this.barcode.trim();
                    this.barcode = '';

                    // lock scanning for cooldown period
                    this.lastScanAt = Date.now();
                    this.scanningLocked = true;
                    setTimeout(() => {
                        this.scanningLocked = false;
                    }, this.scanCooldownMs);

                    try {
                        const response = await fetch('/scanner/scan', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                barcode: barcodeValue,
                                section: this.currentSection
                            })
                        });

                        const data = await response.json();

                        this.message = data.message;
                        this.messageType = data.type;
                        this.description = data.description || '';
                        this.currentUser = data.user || null;

                        // Play sound based on result type
                        if (data.success) {
                            this.playSound('success');
                            await this.refreshLogins();
                            // notify Livewire components (same-session) to refresh stats immediately
                            if (window.Livewire) Livewire.emit('statsUpdated');
                        } else if (data.type === 'archived') {
                            this.playSound('archived');
                        } else {
                            // Any other error (not found, inactive, expired, etc.)
                            this.playSound('error');
                        }
                    } catch (error) {
                        this.message = 'System error. Please try again.';
                        this.messageType = 'error';
                        this.description = '';
                        this.playSound('error');
                    }

                    this.$refs.barcodeInput.focus();
                },

                async refreshLogins() {
                    try {
                        const response = await fetch('/scanner/today-logins');

                        const data = await response.json();
                        if (data.success) {
                            this.recentLogins = data.logins;
                            this.activeUsers = data.activeUsers || 0;
                        }
                    } catch (error) {
                        console.error('Failed to refresh logins:', error);
                    }
                },

                async verifyPassword() {
                    this.isVerifying = true;
                    this.passwordError = '';

                    try {
                        const response = await fetch('/api/verify-admin-password', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                password: this.adminPassword
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Password correct, open sidebar
                            this.showPasswordModal = false;
                            this.adminPassword = '';
                            this.passwordError = '';
                            this.sidebarOpen = true;
                        } else {
                            // Password incorrect
                            this.passwordError = data.message || 'Incorrect password. Please try again.';
                            this.adminPassword = '';
                            this.$refs.passwordInput.focus();
                        }
                    } catch (error) {
                        console.error('Password verification error:', error);
                        this.passwordError = 'An error occurred. Please try again.';
                    } finally {
                        this.isVerifying = false;
                    }
                },

                closePasswordModal() {
                    this.showPasswordModal = false;
                    this.adminPassword = '';
                    this.passwordError = '';
                    this.isVerifying = false;
                },

                async changeSection(code, name) {
                    try {
                        const response = await fetch('/scanner/set-section', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                section: code
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.currentSection = code;
                            this.sectionName = data.sectionName;
                            this.sidebarOpen = false;
                            await this.refreshLogins();
                        }
                    } catch (error) {
                        console.error('Failed to change section:', error);
                    }
                }
            }
        }
    </script>
</body>

</html>
