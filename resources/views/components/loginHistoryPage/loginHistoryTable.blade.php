<div class="flex flex-col" x-data="{
    selectedLogins: [],
    selectAll: false,
    toggleSelectAll() {
        this.selectAll = !this.selectAll;
        if (this.selectAll) {
            this.selectedLogins = Array.from(document.querySelectorAll('[data-login-id]')).map(el => parseInt(el.dataset.loginId));
        } else {
            this.selectedLogins = [];
        }
    },
    toggleLogin(loginId) {
        const index = this.selectedLogins.indexOf(loginId);
        if (index > -1) {
            this.selectedLogins.splice(index, 1);
        } else {
            this.selectedLogins.push(loginId);
        }
        this.selectAll = this.selectedLogins.length === document.querySelectorAll('[data-login-id]').length;
    },
    get hasSelected() {
        return this.selectedLogins.length > 0;
    },
    get selectedCount() {
        return this.selectedLogins.length;
    },
    clearSelection() {
        this.selectedLogins = [];
        this.selectAll = false;
    }
}" @login-deleted.window="clearSelection()"
    @confirm-bulk-delete.window="
        $wire.bulkDelete(selectedLogins);
        clearSelection();
    ">

    <div class="flex justify-between items-center p-4">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Login History</h1>
        <div class="flex flex-col items-end">
            <label for="search" class="sr-only">Search login history</label>
            <input wire:model.live="search" type="text" id="searchLoginHistory" name="search"
                placeholder="Search by name, course, ID..."
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-3 pr-10 py-2 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347] w-80">
            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 mt-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 cursor-pointer absolute right-6"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
    </div>

    <div class="flex justify-between items-center px-4 pb-4">
        <form id="filter" class="grid grid-cols-2 gap-4 lg:gap-0 lg:flex items-center space-x-4 text-sm">
            <!-- Sex Filter -->
            <div id="filterSex"
                class="flex flex-row items-center rounded-md border border-gray-300 dark:border-gray-600 w-40">
                <label for="sex"
                    class="bg-gray-100 dark:bg-gray-700 rounded-l-md p-2 text-gray-700 dark:text-gray-300 font-medium">Sex:</label>
                <select wire:model.live="sex" id="sex" name="sex" x-on:change="$wire.resetPage()"
                    class="rounded-r-md p-2 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 border-0 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347] w-full">
                    <option value="">All</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>

            <!-- Course Filter -->
            <div id="filterCourse"
                class="flex flex-row items-center rounded-md border border-gray-300 dark:border-gray-600 w-40">
                <label for="course"
                    class="bg-gray-100 dark:bg-gray-700 rounded-l-md p-2 text-gray-700 dark:text-gray-300 font-medium">Course:</label>
                <select wire:model.live="course" id="course" name="course" x-on:change="$wire.resetPage()"
                    class="rounded-r-md p-2 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 border-0 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347] w-full">
                    <option value="">All</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course }}">{{ $course }}</option>
                    @endforeach
                </select>
            </div>

            <!-- User Type Filter -->
            <div id="filterType"
                class="flex flex-row items-center rounded-md border border-gray-300 dark:border-gray-600 w-40">
                <label for="userType"
                    class="bg-gray-100 dark:bg-gray-700 rounded-l-md p-2 text-gray-700 dark:text-gray-300 font-medium">Type:</label>
                <select wire:model.live="userType" id="userType" name="userType" x-on:change="$wire.resetPage()"
                    class="rounded-r-md p-2 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 border-0 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347] w-full">
                    <option value="">All</option>
                    <option value="student">Student</option>
                    <option value="visitor">Visitor</option>
                    <option value="faculty">Faculty</option>
                </select>
            </div>

            <!-- Start Date Filter -->
            <div id="filterStartDate"
                class="flex flex-row items-center rounded-md border border-gray-300 dark:border-gray-600 w-52">
                <label for="startDate"
                    class="bg-gray-100 dark:bg-gray-700 rounded-l-md p-2 text-gray-700 dark:text-gray-300 font-medium">From:</label>
                <input wire:model.live="startDate" type="date" id="startDate" name="startDate"
                    x-on:change="$wire.resetPage()"
                    class="rounded-r-md p-2 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 border-0 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347] w-full">
            </div>

            <!-- End Date Filter -->
            <div id="filterEndDate"
                class="flex flex-row items-center rounded-md border border-gray-300 dark:border-gray-600 w-52">
                <label for="endDate"
                    class="bg-gray-100 dark:bg-gray-700 rounded-l-md p-2 text-gray-700 dark:text-gray-300 font-medium">To:</label>
                <input wire:model.live="endDate" type="date" id="endDate" name="endDate"
                    x-on:change="$wire.resetPage()"
                    class="rounded-r-md p-2 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 border-0 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347] w-full">
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="p-4 text-left text-gray-700 dark:text-gray-300 font-semibold">
                        <input type="checkbox" :checked="selectAll" @change="toggleSelectAll()"
                            class="w-4 h-4 text-[#009639] bg-gray-100 border-gray-300 rounded focus:ring-[#009639] dark:focus:ring-[#00b347] dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600">
                    </th>
                    <th class="p-4 text-left text-gray-700 dark:text-gray-300 font-semibold">User ID</th>
                    <th class="p-4 text-left text-gray-700 dark:text-gray-300 font-semibold">Name</th>
                    <th class="p-4 text-left text-gray-700 dark:text-gray-300 font-semibold">Course</th>
                    <th class="p-4 text-left text-gray-700 dark:text-gray-300 font-semibold">Sex</th>
                    <th class="p-4 text-left text-gray-700 dark:text-gray-300 font-semibold">Login Time</th>
                    <th class="p-4 text-left text-gray-700 dark:text-gray-300 font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($loginHistory as $login)
                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        data-login-id="{{ $login->attendance_id }}">
                        <td class="p-4">
                            <input type="checkbox" :checked="selectedLogins.includes({{ $login->attendance_id }})"
                                @change="toggleLogin({{ $login->attendance_id }})"
                                class="w-4 h-4 text-[#009639] bg-gray-100 border-gray-300 rounded focus:ring-[#009639] dark:focus:ring-[#00b347] dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600">
                        </td>
                        <td class="p-4 text-gray-800 dark:text-gray-200">{{ $login->user_id }}</td>
                        <td class="p-4 text-gray-800 dark:text-gray-200">
                            {{ $login->lname }}, {{ $login->fname }} {{ $login->mname }}
                        </td>
                        <td class="p-4 text-gray-800 dark:text-gray-200">{{ $login->course }}</td>
                        <td class="p-4">
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $login->sex === 'male' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                {{ $login->sex === 'female' ? 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200' : '' }}
                                {{ $login->sex === 'other' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                {{ $login->sex === 'unknown' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' : '' }}">
                                {{ ucfirst($login->sex) }}
                            </span>
                        </td>
                        <td class="p-4 text-gray-800 dark:text-gray-200">
                            {{ \Carbon\Carbon::parse($login->login_time)->format('M d, Y h:i A') }}
                        </td>
                        <td class="p-4">
                            <button
                                @click="$dispatch('open-delete-login-modal', {
                                    id: {{ $login->attendance_id }},
                                    name: '{{ $login->lname }}, {{ $login->fname }} {{ $login->mname }}',
                                    time: '{{ \Carbon\Carbon::parse($login->login_time)->format('M d, Y h:i A') }}'
                                })"
                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                                title="Delete login record">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mb-4 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <p class="text-lg font-medium">No login records found</p>
                                <p class="text-sm mt-1">Try adjusting your search or filters</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="p-4 border-t border-gray-300 dark:border-gray-700">
        {{ $loginHistory->links('template.pagination', data: ['scrollTo' => false]) }}
    </div>

    <!-- Fixed Bottom-Right Action Bar -->
    <div x-show="hasSelected" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-full opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-full opacity-0"
        class="fixed rounded-xl bottom-2 right-10 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 shadow-2xl z-50"
        style="display: none;">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex gap-4 items-center justify-between">
                <div class="flex items-center gap-4">
                    <span class="text-gray-700 dark:text-gray-300 font-medium">
                        <span x-text="selectedCount"></span> login<span x-show="selectedCount > 1">s</span> selected
                    </span>
                    <button @click="clearSelection()"
                        class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 underline">
                        Clear selection
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <button @click="$dispatch('open-bulk-delete-modal', { count: selectedCount })"
                        class="flex items-center gap-2 px-4 py-2 bg-red-600 dark:bg-red-500 text-white rounded-md hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 dark:focus:ring-red-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                        <span>Delete Selected</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    @include('components.loginHistoryPage.deleteModal')
    @include('components.loginHistoryPage.bulkDeleteModal')
</div>
