<div x-data="{
    show: false,
    file: null,
    fileName: '',
    uploading: false,
    resetForm() {
        this.file = null;
        this.fileName = '';
        this.uploading = false;
        $refs.fileInput.value = '';
    },
    handleFileChange(event) {
        this.file = event.target.files[0];
        this.fileName = this.file ? this.file.name : '';
    }
}" @open-import-modal.window="show = true; resetForm();" @keydown.escape.window="show = false"
    x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-md" x-transition>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg dark:shadow-gray-900 w-[35rem] max-h-[90vh] overflow-y-auto"
        @click.stop>
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Import CSV File</h2>
            <button @click="show = false"
                class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Form -->
        <form
            @submit.prevent="
                if (!file) {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'error', message: 'Please select a CSV file to upload' }
                    }));
                    return;
                }

                uploading = true;
                const formData = new FormData();
                formData.append('file', file);

                fetch('/accounts/import', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $refs.importForm.querySelector('input[name=_token]').value
                    },
                    body: formData
                })
                .then(res => {
                    if (!res.ok) {
                        return res.json().then(data => {
                            throw new Error(data.message || 'Upload failed');
                        });
                    }
                    return res.json();
                })
                .then(data => {
                    if(data.success) {
                        show = false;
                        resetForm();
                        window.Livewire.dispatch('userCreated');
                        if (window.Livewire && typeof window.Livewire.emit === 'function') {
                            window.Livewire.emit('statsUpdated');
                        }
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: { type: 'success', message: data.message || 'CSV imported successfully!' }
                        }));
                    } else {
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: { type: 'error', message: data.message || 'Import failed' }
                        }));
                    }
                })
                .catch(error => {
                    console.error('Upload error:', error);
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'error', message: error.message || 'An error occurred during upload' }
                    }));
                })
                .finally(() => {
                    uploading = false;
                });
            "
            class="px-6 py-4" x-ref="importForm">

            @csrf

            <!-- File Upload Area -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Select CSV File <span
                        class="text-red-500">*</span></label>

                <!-- Custom File Input -->
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-[#009639] dark:hover:border-[#00b347] transition-colors cursor-pointer"
                    @click="$refs.fileInput.click()">
                    <input type="file" x-ref="fileInput" accept=".csv" @change="handleFileChange" class="hidden">

                    <div x-show="!fileName">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-500 mb-3" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="text-gray-600 dark:text-gray-400 mb-1">Click to upload or drag and drop</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">CSV files only</p>
                    </div>

                    <div x-show="fileName" class="flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500 dark:text-green-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div class="text-left">
                            <p class="text-gray-800 dark:text-gray-100 font-medium" x-text="fileName"></p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Click to change file</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="mb-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm text-blue-800 dark:text-blue-300">
                        <p class="font-semibold mb-1">CSV Format Requirements:</p>
                        <ul class="list-disc list-inside space-y-1 text-blue-700 dark:text-blue-400">
                            <li>First row should contain headers</li>
                            <li>Required columns: lname, fname, email, barcode, course, sex</li>
                            <li>Use comma (,) as delimiter</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2">
                <button type="button" @click="show = false" :disabled="uploading"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-500 disabled:opacity-50">
                    Cancel
                </button>
                <button type="submit" :disabled="uploading || !file"
                    class="px-4 py-2 bg-[#009639] dark:bg-[#00b347] text-white rounded-md hover:bg-[#007a2e] dark:hover:bg-[#009639] focus:outline-none focus:ring-2 focus:ring-green-400 dark:focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed! flex items-center gap-2">
                    <span x-show="!uploading">Import CSV</span>
                    <span x-show="uploading" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Uploading...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
