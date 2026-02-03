<div class="px-6 py-6" x-data="reservedRoomsData('{{ csrf_token() }}')" x-init="init()">
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Reserved Rooms</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Manage room reservations and availability</p>
    </div>

    {{-- Filters and Actions --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Room:</label>
                <select x-model="selectedRoom" @change="loadReservations()"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500">
                    <option value="">All Rooms</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                    @endforeach
                </select>
            </div>

            <button @click="openBlockModal()" type="button"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                Block Time Slot
            </button>
        </div>

        {{-- Legend --}}
        <div class="mt-4 flex items-center gap-6 text-sm">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                <span class="text-gray-700 dark:text-gray-300">Pending</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-green-500 rounded"></div>
                <span class="text-gray-700 dark:text-gray-300">Approved</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-gray-500 rounded"></div>
                <span class="text-gray-700 dark:text-gray-300">Blocked</span>
            </div>
        </div>
    </div>

    {{-- Calendar Navigation --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="flex items-center justify-between">
            <button @click="previousMonth()"
                class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </button>

            <div class="text-center">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white" x-text="currentMonthDisplay"></h2>
                <button @click="goToToday()" class="text-sm text-green-600 dark:text-green-400 hover:underline mt-1">
                    Today
                </button>
            </div>

            <button @click="nextMonth()"
                class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Monthly Calendar --}}
    <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
        {{-- Day Headers --}}
        <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-700">
            <template x-for="dayName in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="dayName">
                <div
                    class="p-3 bg-gray-50 dark:bg-gray-900 text-center border-r border-gray-200 dark:border-gray-700 last:border-r-0">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white" x-text="dayName"></div>
                </div>
            </template>
        </div>

        {{-- Calendar Days --}}
        <div class="grid grid-cols-7">
            <template x-for="day in calendarDays" :key="day.date">
                <div class="border-r border-b border-gray-200 dark:border-gray-700 last:border-r-0 min-h-32 p-2"
                    :class="!day.isCurrentMonth ? 'bg-gray-50 dark:bg-gray-900 opacity-50' : ''">
                    {{-- Day Number --}}
                    <div class="text-sm font-semibold mb-2"
                        :class="day.isToday ? 'text-green-600 dark:text-green-400' : 'text-gray-700 dark:text-gray-300'"
                        x-text="day.dayNumber"></div>

                    {{-- Reservations for this day --}}
                    <div class="space-y-1">
                        <template x-for="reservation in getReservationsForDay(day.date)" :key="reservation.id">
                            <button @click="openReservationModal(reservation)"
                                :class="{
                                    'bg-yellow-100 dark:bg-yellow-900 border-yellow-500 text-yellow-900 dark:text-yellow-100': reservation
                                        .status === 'pending',
                                    'bg-green-100 dark:bg-green-900 border-green-500 text-green-900 dark:text-green-100': reservation
                                        .status === 'approved',
                                    'bg-gray-100 dark:bg-gray-700 border-gray-500 text-gray-900 dark:text-gray-100': reservation
                                        .type === 'blocked'
                                }"
                                class="w-full text-left p-1.5 rounded border-l-4 hover:shadow-md transition-all text-xs">
                                <div class="font-semibold truncate" x-text="reservation.time_display"></div>
                                <div class="truncate text-xs"
                                    x-text="reservation.type === 'blocked' ? 'BLOCKED' : reservation.room_name"></div>
                            </button>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Reservation Detail Modal --}}
    <div x-show="showReservationModal" @keydown.escape.window="showReservationModal && closeReservationModal()" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-md" x-transition>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg dark:shadow-gray-900 w-160 max-h-[90vh] overflow-y-auto"
            @click.stop>

            {{-- Header --}}
            <div
                class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center sticky top-0 bg-white dark:bg-gray-800">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Reservation Details</h2>
                <button @click="closeReservationModal()"
                    class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-4" x-show="currentReservation">
                <div class="space-y-4">
                    {{-- User Information --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student
                                Name</label>
                            <input type="text" :value="currentReservation?.user?.name || 'N/A'" disabled
                                class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student
                                ID</label>
                            <input type="text" :value="currentReservation?.user_id || 'N/A'" disabled
                                class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white cursor-not-allowed">
                        </div>
                    </div>

                    {{-- Reservation Details --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Room</label>
                            <input type="text" x-model="editData.room_name" :disabled="!editMode"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
                                :class="!editMode ? 'bg-gray-50 dark:bg-gray-700 cursor-not-allowed' :
                                    'bg-white dark:bg-gray-800'">
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select x-model="editData.status" :disabled="!editMode"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
                                :class="!editMode ? 'bg-gray-50 dark:bg-gray-700 cursor-not-allowed' :
                                    'bg-white dark:bg-gray-800'">
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                            <input type="date" x-model="editData.reservation_date" :disabled="!editMode"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
                                :class="!editMode ? 'bg-gray-50 dark:bg-gray-700 cursor-not-allowed' :
                                    'bg-white dark:bg-gray-800'">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start
                                Time</label>
                            <select x-model="editData.start_time" :disabled="!editMode"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
                                :class="!editMode ? 'bg-gray-50 dark:bg-gray-700 cursor-not-allowed' :
                                    'bg-white dark:bg-gray-800'">
                                <option value="08:00">8:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="14:00">2:00 PM</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End
                                Time</label>
                            <select x-model="editData.end_time" :disabled="!editMode"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
                                :class="!editMode ? 'bg-gray-50 dark:bg-gray-700 cursor-not-allowed' :
                                    'bg-white dark:bg-gray-800'">
                                <option value="10:00">10:00 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="14:00">2:00 PM</option>
                                <option value="16:00">4:00 PM</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Purpose</label>
                        <textarea x-model="editData.purpose" rows="3" :disabled="!editMode"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white resize-none"
                            :class="!editMode ? 'bg-gray-50 dark:bg-gray-700 cursor-not-allowed' : 'bg-white dark:bg-gray-800'"></textarea>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div
                class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 flex justify-between sticky bottom-0">
                {{-- Left side buttons --}}
                <div class="flex gap-3">
                    {{-- Show Edit button when not in edit mode (for all reservations) --}}
                    <template x-if="!editMode">
                        <button @click="enableEditMode()" type="button"
                            class="w-28 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Edit
                        </button>
                    </template>

                    {{-- Show Save/Cancel when in edit mode --}}
                    <template x-if="editMode">
                        <button @click="saveReservation()" type="button" :disabled="saving"
                            :class="saving ? 'opacity-50 cursor-not-allowed' : ''"
                            class="w-28 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <span x-show="!saving">Save</span>
                            <span x-show="saving">Saving...</span>
                        </button>
                    </template>

                    <template x-if="editMode">
                        <button @click="cancelEdit()" type="button"
                            class="w-28 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                    </template>
                </div>

                {{-- Right side buttons --}}
                <div class="flex gap-3">
                    <button @click="closeReservationModal()" type="button"
                        class="w-28 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        Close
                    </button>

                    {{-- Show Approve/Decline only when not in edit mode and status is pending --}}
                    <template x-if="!editMode && currentReservation?.status === 'pending'">
                        <button @click="approveReservation()" type="button" :disabled="saving"
                            :class="saving ? 'opacity-50 cursor-not-allowed' : ''"
                            class="w-28 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <span x-show="!saving">Approve</span>
                            <span x-show="saving">Approving...</span>
                        </button>
                    </template>

                    <template x-if="!editMode && currentReservation?.status === 'pending'">
                        <button @click="confirmDecline()" type="button"
                            class="w-28 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Decline
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Decline Confirmation Modal --}}
    <div x-show="showDeclineModal" @keydown.escape.window="showDeclineModal && closeDeclineModal()" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-md" x-transition>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg dark:shadow-gray-900 w-120 p-6" @click.stop>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Decline Reservation</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Are you sure you want to decline this reservation? This action will delete the reservation and cannot be
                undone.
            </p>
            <div class="flex gap-3 justify-end">
                <button @click="closeDeclineModal()" type="button"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button @click="declineReservation()" type="button" :disabled="deleting"
                    :class="deleting ? 'opacity-50 cursor-not-allowed' : ''"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <span x-show="!deleting">Decline & Delete</span>
                    <span x-show="deleting">Declining...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Blocked Slot Detail Modal --}}
    <div x-show="showBlockedSlotModal" @keydown.escape.window="showBlockedSlotModal && closeBlockedSlotModal()"
        x-cloak class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-md" x-transition>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg dark:shadow-gray-900 w-160 max-h-[90vh] overflow-y-auto"
            @click.stop>

            {{-- Header --}}
            <div
                class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center sticky top-0 bg-white dark:bg-gray-800">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Blocked Time Slot</h2>
                <button @click="closeBlockedSlotModal()"
                    class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Content --}}
            <div class="px-6 py-4">
                <div class="space-y-4">
                    {{-- Room --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Room</label>
                        <div
                            class="px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
                            <span x-text="currentBlockedSlot?.room?.name"></span>
                        </div>
                    </div>

                    {{-- Date --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Blocked
                            Date</label>
                        <div
                            class="px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
                            <span
                                x-text="new Date(currentBlockedSlot?.blocked_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })"></span>
                        </div>
                    </div>

                    {{-- Time Range --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start
                                Time</label>
                            <div
                                class="px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
                                <span x-text="currentBlockedSlot?.start_time"></span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End
                                Time</label>
                            <div
                                class="px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
                                <span x-text="currentBlockedSlot?.end_time"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Reason --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason</label>
                        <div
                            class="px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white min-h-24">
                            <span x-text="currentBlockedSlot?.reason || 'No reason provided'"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div
                class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 flex justify-between sticky bottom-0">
                <button @click="unblockTimeSlot()" type="button" :disabled="unblocking"
                    :class="unblocking ? 'opacity-50 cursor-not-allowed' : ''"
                    class="w-28 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <span x-show="!unblocking">Unblock</span>
                    <span x-show="unblocking">Unblocking...</span>
                </button>

                <button @click="closeBlockedSlotModal()" type="button"
                    class="w-28 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- Block Time Slot Modal --}}
    <div x-show="showBlockModal" @keydown.escape.window="showBlockModal && closeBlockModal()" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-md" x-transition>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg dark:shadow-gray-900 w-160 max-h-[90vh] overflow-y-auto"
            @click.stop>

            {{-- Header --}}
            <div
                class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center sticky top-0 bg-white dark:bg-gray-800">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Block Time Slot</h2>
                <button @click="closeBlockModal()"
                    class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Form --}}
            <form @submit.prevent="blockTimeSlot()">
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Room</label>
                        <select x-model="blockData.room_id" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500">
                            <option value="">Select a room</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                        <input type="date" x-model="blockData.blocked_date" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start
                                Time</label>
                            <select x-model="blockData.start_time" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500">
                                <option value="">Select start time</option>
                                <option value="08:00">8:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="14:00">2:00 PM</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End
                                Time</label>
                            <select x-model="blockData.end_time" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500">
                                <option value="">Select end time</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="14:00">2:00 PM</option>
                                <option value="16:00">4:00 PM</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason
                            (Optional)</label>
                        <textarea x-model="blockData.reason" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 resize-none"
                            placeholder="e.g., Maintenance, Event, etc."></textarea>
                    </div>
                </div>

                {{-- Footer --}}
                <div
                    class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 flex gap-3 sticky bottom-0">
                    <button @click="closeBlockModal()" type="button"
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" :disabled="blocking"
                        :class="blocking ? 'opacity-50 cursor-not-allowed' : ''"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <span x-show="!blocking">Block Time Slot</span>
                        <span x-show="blocking">Blocking...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function reservedRoomsData(csrfToken) {
        return {
            // State
            selectedRoom: '',
            currentMonth: null,
            currentYear: null,
            calendarDays: [],
            reservations: [],
            csrfToken: csrfToken,

            // Modals
            showReservationModal: false,
            showBlockModal: false,
            showDeclineModal: false,
            showBlockedSlotModal: false,
            currentReservation: null,
            currentBlockedSlot: null,
            editMode: false,
            saving: false,
            blocking: false,
            deleting: false,
            unblocking: false,

            // Edit Data
            editData: {
                room_name: '',
                status: '',
                reservation_date: '',
                start_time: '',
                end_time: '',
                purpose: ''
            },

            // Block Data
            blockData: {
                room_id: '',
                blocked_date: '',
                start_time: '',
                end_time: '',
                reason: ''
            },

            init() {
                this.goToToday();
                this.loadReservations();
            },

            get currentMonthDisplay() {
                if (this.currentMonth === null || this.currentMonth === undefined || !this.currentYear) return '';
                const date = new Date(this.currentYear, this.currentMonth);
                return date.toLocaleDateString('en-US', {
                    month: 'long',
                    year: 'numeric'
                });
            },

            goToToday() {
                const today = new Date();
                this.currentMonth = today.getMonth();
                this.currentYear = today.getFullYear();
                this.generateCalendarDays();
            },

            previousMonth() {
                this.currentMonth--;
                if (this.currentMonth < 0) {
                    this.currentMonth = 11;
                    this.currentYear--;
                }
                this.generateCalendarDays();
                this.loadReservations();
            },

            nextMonth() {
                this.currentMonth++;
                if (this.currentMonth > 11) {
                    this.currentMonth = 0;
                    this.currentYear++;
                }
                this.generateCalendarDays();
                this.loadReservations();
            },

            generateCalendarDays() {
                this.calendarDays = [];

                // First day of the month
                const firstDay = new Date(this.currentYear, this.currentMonth, 1);
                const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);

                // Start from Sunday of the week containing the first day
                const startDate = new Date(firstDay);
                startDate.setDate(startDate.getDate() - startDate.getDay());

                // Generate 42 days (6 weeks) to fill the calendar
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                for (let i = 0; i < 42; i++) {
                    const date = new Date(startDate);
                    date.setDate(date.getDate() + i);

                    const dayDate = new Date(date);
                    dayDate.setHours(0, 0, 0, 0);

                    // Format date as YYYY-MM-DD in local timezone (not UTC)
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    const localDateString = `${year}-${month}-${day}`;

                    this.calendarDays.push({
                        date: localDateString,
                        dayNumber: date.getDate(),
                        isCurrentMonth: date.getMonth() === this.currentMonth,
                        isToday: dayDate.getTime() === today.getTime()
                    });
                }
            },

            async loadReservations() {
                try {
                    const firstDay = this.calendarDays[0].date;
                    const lastDay = this.calendarDays[this.calendarDays.length - 1].date;
                    const roomFilter = this.selectedRoom ? `&room_id=${this.selectedRoom}` : '';

                    console.log('Loading reservations from', firstDay, 'to', lastDay);

                    const response = await fetch(
                        `/reserved-rooms/calendar-data?start_date=${firstDay}&end_date=${lastDay}${roomFilter}`);
                    const data = await response.json();

                    console.log('API Response:', data);

                    if (data.success) {
                        this.reservations = this.transformReservations(data);
                        console.log('Transformed reservations:', this.reservations);
                    }
                } catch (error) {
                    console.error('Error loading reservations:', error);
                }
            },

            transformReservations(data) {
                const all = [];

                const timeLabels = {
                    '08:00': '8AM-10AM',
                    '10:00': '10AM-12PM',
                    '12:00': '12PM-2PM',
                    '14:00': '2PM-4PM'
                };

                // Add regular reservations
                data.reservations.forEach(res => {
                    const startKey = res.start_time.substring(0, 5);
                    // Handle date - it might be a plain YYYY-MM-DD string or a timestamp
                    const dateOnly = res.reservation_date.includes('T') ?
                        res.reservation_date.split('T')[0] :
                        res.reservation_date;

                    all.push({
                        id: res.id,
                        type: 'reservation',
                        status: res.status,
                        room_name: res.room.name,
                        user_name: res.user.name,
                        date: dateOnly,
                        start_time: res.start_time,
                        end_time: res.end_time,
                        time_display: timeLabels[startKey] ||
                            `${startKey}-${res.end_time.substring(0, 5)}`,
                        raw: res
                    });
                });

                // Add blocked slots
                data.blocked_slots.forEach(blocked => {
                    const startKey = blocked.start_time.substring(0, 5);
                    // Handle date - it might be a plain YYYY-MM-DD string or a timestamp
                    const dateOnly = blocked.blocked_date.includes('T') ?
                        blocked.blocked_date.split('T')[0] :
                        blocked.blocked_date;

                    all.push({
                        id: `blocked-${blocked.id}`,
                        type: 'blocked',
                        status: 'blocked',
                        room_name: blocked.room.name,
                        user_name: 'BLOCKED',
                        date: dateOnly,
                        start_time: blocked.start_time,
                        end_time: blocked.end_time,
                        time_display: timeLabels[startKey] ||
                            `${startKey}-${blocked.end_time.substring(0, 5)}`,
                        raw: blocked
                    });
                });

                return all;
            },

            getReservationsForDay(date) {
                return this.reservations.filter(res => res.date === date);
            },

            openReservationModal(reservation) {
                if (reservation.type === 'blocked') {
                    this.openBlockedSlotModal(reservation);
                    return;
                }

                this.currentReservation = reservation.raw;

                // Use the already-transformed date from reservation.date instead of raw
                // This ensures we get the correct date without timezone issues
                this.editData = {
                    room_name: reservation.room_name,
                    status: reservation.raw.status,
                    reservation_date: reservation.date, // Use the transformed date
                    start_time: reservation.start_time.substring(0, 5),
                    end_time: reservation.end_time.substring(0, 5),
                    purpose: reservation.raw.purpose
                };
                this.showReservationModal = true;
            },

            closeReservationModal() {
                this.showReservationModal = false;
                this.editMode = false;
                this.currentReservation = null;
            },

            enableEditMode() {
                this.editMode = true;
            },

            cancelEdit() {
                this.editMode = false;
                // Restore original data
                if (this.currentReservation) {
                    this.editData = {
                        room_name: this.currentReservation.room.name,
                        status: this.currentReservation.status,
                        reservation_date: this.currentReservation.reservation_date.split('T')[0],
                        start_time: this.currentReservation.start_time,
                        end_time: this.currentReservation.end_time,
                        purpose: this.currentReservation.purpose
                    };
                }
            },

            async approveReservation() {
                if (this.saving) return;
                this.saving = true;

                try {
                    const response = await fetch(
                        `/reserved-rooms/reservation/${this.currentReservation.id}/update`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken
                            },
                            body: JSON.stringify({
                                status: 'approved'
                            })
                        });

                    const data = await response.json();

                    if (data.success) {
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: {
                                type: 'success',
                                message: 'Reservation approved successfully'
                            }
                        }));
                        this.closeReservationModal();
                        this.loadReservations();
                    } else {
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: {
                                type: 'error',
                                message: data.message
                            }
                        }));
                    }
                } catch (error) {
                    console.error('Error approving reservation:', error);
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: {
                            type: 'error',
                            message: 'An error occurred while approving'
                        }
                    }));
                } finally {
                    this.saving = false;
                }
            },

            confirmDecline() {
                this.showDeclineModal = true;
            },

            closeDeclineModal() {
                this.showDeclineModal = false;
            },

            async declineReservation() {
                if (this.deleting) return;
                this.deleting = true;

                try {
                    const response = await fetch(`/reserved-rooms/reservation/${this.currentReservation.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: {
                                type: 'success',
                                message: 'Reservation declined and deleted'
                            }
                        }));
                        this.closeDeclineModal();
                        this.closeReservationModal();
                        this.loadReservations();
                    } else {
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: {
                                type: 'error',
                                message: data.message
                            }
                        }));
                    }
                } catch (error) {
                    console.error('Error declining reservation:', error);
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: {
                            type: 'error',
                            message: 'An error occurred while declining'
                        }
                    }));
                } finally {
                    this.deleting = false;
                }
            },

            async saveReservation() {
                if (this.saving) return;
                this.saving = true;

                try {
                    const response = await fetch(
                        `/reserved-rooms/reservation/${this.currentReservation.id}/update`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken
                            },
                            body: JSON.stringify({
                                status: this.editData.status,
                                reservation_date: this.editData.reservation_date,
                                start_time: this.editData.start_time,
                                end_time: this.editData.end_time,
                                purpose: this.editData.purpose
                            })
                        });

                    const data = await response.json();

                    if (data.success) {
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: {
                                type: 'success',
                                message: data.message
                            }
                        }));
                        this.closeReservationModal();
                        this.loadReservations();
                    } else {
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: {
                                type: 'error',
                                message: data.message
                            }
                        }));
                    }
                } catch (error) {
                    console.error('Error saving reservation:', error);
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: {
                            type: 'error',
                            message: 'An error occurred while saving'
                        }
                    }));
                } finally {
                    this.saving = false;
                }
            },

            openBlockModal() {
                this.blockData = {
                    room_id: '',
                    blocked_date: '',
                    start_time: '',
                    end_time: '',
                    reason: ''
                };
                this.showBlockModal = true;
            },

            closeBlockModal() {
                this.showBlockModal = false;
            },

            async blockTimeSlot() {
                if (this.blocking) return;
                this.blocking = true;

                try {
                    const response = await fetch('/reserved-rooms/block-time', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: JSON.stringify(this.blockData)
                    });

                    const data = await response.json();

                    if (data.success) {
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: {
                                type: 'success',
                                message: data.message
                            }
                        }));
                        this.closeBlockModal();
                        this.loadReservations();
                    } else {
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: {
                                type: 'error',
                                message: data.message
                            }
                        }));
                    }
                } catch (error) {
                    console.error('Error blocking time slot:', error);
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: {
                            type: 'error',
                            message: 'An error occurred while blocking the time slot'
                        }
                    }));
                } finally {
                    this.blocking = false;
                }
            },

            openBlockedSlotModal(blockedSlot) {
                this.currentBlockedSlot = blockedSlot.raw;
                this.showBlockedSlotModal = true;
            },

            closeBlockedSlotModal() {
                this.showBlockedSlotModal = false;
                this.currentBlockedSlot = null;
            },

            async unblockTimeSlot() {
                if (this.unblocking) return;
                this.unblocking = true;

                try {
                    const response = await fetch('/reserved-rooms/unblock-time', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: JSON.stringify({
                            id: this.currentBlockedSlot.id
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: {
                                type: 'success',
                                message: data.message
                            }
                        }));
                        this.closeBlockedSlotModal();
                        this.loadReservations();
                    } else {
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: {
                                type: 'error',
                                message: data.message
                            }
                        }));
                    }
                } catch (error) {
                    console.error('Error unblocking time slot:', error);
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: {
                            type: 'error',
                            message: 'An error occurred while unblocking the time slot'
                        }
                    }));
                } finally {
                    this.unblocking = false;
                }
            }
        };
    }
</script>
