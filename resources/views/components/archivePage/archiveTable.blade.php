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
    removeActivatedFromSelection(activatedIds) {
        this.selectedUsers = this.selectedUsers.filter(id => !activatedIds.includes(id));
        this.selectAll = false;
    }
}"
    @bulk-action-completed.window="
    if ($event.detail && $event.detail.action === 'activate' && $event.detail.activatedIds) {
        removeActivatedFromSelection($event.detail.activatedIds);
    }
"
    @user-activated.window="clearSelection()" @user-deleted.window="clearSelection()">
    <div class="flex justify-between items-center p-4">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Archived Accounts</h1>
        <div class="flex flex-col items-end">
            <label for="search" class="sr-only">Search archived accounts</label>
            <input wire:model.live="search" type="text" id="searchArchive" name="search"
                placeholder="Search archived accounts..."
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
    </div>

    <div id="archiveTableContainer" class="overflow-auto">
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
                        Archived At
                    </th>
                    <th
                        class="bg-gray-100 dark:bg-gray-700 py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody id="archiveTableBody">
                @forelse($users as $user)
                    <tr
                        class="hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-700 h-24">
                        <td class="py-3 px-4 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" :checked="selectedUsers.includes({{ $user->id }})"
                                @change="toggleUser({{ $user->id }})" data-user-id="{{ $user->id }}"
                                class="w-4 h-4 text-[#009639] dark:text-[#00b347] bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-[#009639] dark:focus:ring-[#00b347] focus:ring-2">
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ $user->barcode }}
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ $user->lname . ', ' . $user->fname . ' ' . $user->mname }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ $user->course . ' ' . $user->section }}</td>
                        <td
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
                        <td class="py-3 px-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ $user->archived_at ? \Carbon\Carbon::parse($user->archived_at)->format('M d, Y H:i') : 'N/A' }}
                        </td>
                        <td class="py-3 px-4 text-sm">
                            <div class="flex space-x-2">
                                <button
                                    @click="$dispatch('open-activate-modal', {
                                        id: {{ $user->id }},
                                        name: '{{ $user->lname }}, {{ $user->fname }}'
                                    })"
                                    class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 cursor-pointer"
                                    title="Activate">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                    </svg>
                                </button>
                                <button
                                    @click="$dispatch('open-edit-archive-modal', {
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
                                        user_type: '{{ $user->user_type }}'
                                    })"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 cursor-pointer"
                                    title="Edit & Activate">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                                <button
                                    @click="$dispatch('open-delete-archive-modal', {
                                        id: {{ $user->id }},
                                        name: '{{ $user->lname }}, {{ $user->fname }}'
                                    })"
                                    class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 cursor-pointer"
                                    title="Delete Permanently">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-500 dark:text-gray-400">
                            No archived accounts found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">
        {{ $users->links('template.pagination', data: ['scrollTo' => false]) }}
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
                        <span x-text="selectedCount"></span> user<span x-show="selectedCount > 1">s</span> selected
                    </span>
                    <button @click="clearSelection()"
                        class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 underline">
                        Clear selection
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        @click="$dispatch('open-bulk-activate-modal', { count: selectedCount, ids: selectedUsers })"
                        class="flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 dark:focus:ring-green-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                        </svg>
                        <span>Activate Selected</span>
                    </button>
                    <button
                        @click="$dispatch('open-bulk-delete-archive-modal', { count: selectedCount, ids: selectedUsers })"
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

    @include('components.archivePage.activateModal')
    @include('components.archivePage.editModal')
    @include('components.archivePage.deleteModal')
    @include('components.archivePage.bulkActivateModal')
    @include('components.archivePage.bulkDeleteModal')
</div>
