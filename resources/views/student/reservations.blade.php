<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reserve a Room - CLSU Library</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #16a34a;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #15803d;
        }
    </style>
    <script>
        // Store rooms data globally before Alpine loads
        window.roomsData = @json($rooms);
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-linear-to-br from-green-50 to-green-100 min-h-screen">
    <div x-data="reservationApp(window.roomsData)" x-init="init()">
        <!-- Header -->
        <header class="bg-linear-to-r from-green-600 to-green-700 text-white shadow-xl">
            <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('scanner.index') }}"
                            class="p-2 rounded-lg bg-white/10 hover:bg-white/20 transition-all duration-200 backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight">Room Reservation</h1>
                            <p class="text-green-50 text-sm mt-1 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                </svg>
                                CLSU Library and Information Services Office
                            </p>
                        </div>
                    </div>
                    <div class="text-right bg-white/10 backdrop-blur-sm px-4 py-3 rounded-lg border border-white/20">
                        <p class="text-xs text-green-100 uppercase tracking-wider">Current Date</p>
                        <p class="font-semibold text-lg"
                            x-text="new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })">
                        </p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
            <!-- Progress Indicator -->
            <div class="mb-8">
                <div class="flex items-center justify-center">
                    <div class="flex items-center">
                        <!-- Step 1 -->
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-12 h-12 rounded-full transition-all duration-300 shadow-lg"
                                :class="currentStep === 1 ? 'bg-green-600 text-white scale-110' :
                                    'bg-white text-green-600 border-2 border-green-600'">
                                <span class="font-bold text-lg">1</span>
                            </div>
                            <div class="ml-3 text-left">
                                <p class="text-xs text-gray-600 uppercase tracking-wider">Step 1</p>
                                <p class="font-semibold text-gray-900"
                                    :class="currentStep === 1 ? 'text-green-700' : 'text-gray-700'">
                                    Select Date & Time
                                </p>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="mx-8 h-1 w-20 rounded-full transition-colors duration-300"
                            :class="currentStep === 2 ? 'bg-green-600' : 'bg-gray-300'"></div>

                        <!-- Step 2 -->
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-12 h-12 rounded-full transition-all duration-300 shadow-lg"
                                :class="currentStep === 2 ? 'bg-green-600 text-white scale-110' :
                                    'bg-white text-gray-400 border-2 border-gray-300'">
                                <span class="font-bold text-lg">2</span>
                            </div>
                            <div class="ml-3 text-left">
                                <p class="text-xs text-gray-600 uppercase tracking-wider">Step 2</p>
                                <p class="font-semibold"
                                    :class="currentStep === 2 ? 'text-green-700' : 'text-gray-500'">
                                    Confirm Details
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 1: Calendar & Room Selection -->
            <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-4"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform -translate-x-4"
                class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                <!-- Room Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 sticky top-6">
                        <div class="flex items-center gap-2 mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-600">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819" />
                            </svg>
                            <h3 class="text-xl font-bold text-gray-900">Available Rooms</h3>
                        </div>
                        <div class="space-y-3 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                            @foreach ($rooms as $room)
                                <button @click="selectRoom({{ $room->id }})"
                                    :class="selectedRoom?.id === {{ $room->id }} ?
                                        'bg-linear-to-r from-green-50 to-green-100 border-green-500 ring-2 ring-green-500 shadow-md' :
                                        'bg-gray-50 border-gray-200 hover:bg-gray-100 hover:border-gray-300 hover:shadow'"
                                    class="w-full text-left p-4 rounded-xl border-2 transition-all duration-200">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="font-bold text-gray-900 text-base">{{ $room->name }}</p>
                                            <p class="text-sm text-gray-600 mt-1 leading-relaxed">
                                                {{ $room->description }}</p>
                                        </div>
                                        <div x-show="selectedRoom?.id === {{ $room->id }}" class="ml-2 shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="currentColor" class="w-6 h-6 text-green-600">
                                                <path fill-rule="evenodd"
                                                    d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>

                        <!-- Legend -->
                        <div class="mt-6 pt-6 border-t-2 border-gray-200">
                            <h4 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wide">Status Legend</h4>
                            <div class="space-y-3 text-sm">
                                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="w-5 h-5 bg-green-500 rounded-md shadow-sm"></div>
                                    <span class="text-gray-700 font-medium">Available</span>
                                </div>
                                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="w-5 h-5 bg-yellow-500 rounded-md shadow-sm"></div>
                                    <span class="text-gray-700 font-medium">Pending</span>
                                </div>
                                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="w-5 h-5 bg-red-500 rounded-md shadow-sm"></div>
                                    <span class="text-gray-700 font-medium">Reserved</span>
                                </div>
                                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="w-5 h-5 bg-gray-400 rounded-md shadow-sm"></div>
                                    <span class="text-gray-700 font-medium">Blocked</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <div x-show="!selectedRoom" class="text-center py-20">
                            <div
                                class="inline-flex items-center justify-center w-20 h-20 bg-green-50 rounded-full mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-green-600">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Select a Room to Begin</h3>
                            <p class="text-gray-600 max-w-md mx-auto">Choose a room from the sidebar to view available
                                time slots and make your reservation</p>
                        </div>

                        <div x-show="selectedRoom">
                            {{-- Calendar Header --}}
                            <div class="flex items-center justify-between mb-6">
                                <button @click="previousMonth()" type="button"
                                    class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 19.5L8.25 12l7.5-7.5" />
                                    </svg>
                                </button>

                                <div class="flex items-center gap-4">
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white"
                                        x-text="currentMonthDisplay"></h2>
                                    <button @click="goToToday()" type="button"
                                        class="px-3 py-1 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                        Today
                                    </button>
                                </div>

                                <button @click="nextMonth()" type="button"
                                    class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Calendar Grid --}}
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                {{-- Day Headers --}}
                                <div class="grid grid-cols-7 bg-gray-50 dark:bg-gray-800">
                                    <div
                                        class="text-center py-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        Sun</div>
                                    <div
                                        class="text-center py-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        Mon</div>
                                    <div
                                        class="text-center py-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        Tue</div>
                                    <div
                                        class="text-center py-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        Wed</div>
                                    <div
                                        class="text-center py-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        Thu</div>
                                    <div
                                        class="text-center py-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        Fri</div>
                                    <div
                                        class="text-center py-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        Sat</div>
                                </div>

                                {{-- Calendar Days --}}
                                <div class="grid grid-cols-7 bg-white dark:bg-gray-900">
                                    <template x-for="day in calendarDays" :key="day.date">
                                        <div class="border border-gray-200 dark:border-gray-700 min-h-32 p-2"
                                            :class="!day.isCurrentMonth ? 'bg-gray-100 dark:bg-gray-900' : ''">
                                            <div class="text-sm font-semibold mb-2"
                                                :class="day.isToday ? 'text-green-600 dark:text-green-400' : day
                                                    .isCurrentMonth ? 'text-gray-700 dark:text-gray-300' :
                                                    'text-gray-400'"
                                                x-text="day.dayNumber"></div>

                                            {{-- Time Slots --}}
                                            <div class="space-y-1">
                                                <template x-for="slot in getAvailableSlotsForDay(day.date)"
                                                    :key="slot.start">
                                                    <button @click="selectTimeSlot(day.date, slot)"
                                                        :disabled="!slot.isAvailable || day.isPast"
                                                        :class="{
                                                            'bg-green-500 hover:bg-green-600 text-white cursor-pointer': slot
                                                                .isAvailable && !day.isPast,
                                                            'bg-red-500 text-white cursor-not-allowed': !slot
                                                                .isAvailable && slot.status === 'approved',
                                                            'bg-yellow-500 text-white cursor-not-allowed': !slot
                                                                .isAvailable && slot.status === 'pending',
                                                            'bg-gray-400 text-white cursor-not-allowed': !slot
                                                                .isAvailable && slot.status === 'blocked',
                                                            'bg-gray-300 text-gray-500 cursor-not-allowed': day.isPast
                                                        }"
                                                        class="w-full text-xs px-2 py-1 rounded transition-colors"
                                                        x-text="slot.label">
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Reservation Form -->
            <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-4"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform -translate-x-4">

                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8">
                        <div class="mb-8">
                            <h2 class="text-3xl font-bold text-gray-900">Complete Your Reservation</h2>
                            <p class="text-gray-600 mt-2">Review your selected details and provide additional
                                information</p>
                        </div>

                        <form @submit.prevent="submitReservation()">
                            <!-- Selected Details (Read-only) -->
                            <div
                                class="mb-8 bg-linear-to-r from-green-50 to-green-100 rounded-xl p-6 border-2 border-green-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-6 h-6 text-green-600">
                                        <path fill-rule="evenodd"
                                            d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Selected Reservation Details
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <label
                                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Room</label>
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="w-5 h-5 text-green-600">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819" />
                                            </svg>
                                            <p class="text-gray-900 font-semibold" x-text="selectedRoom?.name"></p>
                                        </div>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <label
                                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Date</label>
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="w-5 h-5 text-green-600">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                            </svg>
                                            <p class="text-gray-900 font-semibold" x-text="selectedDate"></p>
                                        </div>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <label
                                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Start
                                            Time</label>
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="w-5 h-5 text-green-600">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            <p class="text-gray-900 font-semibold" x-text="selectedStartTime"></p>
                                        </div>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <label
                                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">End
                                            Time</label>
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="w-5 h-5 text-green-600">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            <p class="text-gray-900 font-semibold" x-text="selectedEndTime"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- User Input Fields -->
                            <div class="space-y-6">
                                <div>
                                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-600">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                                        </svg>
                                        Student ID
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" x-model="barcode" required
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all shadow-sm"
                                        placeholder="Enter your student ID number">
                                    <p class="text-xs text-gray-500 mt-2">Your official student identification number
                                    </p>
                                </div>

                                <div>
                                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-600">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        Purpose of Reservation
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <textarea x-model="purpose" rows="5" required maxlength="500"
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all shadow-sm resize-none"
                                        placeholder="Please describe the purpose of your reservation (e.g., Group study, Meeting, Research, etc.)"></textarea>
                                    <div class="flex justify-between items-center mt-2">
                                        <p class="text-xs text-gray-500">Be specific about your intended use of the
                                            room</p>
                                        <p class="text-xs font-medium"
                                            :class="purpose.length > 450 ? 'text-red-600' : 'text-gray-500'"
                                            x-text="`${purpose.length}/500`">
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-4 mt-10 pt-6 border-t-2 border-gray-200">
                                <button type="button" @click="currentStep = 1"
                                    class="flex-1 px-6 py-4 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                    <span class="flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                                        </svg>
                                        Back to Calendar
                                    </span>
                                </button>
                                <button type="submit" :disabled="submitting"
                                    :class="submitting ? 'opacity-60 cursor-not-allowed' :
                                        'hover:bg-green-700 hover:shadow-lg transform hover:-translate-y-0.5'"
                                    class="flex-1 px-6 py-4 bg-linear-to-r from-green-600 to-green-700 text-white font-bold rounded-xl transition-all duration-200 shadow-md">
                                    <span x-show="!submitting" class="flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        Submit Reservation
                                    </span>
                                    <span x-show="submitting" class="flex items-center justify-center gap-2">
                                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Submitting...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Toast Notification Component -->
    @include('template.toast')
</body>

</html>
