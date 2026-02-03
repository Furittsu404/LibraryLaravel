<div class="flex flex-col p-4 space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Login Reports</h1>
    </div>

    <!-- Filters Section -->
    <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-md dark:shadow-gray-900 p-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Report Filters</h2>

        <form wire:submit.prevent="applyFilters" class="space-y-4" x-data="{
            startTimeCustom: false,
            endTimeCustom: false,
            customStartTime: @entangle('startTime').live,
            customEndTime: @entangle('endTime').live,
            validateDateInput(event) {
                const input = event.target;
                const dateValue = input.value;
        
                if (dateValue) {
                    // Parse the date string (YYYY-MM-DD format)
                    const parts = dateValue.split('-');
                    if (parts.length !== 3) {
                        input.setCustomValidity('Invalid date format. Use YYYY-MM-DD.');
                        input.reportValidity();
                        return false;
                    }
        
                    const year = parseInt(parts[0], 10);
                    const month = parseInt(parts[1], 10);
                    const day = parseInt(parts[2], 10);
        
                    // Create date without timezone issues (using UTC)
                    const date = new Date(Date.UTC(year, month - 1, day));
        
                    // Check if date is valid
                    if (isNaN(date.getTime())) {
                        input.setCustomValidity('Invalid date. Please enter a valid date.');
                        input.reportValidity();
                        return false;
                    }
        
                    // Verify the date components match (catches invalid dates like Feb 31)
                    if (date.getUTCFullYear() !== year ||
                        date.getUTCMonth() !== month - 1 ||
                        date.getUTCDate() !== day) {
                        input.setCustomValidity('Invalid date. This date does not exist in the calendar.');
                        input.reportValidity();
                        return false;
                    }
        
                    input.setCustomValidity('');
                }
                return true;
            }
        }">
            <!-- Date Range -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">
                        Start Date & Time <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="date" wire:model.live="startDate" required @change="validateDateInput($event)"
                            class="flex-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">

                        <!-- Start Time Dropdown with Custom -->
                        <div class="relative w-36">
                            <select x-show="!startTimeCustom" wire:model.live="startTime"
                                @change="if ($event.target.value === 'custom') { startTimeCustom = true; }"
                                class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">
                                <option value="05:00">5:00 AM</option>
                                <option value="06:00">6:00 AM</option>
                                <option value="07:00">7:00 AM</option>
                                <option value="08:00">8:00 AM</option>
                                <option value="09:00">9:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="13:00">1:00 PM</option>
                                <option value="14:00">2:00 PM</option>
                                <option value="15:00">3:00 PM</option>
                                <option value="16:00">4:00 PM</option>
                                <option value="17:00">5:00 PM</option>
                                <option value="18:00">6:00 PM</option>
                                <option value="19:00">7:00 PM</option>
                                <option value="20:00">8:00 PM</option>
                                <option value="21:00">9:00 PM</option>
                                <option value="custom">Custom...</option>
                            </select>
                            <div x-show="startTimeCustom" class="flex gap-1">
                                <input type="time" x-model="customStartTime" required
                                    class="flex-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">
                                <button type="button" @click="startTimeCustom = false"
                                    class="px-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                                    ✕
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">
                        End Date & Time <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="date" wire:model.live="endDate" required @change="validateDateInput($event)"
                            class="flex-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">

                        <!-- End Time Dropdown with Custom -->
                        <div class="relative w-36">
                            <select x-show="!endTimeCustom" wire:model.live="endTime"
                                @change="if ($event.target.value === 'custom') { endTimeCustom = true; }"
                                class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">
                                <option value="05:00">5:00 AM</option>
                                <option value="06:00">6:00 AM</option>
                                <option value="07:00">7:00 AM</option>
                                <option value="08:00">8:00 AM</option>
                                <option value="09:00">9:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="13:00">1:00 PM</option>
                                <option value="14:00">2:00 PM</option>
                                <option value="15:00">3:00 PM</option>
                                <option value="16:00">4:00 PM</option>
                                <option value="17:00">5:00 PM</option>
                                <option value="18:00">6:00 PM</option>
                                <option value="19:00">7:00 PM</option>
                                <option value="20:00">8:00 PM</option>
                                <option value="21:00">9:00 PM</option>
                                <option value="custom">Custom...</option>
                            </select>
                            <div x-show="endTimeCustom" class="flex gap-1">
                                <input type="time" x-model="customEndTime" required
                                    class="flex-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">
                                <button type="button" @click="endTimeCustom = false"
                                    class="px-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                                    ✕
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Type -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">User Type</label>
                    <select wire:model.live="userType"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">
                        <option value="">All Types</option>
                        <option value="student">Student</option>
                        <option value="faculty">Faculty</option>
                        <option value="visitor">Visitor</option>
                    </select>
                </div>
            </div>

            <!-- Course Selection with Modal -->
            <!-- Courses, Sex, and User Type Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Course Selection -->
                <div class="lg:col-span-1" x-data="{ showCourseDropdown: false }">
                    <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">
                        Courses
                    </label>

                    <!-- Dropdown Container -->
                    <div class="relative">
                        <button type="button" @click="showCourseDropdown = !showCourseDropdown"
                            class="w-full min-h-[42px] border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded px-3 py-2 flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">
                            <div class="flex flex-wrap gap-2 flex-1 pointer-events-none">
                                @if (count($selectedCourses) > 0)
                                    <span class="text-gray-700 dark:text-gray-300 text-sm">
                                        {{ count($selectedCourses) }}
                                        {{ count($selectedCourses) === 1 ? 'course' : 'courses' }} selected
                                    </span>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400 text-sm">All courses</span>
                                @endif
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 ml-2 text-gray-500 dark:text-gray-400 transition-transform pointer-events-none"
                                :class="{ 'rotate-180': showCourseDropdown }" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Selected Courses Pills (Outside Dropdown Button) -->
                        @if (count($selectedCourses) > 0)
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($selectedCourses as $course)
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 bg-[#009639] dark:bg-[#00b347] text-white rounded-full text-xs">
                                        {{ $course }}
                                        <button type="button" wire:click="removeCourse('{{ $course }}')"
                                            class="hover:bg-white/20 rounded-full p-0.5 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <!-- Dropdown Menu -->
                        <div x-show="showCourseDropdown" @click.away="showCourseDropdown = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-10 mt-2 w-full bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-lg shadow-lg"
                            style="display: none;">

                            <!-- Course Selection Grid -->
                            <div class="p-3 max-h-80 overflow-y-auto">
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach ($availableCourses as $course)
                                        <label class="relative cursor-pointer group">
                                            <input type="checkbox" wire:model.live="selectedCourses"
                                                value="{{ $course }}" class="sr-only peer">
                                            <div
                                                class="px-3 py-2 text-center text-sm rounded-lg border-2 border-gray-300 dark:border-gray-600
                                                bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                                peer-checked:border-[#009639] peer-checked:dark:border-[#00b347]
                                                peer-checked:text-[#009639] peer-checked:dark:text-[#00b347]
                                                peer-checked:font-semibold
                                                hover:border-gray-400 dark:hover:border-gray-500
                                                transition-all duration-200">
                                                {{ $course }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Clear Button -->
                            <div
                                class="px-3 py-2 border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 rounded-b-lg">
                                <button type="button" wire:click="$set('selectedCourses', [])"
                                    @click="showCourseDropdown = false"
                                    class="w-full px-4 py-2 bg-gray-500 dark:bg-gray-600 text-white rounded-md hover:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-500 text-sm font-medium">
                                    Clear All
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sex -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Sex</label>
                    <select wire:model.live="sex"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">
                        <option value="">All Sexes</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 justify-end">
                <button type="button" wire:click="resetFilters"
                    class="px-6 py-2 bg-gray-500 dark:bg-gray-600 text-white rounded-md hover:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-500">
                    Reset Filters
                </button>
                <button type="submit"
                    class="px-6 py-2 bg-[#009639] dark:bg-[#00b347] text-white rounded-md hover:bg-[#007a2e] dark:hover:bg-[#009639] focus:outline-none focus:ring-2 focus:ring-green-400 dark:focus:ring-green-500">
                    Generate Report
                </button>
            </div>
        </form>
    </div>

    @if ($hasGeneratedReport)
        <!-- Statistics Cards Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Card 1: Top 10 Users by Logins -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-md dark:shadow-gray-900 p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600 dark:text-blue-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Top 10 Users by Logins
                </h3>
                <div class="space-y-3 max-h-128 overflow-y-auto custom-scrollbar">
                    @forelse($topUsers as $index => $user)
                        <div
                            class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <span
                                    class="flex items-center justify-center w-8 h-8 rounded-full {{ $index < 3 ? 'bg-yellow-400 text-yellow-900' : 'bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300' }} font-bold text-sm">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $user['name'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user['course'] }}</p>
                                </div>
                            </div>
                            <span
                                class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-sm font-semibold">
                                {{ $user['login_count'] }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">No data available</p>
                    @endforelse
                </div>
            </div>

            <!-- Card 2: Total Statistics of Logins -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-md dark:shadow-gray-900 p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-green-600 dark:text-green-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Total Statistics
                </h3>
                <div class="space-y-4">
                    <!-- Total Logins Section -->
                    <div class="space-y-3 pb-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="p-3 rounded-lg border border-green-200 dark:border-green-800">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Logins</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ number_format($totalStatistics['total_logins']) }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 rounded-lg border border-pink-200 dark:border-pink-800">
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Female</p>
                                <p class="text-xl font-bold text-pink-600 dark:text-pink-400">
                                    {{ number_format($totalStatistics['total_female_logins']) }}</p>
                            </div>
                            <div class="p-3 rounded-lg border border-blue-200 dark:border-blue-800">
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Male</p>
                                <p class="text-xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ number_format($totalStatistics['total_male_logins']) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Unique Logins Section -->
                    <div class="space-y-3 pt-2">
                        <div class="p-3 rounded-lg border border-green-200 dark:border-green-800">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Unique Logins</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ number_format($totalStatistics['unique_logins']) }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 rounded-lg border border-pink-200 dark:border-pink-800">
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Female</p>
                                <p class="text-xl font-bold text-pink-600 dark:text-pink-400">
                                    {{ number_format($totalStatistics['unique_female_logins']) }}</p>
                            </div>
                            <div class="p-3 rounded-lg border border-blue-200 dark:border-blue-800">
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Male</p>
                                <p class="text-xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ number_format($totalStatistics['unique_male_logins']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Types of Users -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-md dark:shadow-gray-900 p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-yellow-600 dark:text-yellow-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    User Type Statistics
                </h3>
                <div class="space-y-4">
                    <!-- Student -->
                    <div class="p-4 rounded-lg border border-green-200 dark:border-green-800">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-semibold text-green-700 dark:text-green-400 text-base">Students</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            </svg>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 mb-1">Total Student Logins</p>
                                <p class="text-lg font-bold text-green-700 dark:text-green-400">
                                    {{ number_format($userTypeStatistics['student']['total']) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 mb-1">Unique Students</p>
                                <p class="text-lg font-bold text-green-700 dark:text-green-400">
                                    {{ number_format($userTypeStatistics['student']['unique']) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Faculty -->
                    <div class="p-4 rounded-lg border border-yellow-200 dark:border-yellow-800">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-semibold text-yellow-700 dark:text-yellow-400 text-base">Faculty
                                Members</span>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 mb-1">Total Faculty Logins</p>
                                <p class="text-lg font-bold text-yellow-700 dark:text-yellow-400">
                                    {{ number_format($userTypeStatistics['faculty']['total']) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 mb-1">Unique Faculty</p>
                                <p class="text-lg font-bold text-yellow-700 dark:text-yellow-400">
                                    {{ number_format($userTypeStatistics['faculty']['unique']) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Visitor -->
                    <div class="p-4 rounded-lg border border-purple-200 dark:border-purple-800">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-semibold text-purple-700 dark:text-purple-400 text-base">Visitors</span>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 mb-1">Total Visitor Logins</p>
                                <p class="text-lg font-bold text-purple-700 dark:text-purple-400">
                                    {{ number_format($userTypeStatistics['visitor']['total']) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 mb-1">Unique Visitors</p>
                                <p class="text-lg font-bold text-purple-700 dark:text-purple-400">
                                    {{ number_format($userTypeStatistics['visitor']['unique']) }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Staff -->
                    <div class="p-4 rounded-lg border border-teal-200 dark:border-teal-800">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-semibold text-teal-700 dark:text-teal-400 text-base">Staff</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-600 dark:text-teal-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l9-5-9-5-9 5 9 5zM12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            </svg>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 mb-1">Total Staff Logins</p>
                                <p class="text-lg font-bold text-teal-700 dark:text-teal-400">
                                    {{ number_format($userTypeStatistics['staff']['total'] ?? 0) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 mb-1">Unique Staff</p>
                                <p class="text-lg font-bold text-teal-700 dark:text-teal-400">
                                    {{ number_format($userTypeStatistics['staff']['unique'] ?? 0) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Buttons -->
        <div class="flex justify-end gap-3">
            <button wire:click="exportReport"
                class="px-6 py-2 bg-gray-600 dark:bg-gray-700 text-white rounded-md hover:bg-gray-700 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-500 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Raw Data
            </button>

            <button wire:click="printPdf"
                class="px-6 py-2 bg-gray-600 dark:bg-gray-700 text-white rounded-md hover:bg-gray-700 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-500 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print PDF
            </button>

            <button wire:click="exportStatisticsWithAttendance"
                class="px-6 py-2 bg-green-600 dark:bg-green-500 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 dark:focus:ring-green-500 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Reports (PDF + Excel)
            </button>
        </div>

        <!-- Data Table -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md dark:shadow-gray-900 border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Login Records
                    ({{ $paginatedReportData->total() }} records)</h3>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 dark:text-gray-400">Show:</label>
                    <select wire:model.live="perPage"
                        class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-sm text-gray-600 dark:text-gray-400">per page</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">#</th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Name
                            </th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Course
                            </th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Sex
                            </th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">User
                                Type</th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Login
                                Time</th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Logout
                                Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($paginatedReportData as $index => $record)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="py-3 px-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ ($paginatedReportData->currentPage() - 1) * $paginatedReportData->perPage() + $index + 1 }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $record->lname }}, {{ $record->fname }} {{ $record->mname }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-700 dark:text-gray-300">{{ $record->course }}
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    <span
                                        class="px-2 py-1 rounded text-xs font-semibold capitalize
                                        {{ $record->sex === 'female' ? 'bg-pink-100 dark:bg-pink-900 text-pink-800 dark:text-pink-200' : 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' }}">
                                        {{ $record->sex }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    <span
                                        class="px-2 py-1 rounded text-xs font-semibold capitalize
                                        {{ $record->user_type === 'student'
                                            ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200'
                                            : ($record->user_type === 'faculty'
                                                ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200'
                                                : ($record->user_type === 'staff'
                                                    ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200'
                                                    : 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200')) }}">
                                        {{ $record->user_type }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ \Carbon\Carbon::parse($record->login_time)->format('M d, Y h:i A') }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $record->logout_time ? \Carbon\Carbon::parse($record->logout_time)->format('M d, Y h:i A') : 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 px-4 text-center text-gray-500 dark:text-gray-400">
                                    No records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($paginatedReportData->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $paginatedReportData->links('template.pagination', data: ['scrollTo' => false]) }}
                </div>
            @endif
        </div>
    @else
        <!-- Empty State -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md dark:shadow-gray-900 p-12 border border-gray-200 dark:border-gray-700 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-gray-400 dark:text-gray-500 mb-4"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">No Report Generated</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Select your filters above and click "Generate Report" to
                view statistics and data.</p>
        </div>
    @endif
</div>

<script>
    (function() {
        // Dedup guard: remember last opened url + timestamp
        let _lastOpened = {
            url: null,
            ts: 0
        };

        function handleOpenPrintWindow(event) {
            // event may be an object { url } (Livewire.on) or a string, or a DOM CustomEvent with detail
            let url = null;
            if (!event) return;
            if (typeof event === 'string') url = event;
            else if (event.url) url = event.url;
            else if (event.detail) {
                // browser CustomEvent from dispatchBrowserEvent carries payload in detail
                if (typeof event.detail === 'string') url = event.detail;
                else if (event.detail.url) url = event.detail.url;
            } else {
                url = event;
            }

            if (!url) return;

            // simple dedupe: ignore duplicate open requests within 1s
            const now = Date.now();
            if (_lastOpened.url === url && (now - _lastOpened.ts) < 1000) return;
            _lastOpened = {
                url,
                ts: now
            };

            window.open(url, 'PrintWindow', 'width=900,height=650,scrollbars=yes,resizable=yes');
        }

        // Always register a DOM-level listener for browser events dispatched via dispatchBrowserEvent
        document.addEventListener('open-print-window', function(e) {
            try {
                handleOpenPrintWindow(e);
            } catch (err) {
                console.error(err);
            }
        });

        // Register Livewire listener if available, or register when Livewire loads
        function registerLivewireListener() {
            try {
                if (window.Livewire && typeof Livewire.on === 'function') {
                    Livewire.on('open-print-window', function(payload) {
                        try {
                            handleOpenPrintWindow(payload);
                        } catch (err) {
                            console.error(err);
                        }
                    });
                }
            } catch (err) {
                // swallow
            }
        }

        // Attempt immediate registration and also on livewire:load to cover timing cases
        registerLivewireListener();
        document.addEventListener('livewire:load', registerLivewireListener);
    })();
</script>
