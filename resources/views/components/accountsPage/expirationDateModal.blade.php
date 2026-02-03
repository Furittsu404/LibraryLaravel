<div x-data="{
    show: false,
    expirationDate: '',
    isLoading: false
}" @open-expiration-date-modal.window="
    show = true;
    expirationDate = '';
"
    @keydown.escape.window="show = false" x-show="show" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-md" x-transition>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg dark:shadow-gray-900 w-[30rem] max-h-[90vh] overflow-y-auto"
        @click.stop x-ref="expirationForm">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Change All Expiration Dates</h2>
            <button @click="show = false"
                class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        @csrf

        <!-- Content -->
        <div class="px-6 py-6">
            <div class="flex items-start gap-4">
                <!-- Calendar Icon -->
                <div class="flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-purple-600 dark:text-purple-500"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>

                <!-- Message -->
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Update All Users</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Set a new expiration date for <span class="font-semibold text-gray-900 dark:text-gray-100">ALL
                            users</span> in the system.
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        This action will update every user's expiration date to the date you select below.
                    </p>

                    <!-- Date Input -->
                    <div class="mt-4">
                        <label for="expirationDateInput"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            New Expiration Date
                        </label>
                        <input type="date" id="expirationDateInput" x-model="expirationDate" :disabled="isLoading"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 disabled:opacity-50 disabled:cursor-not-allowed"
                            required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
            <button type="button" @click="show = false" :disabled="isLoading"
                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-500 disabled:opacity-50 disabled:cursor-not-allowed">
                Cancel
            </button>
            <button type="button" :disabled="isLoading || !expirationDate"
                @click="
                if (!expirationDate) {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'error', message: 'Please select a date' }
                    }));
                    return;
                }

                isLoading = true;

                fetch('/accounts/update-all-expiration-dates', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $refs.expirationForm.querySelector('input[name=_token]').value
                    },
                    body: JSON.stringify({ expiration_date: expirationDate })
                })
                .then(res => res.json())
                .then(data => {
                    isLoading = false;
                    if (data.success) {
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: { type: 'success', message: data.message }
                        }));
                        $wire.$refresh();
                        show = false;
                    } else {
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: { type: 'error', message: data.message || 'An error occurred' }
                        }));
                    }
                })
                .catch(error => {
                    isLoading = false;
                    console.error('Error:', error);
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'error', message: 'Failed to update expiration dates' }
                    }));
                });
            "
                class="px-4 py-2 bg-purple-600 dark:bg-purple-600 text-white rounded-md hover:bg-purple-700 dark:hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-400 dark:focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!isLoading">Update All Dates</span>
                <span x-show="isLoading" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Updating...
                </span>
            </button>
        </div>
    </div>
</div>
