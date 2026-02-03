<div x-data="{
    show: false,
    userId: null,
    userName: ''
}"
    @open-delete-modal.window="
    userId = $event.detail.id;
    userName = $event.detail.name;
    show = true;
"
    @keydown.escape.window="show = false" x-show="show" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-md" x-transition>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg dark:shadow-gray-900 w-[30rem] max-h-[90vh] overflow-y-auto"
        @click.stop x-ref="deleteForm">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Archive Account</h2>
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
                <!-- Warning Icon -->
                <div class="flex-shrink-0">
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
                        You are about to archive the account for <span
                            class="font-semibold text-gray-900 dark:text-gray-100" x-text="userName"></span>.
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        This action cannot be undone. All data associated with this account will be moved to the archive
                        section.
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
                fetch('/accounts/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $refs.deleteForm.querySelector('input[name=_token]').value
                    },
                    body: JSON.stringify({ id: userId })
                })
                .then(res => {
                    if (!res.ok) {
                        return res.text().then(text => {
                            console.error('Server response:', text);
                            window.dispatchEvent(new CustomEvent('show-toast', {
                                detail: { type: 'error', message: 'Archive failed: ' + res.status }
                            }));
                            throw new Error(text);
                        });
                    }
                    return res.json();
                })
                .then(data => {
                    if(data.success) {
                        show = false;
                        window.Livewire.dispatch('userDeleted');
                        // trigger immediate dashboard/sidebar refresh in this session
                        if (window.Livewire && typeof window.Livewire.emit === 'function') {
                            window.Livewire.emit('statsUpdated');
                            window.Livewire.emit('reservationsUpdated');
                        }
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: { type: 'success', message: data.message || 'Account archived successfully!' }
                        }));
                    } else {
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: { type: 'error', message: data.message || 'Archive failed' }
                        }));
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'error', message: 'An error occurred. Please try again.' }
                    }));
                })
                "
                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400">
                Archive Account
            </button>
        </div>
    </div>
</div>
