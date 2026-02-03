<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reserve a Room - CLSU Library</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
</head>

<body class="bg-gray-50 min-h-screen">
    <div x-data="reservationApp()" x-init="init()">
        <!-- Header -->
        <header class="bg-green-600 text-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('scanner.index') }}" class="text-white hover:text-green-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold">Room Reservation</h1>
                            <p class="text-green-100 text-sm">CLSU Library Information Services Office</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm opacity-90">Current Date</p>
                        <p class="font-semibold" x-text="new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })"></p>
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
                            <div class="flex items-center justify-center w-10 h-10 rounded-full transition-colors"
                                :class="currentStep === 1 ? 'bg-green-600 text-white' : 'bg-green-200 text-green-800'">
                                <span class="font-semibold">1</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Select Date & Time</p>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="w-24 h-1 mx-4 rounded transition-colors"
                            :class="currentStep === 2 ? 'bg-green-600' : 'bg-gray-300'"></div>

                        <!-- Step 2 -->
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full transition-colors"
                                :class="currentStep === 2 ? 'bg-green-600 text-white' : 'bg-gray-300 text-gray-600'">
                                <span class="font-semibold">2</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Enter Details</p>
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
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Select a Room</h3>
                        <div class="space-y-3">
                            @foreach($rooms as $room)
                            <button @click="selectRoom({{ $room->id }})"
                                :class="selectedRoom?.id === {{ $room->id }} ? 'bg-green-50 border-green-500 ring-2 ring-green-500' : 'bg-gray-50 border-gray-200 hover:bg-gray-100'"
                                class="w-full text-left p-4 rounded-lg border-2 transition-all">
                                <p class="font-semibold text-gray-900">{{ $room->name }}</p>
                                <p class="text-sm text-gray-600 mt-1">{{ $room->description }}</p>
                                <div class="flex items-center gap-2 mt-2 text-xs text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                    <span>Capacity: {{ $room->capacity }} people</span>
                                </div>
                            </button>
                            @endforeach
                        </div>

                        <!-- Legend -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Legend</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 bg-green-500 rounded"></div>
                                    <span class="text-gray-700">Available</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                                    <span class="text-gray-700">Pending</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 bg-red-500 rounded"></div>
                                    <span class="text-gray-700">Reserved</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 bg-gray-400 rounded"></div>
                                    <span class="text-gray-700">Blocked</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div x-show="!selectedRoom" class="text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto text-gray-400 mb-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                            <p class="text-gray-600 font-medium">Please select a room to view available time slots</p>
                        </div>

                        <div x-show="selectedRoom" id="calendar"></div>
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
                
                <div class="max-w-3xl mx-auto">
                    <div class="bg-white rounded-lg shadow-md p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Complete Your Reservation</h2>

                        <form @submit.prevent="submitReservation()">
                            <!-- Selected Details (Read-only) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                                    <input type="text" :value="selectedRoom?.name" disabled
                                        class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Reservation Date</label>
                                    <input type="text" :value="selectedDate" disabled
                                        class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                    <input type="text" :value="selectedStartTime" disabled
                                        class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                                    <input type="text" :value="selectedEndTime" disabled
                                        class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 cursor-not-allowed">
                                </div>
                            </div>

                            <!-- User Input Fields -->
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Student ID <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" x-model="userId" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <p class="text-xs text-gray-500 mt-1">Enter your student ID number</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Purpose of Reservation <span class="text-red-500">*</span>
                                    </label>
                                    <textarea x-model="purpose" rows="4" required maxlength="500"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        placeholder="Please describe the purpose of your reservation..."></textarea>
                                    <p class="text-xs text-gray-500 mt-1" x-text="`${purpose.length}/500 characters`"></p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-4 mt-8">
                                <button type="button" @click="currentStep = 1"
                                    class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                                    <span class="flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                                        </svg>
                                        Back
                                    </span>
                                </button>
                                <button type="submit" :disabled="submitting"
                                    :class="submitting ? 'opacity-50 cursor-not-allowed' : ''"
                                    class="flex-1 px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors">
                                    <span x-show="!submitting">Submit Reservation</span>
                                    <span x-show="submitting">Submitting...</span>
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

    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

    <script>
        function reservationApp() {
            return {
                currentStep: 1,
                rooms: @json($rooms),
                selectedRoom: null,
                selectedDate: null,
                selectedStartTime: null,
                selectedEndTime: null,
                userId: '',
                purpose: '',
                submitting: false,
                calendar: null,

                init() {
                    // Initialize will happen when room is selected
                },

                async selectRoom(roomId) {
                    this.selectedRoom = this.rooms.find(r => r.id === roomId);
                    
                    // Wait for next tick to ensure DOM is updated
                    await this.$nextTick();
                    
                    // Initialize or refresh calendar
                    if (this.calendar) {
                        this.calendar.destroy();
                    }
                    
                    this.initCalendar();
                },

                initCalendar() {
                    const calendarEl = document.getElementById('calendar');
                    if (!calendarEl) return;

                    this.calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'timeGridWeek',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'timeGridWeek,timeGridDay'
                        },
                        slotMinTime: '08:00:00',
                        slotMaxTime: '17:00:00',
                        slotDuration: '01:00:00',
                        allDaySlot: false,
                        height: 'auto',
                        selectable: true,
                        selectMirror: true,
                        selectConstraint: {
                            start: '08:00',
                            end: '17:00',
                        },
                        validRange: {
                            start: new Date(),
                            end: new Date(new Date().setDate(new Date().getDate() + 14))
                        },
                        select: (info) => {
                            this.handleTimeSlotSelect(info);
                        },
                        eventSources: [
                            {
                                url: '{{ route("student.reservations.slots") }}',
                                method: 'GET',
                                extraParams: () => ({
                                    room_id: this.selectedRoom?.id,
                                    start_date: this.calendar.view.activeStart.toISOString().split('T')[0],
                                    end_date: this.calendar.view.activeEnd.toISOString().split('T')[0]
                                }),
                                success: (response) => {
                                    return this.transformSlotData(response.slots);
                                },
                                failure: () => {
                                    console.error('Failed to load time slots');
                                }
                            }
                        ],
                        eventClassNames: (arg) => {
                            const status = arg.event.extendedProps.status;
                            const classes = ['border-0'];
                            
                            if (status === 'available') {
                                classes.push('!bg-green-500');
                            } else if (status === 'pending') {
                                classes.push('!bg-yellow-500');
                            } else if (status === 'reserved') {
                                classes.push('!bg-red-500');
                            } else if (status === 'blocked') {
                                classes.push('!bg-gray-400');
                            }
                            
                            return classes;
                        },
                        eventClick: (info) => {
                            if (info.event.extendedProps.status === 'available') {
                                this.handleTimeSlotSelect({
                                    start: info.event.start,
                                    end: info.event.end
                                });
                            }
                        }
                    });

                    this.calendar.render();
                },

                transformSlotData(slots) {
                    const events = [];
                    
                    for (const [date, timeSlots] of Object.entries(slots)) {
                        timeSlots.forEach(slot => {
                            events.push({
                                start: `${date}T${slot.start_time}`,
                                end: `${date}T${slot.end_time}`,
                                title: slot.status.charAt(0).toUpperCase() + slot.status.slice(1),
                                extendedProps: {
                                    status: slot.status
                                },
                                display: 'background'
                            });
                        });
                    }
                    
                    return events;
                },

                handleTimeSlotSelect(info) {
                    const status = this.getSlotStatus(info.start, info.end);
                    
                    if (status !== 'available') {
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: { 
                                type: 'error', 
                                message: 'This time slot is not available. Please select an available slot (green).' 
                            }
                        }));
                        return;
                    }

                    // Format the selected information
                    this.selectedDate = info.start.toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                    this.selectedStartTime = info.start.toLocaleTimeString('en-US', { 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    });
                    this.selectedEndTime = info.end.toLocaleTimeString('en-US', { 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    });

                    // Move to step 2
                    this.currentStep = 2;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                getSlotStatus(start, end) {
                    const events = this.calendar.getEvents();
                    for (const event of events) {
                        if (event.start <= start && event.end >= end) {
                            return event.extendedProps.status || 'available';
                        }
                    }
                    return 'available';
                },

                async submitReservation() {
                    if (this.submitting) return;
                    this.submitting = true;

                    try {
                        // Parse the date and time properly
                        const dateObj = new Date(this.selectedDate);
                        const reservationDate = dateObj.toISOString().split('T')[0];
                        
                        // Convert 12-hour time to 24-hour format
                        const startTime24 = this.convertTo24Hour(this.selectedStartTime);
                        const endTime24 = this.convertTo24Hour(this.selectedEndTime);

                        const response = await fetch('{{ route("student.reservations.create") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                room_id: this.selectedRoom.id,
                                user_id: this.userId,
                                reservation_date: reservationDate,
                                start_time: startTime24,
                                end_time: endTime24,
                                purpose: this.purpose
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            window.dispatchEvent(new CustomEvent('show-toast', {
                                detail: { type: 'success', message: data.message }
                            }));

                            // Reset form and go back to step 1
                            setTimeout(() => {
                                this.resetForm();
                                this.currentStep = 1;
                                if (this.calendar) {
                                    this.calendar.refetchEvents();
                                }
                            }, 2000);
                        } else {
                            window.dispatchEvent(new CustomEvent('show-toast', {
                                detail: { type: 'error', message: data.message }
                            }));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: { type: 'error', message: 'An error occurred. Please try again.' }
                        }));
                    } finally {
                        this.submitting = false;
                    }
                },

                convertTo24Hour(time12h) {
                    const [time, modifier] = time12h.split(' ');
                    let [hours, minutes] = time.split(':');
                    
                    if (hours === '12') {
                        hours = '00';
                    }
                    
                    if (modifier === 'PM') {
                        hours = parseInt(hours, 10) + 12;
                    }
                    
                    return `${String(hours).padStart(2, '0')}:${minutes}`;
                },

                resetForm() {
                    this.userId = '';
                    this.purpose = '';
                    this.selectedDate = null;
                    this.selectedStartTime = null;
                    this.selectedEndTime = null;
                }
            }
        }
    </script>
</body>

</html>
