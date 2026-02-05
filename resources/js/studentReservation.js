// Student Reservation Alpine.js Component
window.reservationApp = function (rooms) {
    return {
        rooms: rooms,
        selectedRoom: null,
        currentStep: 1,
        barcode: "",
        purpose: "",
        submitting: false,

        // Calendar state
        currentMonth: null,
        currentYear: null,
        calendarDays: [],
        reservations: [],
        selectedDate: null,
        selectedStartTime: null,
        selectedEndTime: null,

        init() {
            console.log("Reservation app initialized", this.rooms);
            this.goToToday();
        },

        selectRoom(roomId) {
            this.selectedRoom = this.rooms.find((r) => r.id === roomId);
            console.log("Selected room:", this.selectedRoom);
            // Load reservations when room is selected
            if (this.selectedRoom) {
                this.loadReservations();
            }
        },

        get currentMonthDisplay() {
            if (
                this.currentMonth === null ||
                this.currentMonth === undefined ||
                !this.currentYear
            )
                return "";
            const date = new Date(this.currentYear, this.currentMonth);
            return date.toLocaleDateString("en-US", {
                month: "long",
                year: "numeric",
            });
        },

        goToToday() {
            const today = new Date();
            this.currentMonth = today.getMonth();
            this.currentYear = today.getFullYear();
            this.generateCalendarDays();
            if (this.selectedRoom) {
                this.loadReservations();
            }
        },

        previousMonth() {
            this.currentMonth--;
            if (this.currentMonth < 0) {
                this.currentMonth = 11;
                this.currentYear--;
            }
            this.generateCalendarDays();
            if (this.selectedRoom) {
                this.loadReservations();
            }
        },

        nextMonth() {
            this.currentMonth++;
            if (this.currentMonth > 11) {
                this.currentMonth = 0;
                this.currentYear++;
            }
            this.generateCalendarDays();
            if (this.selectedRoom) {
                this.loadReservations();
            }
        },

        generateCalendarDays() {
            this.calendarDays = [];

            const firstDay = new Date(this.currentYear, this.currentMonth, 1);
            const lastDay = new Date(
                this.currentYear,
                this.currentMonth + 1,
                0,
            );
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - startDate.getDay());

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            for (let i = 0; i < 42; i++) {
                const date = new Date(startDate);
                date.setDate(date.getDate() + i);

                const dayDate = new Date(date);
                dayDate.setHours(0, 0, 0, 0);

                // Format date as YYYY-MM-DD in local timezone
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, "0");
                const day = String(date.getDate()).padStart(2, "0");
                const localDateString = `${year}-${month}-${day}`;

                this.calendarDays.push({
                    date: localDateString,
                    dayNumber: date.getDate(),
                    isCurrentMonth: date.getMonth() === this.currentMonth,
                    isToday: dayDate.getTime() === today.getTime(),
                    isPast: dayDate <= today,
                });
            }
        },

        async loadReservations() {
            if (!this.selectedRoom) return;

            try {
                const firstDay = this.calendarDays[0].date;
                const lastDay =
                    this.calendarDays[this.calendarDays.length - 1].date;

                const response = await fetch(
                    `/reserved-rooms/calendar-data?start_date=${firstDay}&end_date=${lastDay}&room_id=${this.selectedRoom.id}`,
                );
                const data = await response.json();

                if (data.success) {
                    this.reservations = this.transformReservations(data);
                }
            } catch (error) {
                console.error("Error loading reservations:", error);
            }
        },

        transformReservations(data) {
            const all = [];
            const timeLabels = {
                "08:00": "8AM-10AM",
                "10:00": "10AM-12PM",
                "12:00": "12PM-2PM",
                "14:00": "2PM-4PM",
            };

            data.reservations.forEach((res) => {
                const startKey = res.start_time.substring(0, 5);
                const dateOnly = res.reservation_date.includes("T")
                    ? res.reservation_date.split("T")[0]
                    : res.reservation_date;

                all.push({
                    id: res.id,
                    type: "reservation",
                    status: res.status,
                    date: dateOnly,
                    start_time: res.start_time,
                    end_time: res.end_time,
                    time_display:
                        timeLabels[startKey] ||
                        `${startKey}-${res.end_time.substring(0, 5)}`,
                });
            });

            data.blocked_slots.forEach((blocked) => {
                const startKey = blocked.start_time.substring(0, 5);
                const dateOnly = blocked.blocked_date.includes("T")
                    ? blocked.blocked_date.split("T")[0]
                    : blocked.blocked_date;

                all.push({
                    id: `blocked-${blocked.id}`,
                    type: "blocked",
                    status: "blocked",
                    date: dateOnly,
                    start_time: blocked.start_time,
                    end_time: blocked.end_time,
                    time_display:
                        timeLabels[startKey] ||
                        `${startKey}-${blocked.end_time.substring(0, 5)}`,
                });
            });

            return all;
        },

        getAvailableSlotsForDay(date) {
            const dayReservations = this.reservations.filter(
                (res) => res.date === date,
            );
            const allSlots = [
                { start: "08:00", end: "10:00", label: "8AM-10AM" },
                { start: "10:00", end: "12:00", label: "10AM-12PM" },
                { start: "12:00", end: "14:00", label: "12PM-2PM" },
                { start: "14:00", end: "16:00", label: "2PM-4PM" },
            ];

            return allSlots.map((slot) => {
                const reserved = dayReservations.find(
                    (res) => res.start_time.substring(0, 5) === slot.start,
                );
                return {
                    ...slot,
                    isAvailable: !reserved,
                    status: reserved ? reserved.status : "available",
                };
            });
        },

        selectTimeSlot(date, slot) {
            if (
                !slot.isAvailable ||
                this.calendarDays.find((d) => d.date === date)?.isPast
            ) {
                return;
            }

            this.selectedDate = date;
            this.selectedStartTime = slot.start;
            this.selectedEndTime = slot.end;
            this.currentStep = 2;
        },

        async submitReservation() {
            if (this.submitting) return;
            this.submitting = true;

            try {
                const response = await fetch("/reservations/create", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]',
                        ).content,
                    },
                    body: JSON.stringify({
                        room_id: this.selectedRoom?.id,
                        barcode: this.barcode,
                        reservation_date: this.selectedDate,
                        start_time: this.selectedStartTime,
                        end_time: this.selectedEndTime,
                        purpose: this.purpose,
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    window.dispatchEvent(
                        new CustomEvent("show-toast", {
                            detail: {
                                type: "success",
                                message:
                                    data.message ||
                                    "Reservation submitted successfully!",
                            },
                        }),
                    );
                    // Reset form
                    this.currentStep = 1;
                    this.selectedRoom = null;
                    this.selectedDate = null;
                    this.selectedStartTime = null;
                    this.selectedEndTime = null;
                    this.barcode = "";
                    this.purpose = "";
                } else {
                    window.dispatchEvent(
                        new CustomEvent("show-toast", {
                            detail: {
                                type: "error",
                                message:
                                    data.message ||
                                    "Failed to submit reservation",
                            },
                        }),
                    );
                }
            } catch (error) {
                console.error("Error submitting reservation:", error);
                window.dispatchEvent(
                    new CustomEvent("show-toast", {
                        detail: {
                            type: "error",
                            message: "An error occurred. Please try again.",
                        },
                    }),
                );
            } finally {
                this.submitting = false;
            }
        },
    };
};
