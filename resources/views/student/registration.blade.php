<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - CLSU Library</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #16a34a;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #15803d;
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-linear-to-br from-green-50 to-green-100 min-h-screen">
    <div x-data="registrationApp()" x-init="init()">
        <!-- Header -->
        <header class="bg-linear-to-r from-green-600 to-green-700 text-white shadow-xl">
            <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('scanner.index') }}"
                            class="p-2 rounded-lg bg-white/10 hover:bg-white/20 transition-all duration-200 backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight">Library Registration</h1>
                            <p class="text-green-50 text-sm mt-1 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                </svg>
                                CLSU Library and Information Services Office
                            </p>
                        </div>
                    </div>
                    <div class="text-right bg-white/10 backdrop-blur-sm px-4 py-3 rounded-lg border border-white/20">
                        <p class="text-xs text-green-100 uppercase tracking-wider">Current Date</p>
                        <p class="font-semibold text-lg"
                            x-text="new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })">
                        </p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
            <!-- Success Message -->
            <div x-show="showSuccess" x-cloak
                class="mb-6 bg-green-50 border-l-4 border-green-500 p-6 rounded-lg shadow-lg animate-fade-in">
                <div class="flex items-start">
                    <div class="shrink-0">
                        <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-lg font-semibold text-green-800">Registration Successful!</h3>
                        <p class="mt-2 text-green-700" x-text="successMessage"></p>
                        <div class="mt-4 p-4 bg-white rounded-lg border border-green-200">
                            <p class="text-sm text-gray-600 mb-2">Your Account Details:</p>
                            <p class="font-semibold text-gray-900" x-text="'Name: ' + registeredUser.name"></p>
                            <p class="font-semibold text-gray-900" x-text="'ID/Barcode: ' + registeredUser.barcode"></p>
                            <p class="text-sm text-gray-600 mt-2" x-text="'User Type: ' + registeredUser.user_type"></p>
                        </div>
                        <button @click="resetForm()"
                            class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                            Register Another Account
                        </button>
                    </div>
                </div>
            </div>

            <!-- Redirect Countdown Card -->
            <div x-show="showSuccess" x-cloak
                class="mb-6 bg-green-50 border-l-4 border-green-500 p-6 rounded-lg shadow-lg animate-fade-in">
                <div class="flex items-start">
                    <div class="shrink-0">
                        <svg class="h-6 w-6 text-green-500 animate-spin" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-lg font-semibold text-green-800">Redirecting...</h3>
                        <p class="mt-2 text-green-700">
                            You will be redirected to the scanner page in <span class="font-bold text-xl"
                                x-text="countdown"></span> seconds
                        </p>
                        <div class="mt-3 w-full bg-green-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full transition-all duration-1000"
                                :style="`width: ${(countdown / 6) * 100}%`"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Form -->
            <div x-show="!showSuccess" class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                        </svg>
                        New User Registration
                    </h2>
                    <p class="text-green-50 text-sm mt-1">Please fill in all required information</p>
                </div>

                <form @submit.prevent="submitRegistration()" class="p-6 space-y-6">
                    <!-- Error Messages -->
                    <div x-show="Object.keys(errors).length > 0" x-cloak
                        class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                        <div class="flex">
                            <div class="shrink-0">
                                <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-red-800">Please correct the following errors:
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <template x-for="(error, field) in errors" :key="field">
                                            <li x-text="error[0]"></li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ID/Barcode -->
                    <div>
                        <label for="barcode" class="block text-sm font-semibold text-gray-700 mb-2">
                            School ID/LRN <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="barcode" x-model="formData.barcode" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                            placeholder="Enter your School ID or LRN">
                        <p class="text-xs text-gray-500 mt-1">Your school ID number</p>
                    </div>

                    <!-- Name Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="fname" class="block text-sm font-semibold text-gray-700 mb-2">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="fname" x-model="formData.fname" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                placeholder="First name">
                        </div>
                        <div>
                            <label for="mname" class="block text-sm font-semibold text-gray-700 mb-2">
                                Middle Name
                            </label>
                            <input type="text" id="mname" x-model="formData.mname"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                placeholder="Middle name">
                        </div>
                        <div>
                            <label for="lname" class="block text-sm font-semibold text-gray-700 mb-2">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="lname" x-model="formData.lname" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                placeholder="Last name">
                        </div>
                    </div>

                    <!-- Course and Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="course" class="block text-sm font-semibold text-gray-700 mb-2">
                                Course/Department <span class="text-red-500">*</span>
                            </label>
                            <select id="course" x-model="formData.course" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                                <option value="">Select Course</option>
                                <option value="BSAb">BSAb</option>
                                <option value="BSA">BSA</option>
                                <option value="BSFil">BAFil</option>
                                <option value="BALit">BALit</option>
                                <option value="BAIS">BAIS</option>
                                <option value="BASS">BASS</option>
                                <option value="BSDC">BSDC</option>
                                <option value="BSPsych">BSPsych</option>
                                <option value="BSAc">BSAc</option>
                                <option value="BSBA">BSBA</option>
                                <option value="BSEntrep">BSEntrep</option>
                                <option value="BSMA">BSMA</option>
                                <option value="BCAEd">BCAEd</option>
                                <option value="BECEd">BECEd</option>
                                <option value="BEEd">BEEd</option>
                                <option value="BPEd">BPEd</option>
                                <option value="BSEd">BSEd</option>
                                <option value="BTLEd">BTLEd</option>
                                <option value="BSABE">BSABE</option>
                                <option value="BSCE">BSCE</option>
                                <option value="BSIT">BSIT</option>
                                <option value="BSMet">BSMet</option>
                                <option value="BSF">BSF</option>
                                <option value="BSFT">BSFT</option>
                                <option value="BSTFT">BSTFT</option>
                                <option value="BSHM">BSHM</option>
                                <option value="BSTM">BSTM</option>
                                <option value="BSBio">BSBio</option>
                                <option value="BSChem">BSChem</option>
                                <option value="BSES">BSES</option>
                                <option value="BSMath">BSMath</option>
                                <option value="BSStat">BSStat</option>
                                <option value="DVM">DVM</option>
                                <option value="ASTS">CLSU-ASTS</option>
                                <option value="USHS">CLSU-USHS</option>
                                <option value="CLSU-Staff">CLSU-Staff</option>
                                <option value="CLSU-Faculty">CLSU-Faculty</option>
                                <option value="CLSU-Alumni">CLSU-Alumni</option>
                                <option value="visitor">Visitor</option>
                                <option value="MS-Student">MS-Student</option>
                                <option value="PHD-Student">PHD-Student</option>
                            </select>
                        </div>
                        <div>
                            <label for="section" class="block text-sm font-semibold text-gray-700 mb-2">
                                Section
                            </label>
                            <input type="text" id="section" x-model="formData.section"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                placeholder="e.g., 1-1, 2-1, 3B">
                        </div>
                    </div>

                    <!-- User Type and Sex -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="user_type" class="block text-sm font-semibold text-gray-700 mb-2">
                                User Type <span class="text-red-500">*</span>
                            </label>
                            <select id="user_type" x-model="formData.user_type" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                                <option value="">Select user type</option>
                                <option value="student">Student</option>
                                <option value="faculty">Faculty</option>
                                <option value="staff">Staff</option>
                                <option value="visitor">Visitor</option>
                            </select>
                        </div>
                        <div>
                            <label for="sex" class="block text-sm font-semibold text-gray-700 mb-2">
                                Sex <span class="text-red-500">*</span>
                            </label>
                            <select id="sex" x-model="formData.sex" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                                <option value="">Select sex</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Contact Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" x-model="formData.email" required
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                    placeholder="email@example.com">
                            </div>
                            <div>
                                <label for="phonenumber" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" id="phonenumber" x-model="formData.phonenumber" required
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                    placeholder="09XX XXX XXXX">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-between pt-6 border-t">
                        <a href="{{ route('scanner.index') }}"
                            class="px-6 py-3 rounded-lg shadow-lg bg-gray-200 hover:bg-gray-300 text-gray-700 hover:text-gray-900 font-semibold transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit" :disabled="isSubmitting"
                            class="px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-lg shadow-lg hover:from-green-700 hover:to-green-800 focus:ring-4 focus:ring-green-300 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                            <template x-if="!isSubmitting">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                                </svg>
                            </template>
                            <template x-if="isSubmitting">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </template>
                            <span x-text="isSubmitting ? 'Registering...' : 'Register'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </main>

        <!-- Footer -->
        <footer class="mt-12 text-center text-gray-600 pb-8">
            <p class="text-sm">© {{ date('Y') }} Central Luzon State University</p>
            <p class="text-xs mt-1">Library and Information Services Office</p>
        </footer>
    </div>

    <script>
        function registrationApp() {
            return {
                formData: {
                    barcode: '',
                    fname: '',
                    mname: '',
                    lname: '',
                    course: '',
                    section: '',
                    sex: '',
                    user_type: '',
                    email: '',
                    phonenumber: ''
                },
                errors: {},
                isSubmitting: false,
                showSuccess: false,
                successMessage: '',
                countdown: 6,
                countdownInterval: null,
                registeredUser: {
                    name: '',
                    barcode: '',
                    user_type: ''
                },

                init() {
                    console.log('Registration app initialized');
                },

                startCountdown() {
                    this.countdown = 6;
                    this.countdownInterval = setInterval(() => {
                        this.countdown--;
                        if (this.countdown <= 0) {
                            clearInterval(this.countdownInterval);
                            // Add 2-second grace period before redirecting
                            setTimeout(() => {
                                window.location.href = '{{ route('scanner.index') }}';
                            }, 2000);
                        }
                    }, 1000);
                },

                async submitRegistration() {
                    this.isSubmitting = true;
                    this.errors = {};

                    try {
                        const response = await fetch('{{ route('student.registration.submit') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.formData)
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.successMessage = data.message;
                            this.registeredUser = data.user;
                            this.showSuccess = true;
                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            });
                            // Start countdown after successful registration
                            this.startCountdown();
                        } else {
                            if (data.errors) {
                                this.errors = data.errors;
                            } else {
                                alert(data.message || 'Registration failed. Please try again.');
                            }
                        }
                    } catch (error) {
                        console.error('Registration error:', error);
                        alert('An error occurred. Please try again.');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                resetForm() {
                    // Clear countdown if it's running
                    if (this.countdownInterval) {
                        clearInterval(this.countdownInterval);
                    }

                    this.formData = {
                        barcode: '',
                        fname: '',
                        mname: '',
                        lname: '',
                        course: '',
                        section: '',
                        sex: '',
                        user_type: '',
                        email: '',
                        phonenumber: ''
                    };
                    this.errors = {};
                    this.showSuccess = false;
                    this.successMessage = '';
                    this.countdown = 6;
                    this.registeredUser = {
                        name: '',
                        barcode: '',
                        user_type: ''
                    };
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>
</body>

</html>
