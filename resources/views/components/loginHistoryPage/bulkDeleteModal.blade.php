<div x-data="{
    show: false,
    count: 0
}" @open-bulk-delete-modal.window="
    count = $event.detail.count;
    show = true;
"
    @keydown.escape.window="show = false" x-show="show" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-md" x-transition>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg dark:shadow-gray-900 w-120 max-h-[90vh] overflow-y-auto"
        @click.stop>
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Delete Multiple Login Records</h2>
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
                <!-- Warning Icon -->
                <div class="shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-600 dark:text-red-500"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <!-- Message -->
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Are you sure?</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        You are about to permanently delete <span class="font-semibold text-gray-900 dark:text-gray-100"
                            x-text="count"></span>
                        <span x-text="count === 1 ? 'login record' : 'login records'"></span>.
                    </p>
                    <p class="text-sm text-red-600 dark:text-red-400 font-semibold">
                        ⚠️ This action cannot be undone. All selected login records will be permanently deleted from the
                        database.
                    </p>
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
                $dispatch('confirm-bulk-delete');
                show = false;
            "
                class="px-4 py-2 bg-red-600 dark:bg-red-700 text-white rounded-md hover:bg-red-700 dark:hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-600">
                Delete <span x-text="count"></span> Record<span x-show="count !== 1">s</span> Permanently
            </button>
        </div>
    </div>
</div>
