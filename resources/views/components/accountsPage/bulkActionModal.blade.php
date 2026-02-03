<div x-data="{
    show: false,
    action: '',
    actionLabel: '',
    actionColor: '',
    actionIcon: '',
    selectedCount: 0,
    selectedIds: [],

    getActionDetails() {
        const details = {
            'inside': {
                label: 'Move Inside',
                color: 'green',
                icon: 'M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75',
                description: 'This will update the status of the selected user(s) to INSIDE.'
            },
            'outside': {
                label: 'Move Outside',
                color: 'blue',
                icon: 'M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9',
                description: 'This will update the status of the selected user(s) to OUTSIDE.'
            },
            'archive': {
                label: 'Archive',
                color: 'red',
                icon: 'm20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z',
                description: 'This will permanently move the selected user(s) to the archive. This action cannot be undone.'
            }
        };
        return details[this.action] || {};
    },

    getBgColor() {
        const colors = {
            'green': 'bg-green-100 dark:bg-green-900/20',
            'blue': 'bg-blue-100 dark:bg-blue-900/20',
            'red': 'bg-red-100 dark:bg-red-900/20'
        };
        return colors[this.getActionDetails().color] || 'bg-gray-100 dark:bg-gray-700';
    },

    getTextColor() {
        const colors = {
            'green': 'text-green-600 dark:text-green-400',
            'blue': 'text-blue-600 dark:text-blue-400',
            'red': 'text-red-600 dark:text-red-400'
        };
        return colors[this.getActionDetails().color] || 'text-gray-600 dark:text-gray-400';
    },

    getButtonColor() {
        const colors = {
            'green': 'bg-green-600 hover:bg-green-700 focus:ring-green-400 dark:bg-green-500 dark:hover:bg-green-600',
            'blue': 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-400 dark:bg-blue-500 dark:hover:bg-blue-600',
            'red': 'bg-red-600 hover:bg-red-700 focus:ring-red-400 dark:bg-red-500 dark:hover:bg-red-600'
        };
        return colors[this.getActionDetails().color] || 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-400 dark:bg-gray-500 dark:hover:bg-gray-600';
    }
}"
    @open-bulk-action-modal.window="
    action = $event.detail.action;
    selectedCount = $event.detail.count;
    selectedIds = $event.detail.ids;
    show = true;
"
    @keydown.escape.window="show = false" x-show="show" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-md" x-transition>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg dark:shadow-gray-900 w-120 max-h-[90vh] overflow-y-auto"
        @click.stop>
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Confirm Bulk Action</h2>
            <button @click="show = false"
                class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="px-6 py-6">
            <div class="flex items-start gap-4">
                <!-- Icon -->
                <div class="shrink-0 p-3 rounded-full" :class="getBgColor()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" :class="getTextColor()" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" :d="getActionDetails().icon" />
                    </svg>
                </div>

                <!-- Message -->
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        <span x-text="getActionDetails().label"></span> <span x-text="selectedCount"></span> User<span
                            x-show="selectedCount > 1">s</span>?
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        You are about to <span class="font-semibold lowercase" x-text="getActionDetails().label"></span>
                        <span class="font-semibold" x-text="selectedCount"></span> user<span
                            x-show="selectedCount > 1">s</span>.
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="getActionDetails().description"></p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
            <button type="button" @click="show = false"
                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-500">
                Cancel
            </button>
            <button type="button"
                @click="
                    if (action === 'archive') {
                        $wire.bulkArchive(selectedIds).then(() => {
                            show = false;
                            window.dispatchEvent(new CustomEvent('bulk-action-completed', {
                                detail: { action: 'archive', archivedIds: selectedIds }
                            }));
                        });
                    } else {
                        $wire.bulkUpdateStatus(selectedIds, action).then(() => {
                            show = false;
                            window.dispatchEvent(new CustomEvent('bulk-action-completed', {
                                detail: { action: action, updatedIds: selectedIds }
                            }));
                        });
                    }
                "
                class="px-4 py-2 text-white rounded-md focus:outline-none focus:ring-2" :class="getButtonColor()">
                <span x-text="'Confirm ' + getActionDetails().label"></span>
            </button>
        </div>
    </div>
</div>
