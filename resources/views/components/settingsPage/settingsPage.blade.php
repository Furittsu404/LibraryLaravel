<div class="px-6 py-6">
    <div class="">
        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Settings</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Configure system settings and manage your account</p>
        </div>

        {{-- Settings Sections (Vertical Layout with Dividers) --}}
        <div class="space-y-8">

            {{-- User Expiration Settings Section --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="max-w-5xl">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">User Expiration Date</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Default expiration date for new
                            accounts</p>
                    </div>

                    <div x-data="{
                        expirationDate: '{{ $defaultExpirationDate }}',
                        saving: false,
                        async saveExpiration() {
                            if (this.saving) return;
                            this.saving = true;
                    
                            try {
                                const response = await fetch('{{ route('settings.expiration.update') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        expiration_date: this.expirationDate
                                    })
                                });
                    
                                const data = await response.json();
                    
                                if (data.success) {
                                    window.dispatchEvent(new CustomEvent('show-toast', {
                                        detail: { type: 'success', message: data.message }
                                    }));
                                    Livewire.dispatch('settingsUpdated');
                                } else {
                                    window.dispatchEvent(new CustomEvent('show-toast', {
                                        detail: { type: 'error', message: data.message || 'Error updating expiration date' }
                                    }));
                                }
                            } catch (error) {
                                window.dispatchEvent(new CustomEvent('show-toast', {
                                    detail: { type: 'error', message: 'Error updating expiration date' }
                                }));
                                console.error(error);
                            } finally {
                                this.saving = false;
                            }
                        }
                    }">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Default Expiration Date
                                </label>
                                <input type="date" x-model="expirationDate"
                                    class="w-full max-w-xs px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                    New accounts will automatically expire on <span x-text="expirationDate"></span>
                                </p>
                            </div>

                            <button @click="saveExpiration()" :disabled="saving"
                                :class="saving ? 'opacity-50 cursor-not-allowed' : ''"
                                class="bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                                <span x-show="!saving">Save Changes</span>
                                <span x-show="saving">Saving...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Auto Logout Settings Section --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="max-w-5xl">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Auto Logout Time</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Daily automatic logout time</p>
                    </div>

                    <div x-data="{
                        logoutTime: '{{ $autoLogoutTime }}',
                        saving: false,
                        async saveLogoutTime() {
                            if (this.saving) return;
                            this.saving = true;
                    
                            try {
                                const response = await fetch('{{ route('settings.logout.update') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        logout_time: this.logoutTime
                                    })
                                });
                    
                                const data = await response.json();
                    
                                if (data.success) {
                                    window.dispatchEvent(new CustomEvent('show-toast', {
                                        detail: { type: 'success', message: data.message }
                                    }));
                                    Livewire.dispatch('settingsUpdated');
                                } else {
                                    window.dispatchEvent(new CustomEvent('show-toast', {
                                        detail: { type: 'error', message: data.message || 'Error updating auto-logout time' }
                                    }));
                                }
                            } catch (error) {
                                window.dispatchEvent(new CustomEvent('show-toast', {
                                    detail: { type: 'error', message: 'Error updating auto-logout time' }
                                }));
                                console.error(error);
                            } finally {
                                this.saving = false;
                            }
                        }
                    }">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Auto Logout Time
                                </label>
                                <input type="time" x-model="logoutTime"
                                    class="w-full max-w-xs px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                    Users will be automatically logged out daily at <span x-text="logoutTime"></span>
                                </p>
                            </div>

                            <div
                                class="mb-4 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50 border-l-4 border-gray-400 dark:border-gray-600 pl-4 py-2">
                                <strong>Note:</strong> This requires a database event scheduler to be enabled. The
                                system
                                will automatically log out all users who haven't scanned out at the specified time each
                                day.
                            </div>

                            <button @click="saveLogoutTime()" :disabled="saving"
                                :class="saving ? 'opacity-50 cursor-not-allowed' : ''"
                                class="bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                                <span x-show="!saving">Save Changes</span>
                                <span x-show="saving">Saving...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Scanner Password Settings Section --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="max-w-5xl">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Attendance Scanner Password</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Password required to access section
                            settings in the scanner</p>
                    </div>

                    <div x-data="{
                        scannerPassword: '',
                        confirmPassword: '',
                        saving: false,
                        async saveScannerPassword() {
                            if (this.saving) return;
                    
                            if (!this.scannerPassword) {
                                window.dispatchEvent(new CustomEvent('show-toast', {
                                    detail: { type: 'error', message: 'Please enter a password' }
                                }));
                                return;
                            }
                    
                            if (this.scannerPassword !== this.confirmPassword) {
                                window.dispatchEvent(new CustomEvent('show-toast', {
                                    detail: { type: 'error', message: 'Passwords do not match' }
                                }));
                                return;
                            }
                    
                            this.saving = true;
                    
                            try {
                                const response = await fetch('{{ route('settings.scanner-password.update') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        scanner_password: this.scannerPassword
                                    })
                                });
                    
                                const data = await response.json();
                    
                                if (data.success) {
                                    window.dispatchEvent(new CustomEvent('show-toast', {
                                        detail: { type: 'success', message: data.message }
                                    }));
                                    this.scannerPassword = '';
                                    this.confirmPassword = '';
                                    Livewire.dispatch('settingsUpdated');
                                } else {
                                    window.dispatchEvent(new CustomEvent('show-toast', {
                                        detail: { type: 'error', message: data.message || 'Error updating scanner password' }
                                    }));
                                }
                            } catch (error) {
                                window.dispatchEvent(new CustomEvent('show-toast', {
                                    detail: { type: 'error', message: 'Error updating scanner password' }
                                }));
                                console.error(error);
                            } finally {
                                this.saving = false;
                            }
                        }
                    }">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    New Scanner Password
                                </label>
                                <input type="password" x-model="scannerPassword"
                                    class="w-full max-w-xs px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400"
                                    placeholder="Enter new password">
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Confirm Password
                                </label>
                                <input type="password" x-model="confirmPassword"
                                    class="w-full max-w-xs px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400"
                                    placeholder="Confirm password">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                    This password will be required to access section settings on the attendance scanner
                                </p>
                            </div>

                            <div
                                class="mb-4 text-sm text-gray-600 dark:text-gray-400 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 dark:border-blue-600 pl-4 py-2">
                                <strong>Security Note:</strong> This password is separate from your admin account and is
                                used to prevent unauthorized section changes on the public scanner interface.
                            </div>

                            <button @click="saveScannerPassword()" :disabled="saving"
                                :class="saving ? 'opacity-50 cursor-not-allowed' : ''"
                                class="bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                                <span x-show="!saving">Update Scanner Password</span>
                                <span x-show="saving">Updating...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Admin Account Settings Section --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="max-w-5xl">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Admin Account</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update your account information and
                            password</p>
                    </div>

                    <div x-data="{
                        name: '{{ $adminName }}',
                        email: '{{ $adminEmail }}',
                        currentPassword: '',
                        newPassword: '',
                        newPasswordConfirmation: '',
                        saving: false,
                        async saveAccount() {
                            if (this.saving) return;
                            this.saving = true;
                    
                            try {
                                const response = await fetch('{{ route('settings.admin.update') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        name: this.name,
                                        email: this.email,
                                        current_password: this.currentPassword,
                                        new_password: this.newPassword,
                                        new_password_confirmation: this.newPasswordConfirmation
                                    })
                                });
                    
                                const data = await response.json();
                                console.log('Response:', response.status, data);
                    
                                if (data.success) {
                                    window.dispatchEvent(new CustomEvent('show-toast', {
                                        detail: { type: 'success', message: data.message }
                                    }));
                                    this.currentPassword = '';
                                    this.newPassword = '';
                                    this.newPasswordConfirmation = '';
                                    Livewire.dispatch('settingsUpdated');
                                } else {
                                    // Handle validation errors or custom error messages
                                    let errorMessage = data.message || 'Error updating account';
                    
                                    // Check for Laravel validation errors
                                    if (data.errors) {
                                        const firstError = Object.values(data.errors)[0];
                                        errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                                    }
                    
                                    window.dispatchEvent(new CustomEvent('show-toast', {
                                        detail: { type: 'error', message: errorMessage }
                                    }));
                                }
                            } catch (error) {
                                window.dispatchEvent(new CustomEvent('show-toast', {
                                    detail: { type: 'error', message: 'Network error: ' + error.message }
                                }));
                                console.error('Fetch error:', error);
                            } finally {
                                this.saving = false;
                            }
                        }
                    }">
                        <div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                                {{-- Account Information --}}
                                <div class="space-y-4">
                                    <h3 class="font-medium text-gray-900 dark:text-white mb-4">Account Information</h3>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Name
                                        </label>
                                        <input type="text" x-model="name"
                                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Email
                                        </label>
                                        <input type="text" x-model="email"
                                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400">
                                    </div>
                                </div>

                                {{-- Password Change --}}
                                <div class="space-y-4">
                                    <h3 class="font-medium text-gray-900 dark:text-white mb-4">Change Password
                                        (Optional)
                                    </h3>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Current Password
                                        </label>
                                        <input type="password" x-model="currentPassword"
                                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            New Password
                                        </label>
                                        <input type="password" x-model="newPassword"
                                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Confirm New Password
                                        </label>
                                        <input type="password" x-model="newPasswordConfirmation"
                                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button @click="saveAccount()" :disabled="saving"
                                    :class="saving ? 'opacity-50 cursor-not-allowed' : ''"
                                    class="bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                                    <span x-show="!saving">Save Account Changes</span>
                                    <span x-show="saving">Saving...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
