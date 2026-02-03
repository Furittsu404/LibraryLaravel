<div class="flex flex-col" x-data="{
    selectedUsers: [],
    selectAll: false,
    toggleSelectAll() {
        this.selectAll = !this.selectAll;
        if (this.selectAll) {
            this.selectedUsers = Array.from(document.querySelectorAll('[data-user-id]')).map(el => parseInt(el.dataset.userId));
        } else {
            this.selectedUsers = [];
        }
    },
    toggleUser(userId) {
        const index = this.selectedUsers.indexOf(userId);
        if (index > -1) {
            this.selectedUsers.splice(index, 1);
        } else {
            this.selectedUsers.push(userId);
        }
        this.selectAll = this.selectedUsers.length === document.querySelectorAll('[data-user-id]').length;
    },
    get hasSelected() {
        return this.selectedUsers.length > 0;
    },
    get selectedCount() {
        return this.selectedUsers.length;
    },
    clearSelection() {
        this.selectedUsers = [];
        this.selectAll = false;
    },
    removeArchivedFromSelection(archivedIds) {
        this.selectedUsers = this.selectedUsers.filter(id => !archivedIds.includes(id));
        this.selectAll = false;
    }
}"
    @bulk-action-completed.window="
    if ($event.detail && $event.detail.action === 'archive' && $event.detail.archivedIds) {
        removeArchivedFromSelection($event.detail.archivedIds);
    }
