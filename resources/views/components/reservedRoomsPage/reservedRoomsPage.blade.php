<div class="px-6 py-6" x-data="reservedRoomsApp()" x-init="init()">
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Reserved Rooms</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Manage room reservations and availability</p>
    </div>

    {{-- Room Filter --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="flex items-center gap-4">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter by Room:</label>
            <select x-model="selectedRoomFilter" @change="calendar.refetchEvents()"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500">
                <option value="">All Rooms</option>
                @foreach($rooms as $room)
                <option value="{{ $room->id }}">{{ $room->name }}</option>
                @endforeach
            </select>

            {{-- Legend --}}
            <div class="ml-auto flex items-center gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-green-500 rounded"></div>
                    <span class="text-gray-700 dark:text-gray-300">Available</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                    <span class="text-gray-700 dark:text-gray-300">Pending</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-red-500 rounded"></div>
                    <span class="text-gray-700 dark:text-gray-300">Approved</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-gray-400 rounded"></div>
                    <span class="text-gray-700 dark:text-gray-300">Blocked</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Calendar --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <div id="admin-calendar"></div>
    </div>

    {{-- Reservation Detail Modal --}}
    <div x-show="showReservationModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeReservationModal()"></div>

            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Reservation Details</h3>
                </div>

                <div class="px-6 py-4" x-show="currentReservation">
                    <template x-if="currentReservation">
                        <div class="space-y-4">
                            {{-- User Information --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student Name</label>
                                    <input type="text" :value="currentReservation.user?.name || 'N/A'" disabled
                                        class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student ID</label>
                                    <input type="text" :value="currentReservation.user_id" disabled
                                        class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white cursor-not-allowed">
                                </div>
                            </div>

                            {{-- Reservation Details --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Room</label>
                                    <input type="text" x-model="editData.room_name" :disabled="!editMode"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
                                        :class="!editMode ? 'bg-gray-50 dark:bg-gray-700 cursor-not-allowed' : 'bg-white dark:bg-gray-800'">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                    <select x-model="editData.status" :disabled="!editMode"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
                                        :class="!editMode ? 'bg-gray-50 dark:bg-gray-700 cursor-not-allowed' : 'bg-white dark:bg-gray-800'">
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
                                        :class="!editMode ? 'bg-gray-50 dark:bg-gray-700 cursor-not-allowed' : 'bg-white dark:bg-gray-800'">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Time</label>
                                    <input type="time" x-model="editData.start_time" :disabled="!editMode"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
                                        :class="!editMode ? 'bg-gray-50 dark:bg-gray-700 cursor-not-allowed' : 'bg-white dark:bg-gray-800'">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Time</label>
                                    <input type="time" x-model="editData.end_time" :disabled="!editMode"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
                                        :class="!editMode ? 'bg-gray-50 dark:bg-gray-700 cursor-not-allowed' : 'bg-white dark:bg-gray-800'">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Purpose</label>
                                <textarea x-model="editData.purpose" rows="3" :disabled="!editMode"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
                                    :class="!editMode ? 'bg-gray-50 dark:bg-gray-700 cursor-not-allowed' : 'bg-white dark:bg-gray-800'"></textarea>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 flex gap-3">
                    <button @click="closeReservationModal()" type="button"
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        Close
                    </button>
                    
                    <button x-show="!editMode" @click="enableEditMode()" type="button"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Edit
                    </button>

                    <button x-show="editMode" @click="saveReservation()" type="button" :disabled="saving"
                        :class="saving ? 'opacity-50 cursor-not-allowed' : ''"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <span x-show="!saving">Save</span>
                        <span x-show="saving">Saving...</span>
                    </button>

                    <button @click="confirmDelete()" type="button"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Block Time Slot Modal --}}
    <div x-show="showBlockModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeBlockModal()"></div>

            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Block Time Slot</h3>
                </div>

                <form @submit.prevent="blockTimeSlot()">
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Room</label>
                            <select x-model="blockData.room_id" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500">
                                <option value="">Select a room</option>
                                @foreach($rooms as $room)
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
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Time</label>
                                <input type="time" x-model="blockData.start_time" required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Time</label>
                                <input type="time" x-model="blockData.end_time" required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason (Optional)</label>
                            <textarea x-model="blockData.reason" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500"
                                placeholder="e.g., Maintenance, Event, etc."></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 flex gap-3">
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
</div>

<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

<script>
function reservedRoomsApp() {
    return {
        calendar: null,
        selectedRoomFilter: '',
        showReservationModal: false,
        showBlockModal: false,
        currentReservation: null,
        editMode: false,
        saving: false,
        blocking: false,
        editData: {
            room_name: '',
            status: '',
            reservation_date: '',
            start_time: '',
            end_time: '',
            purpose: ''
        },
        blockData: {
            room_id: '',
            blocked_date: '',
            start_time: '',
            end_time: '',
            reason: ''
        },

        init() {
            this.initCalendar();
        },

        initCalendar() {
            const calendarEl = document.getElementById('admin-calendar');
            
            this.calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left: 'prev,next today blockTimeBtn',
                    center: 'title',
                    right: 'timeGridWeek,timeGridDay,dayGridMonth'
                },
                customButtons: {
                    blockTimeBtn: {
                        text: 'Block Time',
                        click: () => {
                            this.openBlockModal();
                        }
                    }
                },
                slotMinTime: '08:00:00',
                slotMaxTime: '17:00:00',
                slotDuration: '01:00:00',
                allDaySlot: false,
                height: 'auto',
                editable: false,
                eventClick: (info) => {
                    if (info.event.extendedProps.reservation_id) {
                        this.openReservationModal(info.event.extendedProps.reservation_id);
                    }
                },
                eventSources: [
                    {
                        url: '{{ route("admin.reserved-rooms.calendar-data") }}',
                        method: 'GET',
                        extraParams: () => ({
                            start_date: this.calendar.view.activeStart.toISOString().split('T')[0],
                            end_date: this.calendar.view.activeEnd.toISOString().split('T')[0],
                            room_id: this.selectedRoomFilter || undefined
                        }),
                        success: (response) => {
                            return this.transformCalendarData(response);
                        }
                    }
                ],
                eventClassNames: (arg) => {
                    const status = arg.event.extendedProps.status;
                    let bgColor = '';
                    
                    if (status === 'pending') {
                        bgColor = '!bg-yellow-500 !border-yellow-600';
                    } else if (status === 'approved') {
                        bgColor = '!bg-red-500 !border-red-600';
                    } else if (status === 'blocked') {
                        bgColor = '!bg-gray-400 !border-gray-500';
                    }
                    
                    return [bgColor, 'cursor-pointer'];
                }
            });

            this.calendar.render();
        },

        transformCalendarData(response) {
            const events = [];
            
            // Add reservations
            response.reservations.forEach(reservation => {
                events.push({
                    id: `res-${reservation.id}`,
                    title: `${reservation.room.name} - ${reservation.user.name}`,
                    start: `${reservation.reservation_date}T${reservation.start_time}`,
                    end: `${reservation.reservation_date}T${reservation.end_time}`,
                    extendedProps: {
                        reservation_id: reservation.id,
                        status: reservation.status
                    }
                });
            });
            
            // Add blocked slots
            response.blocked_slots.forEach(blocked => {
                events.push({
                    id: `blocked-${blocked.id}`,
                    title: `BLOCKED: ${blocked.room.name}`,
                    start: `${blocked.blocked_date}T${blocked.start_time}`,
                    end: `${blocked.blocked_date}T${blocked.end_time}`,
                    extendedProps: {
                        status: 'blocked',
                        blocked_id: blocked.id
                    }
                });
            });
            
            return events;
        },

        async openReservationModal(reservationId) {
            try {
                const response = await fetch(`/reserved-rooms/reservation/${reservationId}`);
                const data = await response.json();
                
                if (data.success) {
                    this.currentReservation = data.reservation;
                    this.editData = {
                        room_name: data.reservation.room.name,
                        status: data.reservation.status,
                        reservation_date: data.reservation.reservation_date,
                        start_time: data.reservation.start_time,
                        end_time: data.reservation.end_time,
                        purpose: data.reservation.purpose
                    };
                    this.showReservationModal = true;
                }
            } catch (error) {
                console.error('Error fetching reservation:', error);
            }
        },

        closeReservationModal() {
            this.showReservationModal = false;
            this.editMode = false;
            this.currentReservation = null;
        },

        enableEditMode() {
            this.editMode = true;
        },

        async saveReservation() {
            if (this.saving) return;
            this.saving = true;

            try {
                const response = await fetch(`/reserved-rooms/reservation/${this.currentReservation.id}/update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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
                        detail: { type: 'success', message: data.message }
                    }));
                    this.closeReservationModal();
                    this.calendar.refetchEvents();
                } else {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'error', message: data.message }
                    }));
                }
            } catch (error) {
                console.error('Error saving reservation:', error);
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { type: 'error', message: 'An error occurred while saving' }
                }));
            } finally {
                this.saving = false;
            }
        },

        async confirmDelete() {
            if (!confirm('Are you sure you want to delete this reservation?')) {
                return;
            }

            try {
                const response = await fetch(`/reserved-rooms/reservation/${this.currentReservation.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'success', message: data.message }
                    }));
                    this.closeReservationModal();
                    this.calendar.refetchEvents();
                } else {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'error', message: data.message }
                    }));
                }
            } catch (error) {
                console.error('Error deleting reservation:', error);
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { type: 'error', message: 'An error occurred while deleting' }
                }));
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
                const response = await fetch('{{ route("admin.reserved-rooms.block") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.blockData)
                });

                const data = await response.json();

                if (data.success) {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'success', message: data.message }
                    }));
                    this.closeBlockModal();
                    this.calendar.refetchEvents();
                } else {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'error', message: data.message }
                    }));
                }
            } catch (error) {
                console.error('Error blocking time slot:', error);
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { type: 'error', message: 'An error occurred while blocking the time slot' }
                }));
            } finally {
                this.blocking = false;
            }
        }
    }
}
</script>
