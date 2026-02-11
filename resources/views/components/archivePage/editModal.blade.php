<div x-data="{
    show: false,
    user: {
        id: '',
        lname: '',
        fname: '',
        mname: '',
        sex: '',
        email: '',
        phonenumber: '',
        address: '',
        course: '',
        section: '',
        barcode: '',
        user_type: ''
    }
}" @open-edit-archive-modal.window="
    user = $event.detail;
    show = true;
"
    @keydown.escape.window="show = false" x-show="show" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-md" x-transition>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg dark:shadow-gray-900 w-[40rem] max-h-[90vh] overflow-y-auto"
        @click.stop>
        <!-- Header -->
        <div
            class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center sticky top-0 bg-white dark:bg-gray-800">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Edit & Activate Account</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Account will be automatically activated upon
                    saving</p>
            </div>
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
            fetch('/archive/edit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $el.querySelector('input[name=_token]').value
                },
                body: JSON.stringify(user)
            })
            .then(res => {
                if (!res.ok) {
                    return res.text().then(text => {
                        console.error('Server response:', text);
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: { type: 'error', message: 'Update failed: ' + res.status }
                        }));
                        throw new Error(text);
                    });
                }
                return res.json();
            })
            .then(data => {
                if(data.success) {
                    show = false;
                    window.Livewire.dispatch('userActivated');
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'success', message: data.message || 'User updated and activated successfully!' }
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'error', message: data.message || 'Update failed' }
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
            class="px-6 py-4">

            @csrf

            <!-- Name Fields -->
            <div class="mb-4 flex gap-4">
                <div class="flex-1">
                    <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Last Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" x-model="user.lname" required
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">
                </div>
                <div class="flex-1">
                    <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">First Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" x-model="user.fname" required
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">
                </div>
                <div class="flex-1">
                    <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Middle Name</label>
                    <input type="text" x-model="user.mname"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">
                </div>
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Email <span
                        class="text-red-500">*</span></label>
                <input type="email" x-model="user.email"
                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">
            </div>

            <div class="mb-4 flex gap-4">
                <!-- Phone -->
                <div class="flex-1">
                    <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Phone Number</label>
                    <input type="text" x-model="user.phonenumber"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">
                </div>
                <!-- Sex -->
                <div class="flex-1">
                    <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Sex <span
                            class="text-red-500">*</span></label>
                    <select x-model="user.sex" required
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">
                        <option value="">Select Sex</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
            </div>

            <!-- Address -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Address</label>
                <input type="text" x-model="user.address"
                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">
            </div>

            <!-- Course & Section -->
            <div class="mb-4 flex gap-4">
                <div class="flex-1">
                    <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Course <span
                            class="text-red-500">*</span></label>
                    <select type="text" x-model="user.course" required
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">
                        <option value="BSAb">BSAb</option>
                        <option value="BSA">BSA</option>
                        <option value="BAFil">BAFil</option>
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
                <div class="flex-1">
                    <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Section</label>
                    <input type="text" x-model="user.section"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">
                </div>
            </div>

            <div class="mb-6 flex gap-4">
                <!-- Barcode/ID -->
                <div class="flex-1">
                    <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">ID Number <span
                            class="text-red-500">*</span></label>
                    <input type="text" x-model="user.barcode" required
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">
                </div>
                <!-- User Type -->
                <div class="flex-1">
                    <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">User Type <span
                            class="text-red-500">*</span></label>
                    <select x-model="user.user_type" required
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#009639] dark:focus:ring-[#00b347]">
                        <option value="">Select User Type</option>
                        <option value="student">Student</option>
                        <option value="visitor">Visitor</option>
                        <option value="faculty">Faculty</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2">
                <button type="button" @click="show = false"
                    class="w-40 bg-gray-500 dark:bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-500">
                    Cancel
                </button>
                <button type="submit"
                    class="w-48 bg-green-600 dark:bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 dark:focus:ring-green-500 flex items-center justify-center gap-2">
                    Save & Activate
                </button>
            </div>
        </form>
    </div>
</div>