">
    <div class="flex justify-between items-center p-4">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Accounts Information</h1>
        <div class="flex flex-col items-end">
            <label for="search" class="sr-only">Search students</label>
            <input wire:model.live="search" type="text" id="searchAccounts" name="search"
                placeholder="Search students..."
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
            <div id="filterGender"
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
            <div id="filterStatus"
                class="flex flex-row items-center rounded-md border border-gray-300 dark:border-gray-600 w-40">
                <label for="status"
                    class="bg-gray-100 dark:bg-gray-700 rounded-l-md p-2 text-gray-700 dark:text-gray-300 font-medium">Status:</label>
                <select wire:model.live="status" id="status" name="status" x-on:change="$wire.resetPage()"
                    class="rounded-r-md p-2 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 border-0 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347] w-full">
                    <option value="">All</option>
                    <option value="inside">Inside</option>
                    <option value="outside">Outside</option>
                </select>
            </div>
            <div id="filterType"
                class="flex flex-row items-center rounded-md border border-gray-300 dark:border-gray-600 w-40">
                <label for="type"
                    class="bg-gray-100 dark:bg-gray-700 rounded-l-md p-2 text-gray-700 dark:text-gray-300 font-medium">Type:</label>
                <select wire:model.live="type" id="type" name="type" x-on:change="$wire.resetPage()"
                    class="rounded-r-md p-2 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 border-0 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347] w-full">
                    <option value="">All</option>
                    <option value="student">Student</option>
                    <option value="visitor">Visitor</option>
                    <option value="faculty">Faculty</option>
                    <option value="staff">Staff</option>
                </select>
            </div>
        </form>
        <div class="flex flex-row gap-4">
            <!-- More Options Dropdown -->
            <div class="relative" x-data="{ dropdownOpen: false }">
                <button id="moreOptions" @click="dropdownOpen = !dropdownOpen"
                    class="flex bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 p-2 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-500 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-md shadow-lg dark:shadow-gray-900 z-50 border border-gray-200 dark:border-gray-700"
                    style="display: none;">

                    <div class="py-1">
                        <!-- Download CSV -->
                        <button @click="$wire.downloadAllUsers(); dropdownOpen = false"
                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="w-5 h-5 mr-3 text-blue-600 dark:text-blue-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            <span>Download All User Info</span>
                        </button>

                        <!-- Divider -->
                        <div class="border-t border-gray-100 dark:border-gray-700"></div>

                        <!-- Change All to Inside -->
                        <button
                            @click="
                            dropdownOpen = false;
                            $nextTick(() => {
                                $dispatch('open-status-change-modal', {
                                    action: 'inside'
                                });
                            });
                        "
                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="w-5 h-5 mr-3 text-green-600 dark:text-green-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                            </svg>
                            <span>Change All to Inside</span>
                        </button>

                        <!-- Change All to Outside -->
                        <button
                            @click="
                            dropdownOpen = false;
                            $nextTick(() => {
                                $dispatch('open-status-change-modal', {
                                    action: 'outside'
                                });
                            });
                        "
                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="w-5 h-5 mr-3 text-blue-600 dark:text-blue-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>
                            <span>Change All to Outside</span>
                        </button>

                        <!-- Divider -->
                        <div class="border-t border-gray-100 dark:border-gray-700"></div>

                        <!-- Change All Expiration Dates -->
                        <button
                            @click="
                            dropdownOpen = false;
                            $nextTick(() => {
                                $dispatch('open-expiration-date-modal');
                            });
                        "
                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="w-5 h-5 mr-3 text-purple-600 dark:text-purple-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                            </svg>
                            <span>Change All Expiration Dates</span>
                        </button>
                    </div>
                </div>
            </div>

            <button id="importCSV" @click="$dispatch('open-import-modal')"
                class="flex flex-row bg-blue-500 dark:bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 dark:focus:ring-blue-500 cursor-pointer">
                <span class="hidden lg:inline">Import CSV</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6 ml-2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
            </button>
            <button id="registerUser" @click="$dispatch('open-create-modal')"
                class="flex flex-row bg-green-500 dark:bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-600 dark:hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400 dark:focus:ring-green-500 cursor-pointer">
                <span class="hidden lg:inline">Register User</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6 ml-0 lg:ml-2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                </svg>
            </button>
        </div>
    </div>
    <div id="accountsTableContainer" class="overflow-auto">
        <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-700">
                    <th
                        class="bg-gray-100 dark:bg-gray-700 py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                        <input type="checkbox" x-model="selectAll" @click="toggleSelectAll()"
                            class="w-4 h-4 text-[#009639] dark:text-[#00b347] bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-[#009639] dark:focus:ring-[#00b347] focus:ring-2">
                    </th>
                    <th
                        class="bg-gray-100 dark:bg-gray-700 py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                        #
                    </th>
                    <th
                        class="bg-gray-100 dark:bg-gray-700 py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                        ID
                    </th>
                    <th
                        class="bg-gray-100 dark:bg-gray-700 py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                        Name
                    </th>
                    <th
                        class="bg-gray-100 dark:bg-gray-700 py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                        Course
                    </th>
                    <th
                        class="bg-gray-100 dark:bg-gray-700 py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                        User Type
                    </th>
                    <th
                        class="bg-gray-100 dark:bg-gray-700 py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                        User Status</th>
                    <th
                        class="bg-gray-100 dark:bg-gray-700 py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                        Account Status</th>
                    <th
                        class="bg-gray-100 dark:bg-gray-700 py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                        Actions
                    </th>
                    <th
                        class="bg-gray-100 dark:bg-gray-700 py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                    </th>
                </tr>
            </thead>
            <tbody id="accountsTableBody">
                @forelse($users as $user)
                    <tr
                        class="hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-700 h-24">
                        <td class="py-3 px-4 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" :checked="selectedUsers.includes({{ $user->id }})"
                                @change="toggleUser({{ $user->id }})" data-user-id="{{ $user->id }}"
                                class="w-4 h-4 text-[#009639] dark:text-[#00b347] bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-[#009639] dark:focus:ring-[#00b347] focus:ring-2">
                        </td>
                        <td onclick="expandRow(this)" class="py-3 px-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                        </td>
                        <td onclick="expandRow(this)" class="py-3 px-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ $user->barcode }}
                        </td>
                        <td onclick="expandRow(this)" class="py-3 px-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ $user->lname . ', ' . $user->fname . ' ' . $user->mname }}</td>
                        <td onclick="expandRow(this)" class="py-3 px-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ $user->course . ' ' . $user->section }}</td>
                        <td onclick="expandRow(this)"
                            class="py-3 px-4 text-sm capitalize font-medium
                        {{ isset($user->user_type)
                            ? ($user->user_type === 'student'
                                ? 'text-green-700 dark:text-green-400'
                                : ($user->user_type === 'faculty'
                                    ? 'text-yellow-700 dark:text-yellow-400'
                                    : ($user->user_type === 'staff'
                                        ? 'text-blue-700 dark:text-blue-400'
                                        : 'text-purple-700 dark:text-purple-400')))
                            : 'text-gray-700 dark:text-gray-300' }}
                        ">
                            {{ $user->user_type ?? 'student' }}</td>
                        <td onclick="expandRow(this)" class="py-3 px-4 text-sm">
                            <span
                                class="px-2 py-1 rounded text-xs font-semibold {{ $user->user_status === 'inside' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200' }}">
                                {{ ucfirst($user->user_status) }}
                            </span>
                        </td>
                        <td onclick="expandRow(this)" class="py-3 px-4 text-sm">
                            <span
                                class="px-2 py-1 rounded text-xs font-semibold {{ $user->account_status === 'active' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200' }}">
                                {{ ucfirst($user->account_status) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-sm">
                            <div class="flex space-x-2">
                                <button
                                    @click="$dispatch('open-edit-modal', {
                                    id: {{ $user->id }}, lname: '{{ $user->lname }}',
                                    fname: '{{ $user->fname }}',
                                    mname: '{{ $user->mname }}',
                                    sex: '{{ $user->sex }}',
                                    email: '{{ $user->email }}',
                                    phonenumber: '{{ $user->phonenumber }}',
                                    address: '{{ $user->address }}',
                                    course: '{{ $user->course }}',
                                    section: '{{ $user->section }}',
                                    barcode: '{{ $user->barcode }}',
                                    user_type: '{{ $user->user_type }}',
                                    account_status: '{{ $user->account_status }}',
                                    user_status: '{{ $user->user_status }}'
                                })"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 cursor-pointer"
                                    title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                                <button
                                    @click="$dispatch('open-delete-modal', {
                                        id: {{ $user->id }},
                                        name: '{{ $user->lname }}, {{ $user->fname }}'
                                    })"
                                    class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 cursor-pointer"
                                    title="Archive">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                    </svg>
                                </button>
                                <button
                                    wire:click="updateUserStatus({{ $user->id }},'{{ $user->user_status === 'inside' ? 'outside' : 'inside' }}')"
                                    class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300 cursor-pointer"
                                    title="{{ $user->user_status === 'inside' ? 'Outside' : 'Inside' }}">
                                    @if ($user->user_status === 'inside')
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                    @endif
                                </button>
                            </div>
                        </td>
                        <td onclick="expandRow(this)" class="py-3 px-4 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="dropdownIcon size-6 transition-all duration-200 text-gray-700 dark:text-gray-300">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </td>
                    </tr>
                    <tr id="additionalInfo{{ $user->id }}"
                        class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 hidden">
                        <td colspan="10" class="py-3 px-4 text-gray-700 dark:text-gray-300">
                            <p class="mb-2 font-bold text-gray-900 dark:text-gray-100">Additional Info</p>
                            <p><span class="font-medium text-gray-900 dark:text-gray-200">Email:</span>
                                {{ $user->email }}</p>
                            <p><span class="font-medium text-gray-900 dark:text-gray-200">Sex:</span>
                                {{ $user->sex }}</p>
                            <p><span class="font-medium text-gray-900 dark:text-gray-200">Address:</span>
                                {{ $user->address }}</p>
                            <p><span class="font-medium text-gray-900 dark:text-gray-200">Phone:</span>
                                {{ $user->phonenumber }}</p>
                            <p><span class="font-medium text-gray-900 dark:text-gray-200">Expiration Date:</span>
                                {{ $user->expiration_date ? \Carbon\Carbon::parse($user->expiration_date)->format('M d, Y') : 'N/A' }}
                            </p>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="15" class="py-8 px-4 text-center text-gray-500 dark:text-gray-400">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="15" class="py-4 px-4 dark:bg-gray-800">
                        {{ $users->links('template.pagination', data: ['scrollTo' => false]) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Fixed Bottom Action Bar -->
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
                        <span x-text="selectedCount"></span> user<span x-show="selectedCount > 1">s</span> selected
                    </span>
                    <button @click="clearSelection()"
                        class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 underline">
                        Clear selection
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        @click="$dispatch('open-bulk-action-modal', {
                            action: 'inside',
                            count: selectedCount,
                            ids: selectedUsers
                        })"
                        class="flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                        </svg>
                        <span>Move Inside</span>
                    </button>
                    <button
                        @click="$dispatch('open-bulk-action-modal', {
                            action: 'outside',
                            count: selectedCount,
                            ids: selectedUsers
                        })"
                        class="flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                        <span>Move Outside</span>
                    </button>
                    <button
                        @click="$dispatch('open-bulk-action-modal', {
                            action: 'archive',
                            count: selectedCount,
                            ids: selectedUsers
                        })"
                        class="flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                        </svg>
                        <span>Archive Selected</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('components.accountsPage.importCSVModal')
    @include('components.accountsPage.createModal')
    @include('components.accountsPage.editModal')
    @include('components.accountsPage.deleteModal')
    @include('components.accountsPage.bulkActionModal')
    @include('components.accountsPage.statusChangeModal')
    @include('components.accountsPage.expirationDateModal')

    <script>
        function expandRow(cell) {
            const row = $(cell).closest("tr");
            const nextRow = row.next("tr");
            const icon = row.find(".dropdownIcon");
            if (nextRow.length) {
                if (nextRow.hasClass("hidden")) {
                    nextRow.removeClass("hidden");
                    icon.addClass("rotate-180");
                } else {
                    nextRow.addClass("hidden");
                    icon.removeClass("rotate-180");
                }
            }
        }
    </script>
</div>
