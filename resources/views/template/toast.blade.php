<div class="fixed bottom-0 right-0 mb-4 mr-4 z-50 flex flex-col gap-2" x-data="{
    toasts: [],
    maxToasts: 5,
    addToast(type, message) {
        const id = Date.now();

        if (this.toasts.length >= this.maxToasts) {
            this.toasts.shift();
        }

        this.toasts.push({ id, type, message, show: false });

        this.$nextTick(() => {
            const index = this.toasts.findIndex(t => t.id === id);
            if (index !== -1) {
                this.toasts[index].show = true;
            }
        });

        setTimeout(() => {
            this.removeToast(id);
        }, 3000);
    },
    removeToast(id) {
        const index = this.toasts.findIndex(t => t.id === id);
        if (index !== -1) {
            this.toasts[index].show = false;
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 300);
        }
    },
    getColorClass(type) {
        switch (type) {
            case 'success':
                return 'bg-green-500 text-white';
            case 'error':
                return 'bg-red-500 text-white';
            case 'warning':
                return 'bg-yellow-500 text-white';
            case 'info':
                return 'bg-blue-500 text-white';
            default:
                return 'bg-gray-500 text-white';
        }
    }
}" x-init="window.addEventListener('show-toast', (e) => {
    addToast(e.detail.type, e.detail.message);
});">

    {{-- Server-side session messages --}}
    @if (session('success'))
        <div class="bg-green-500 text-white px-4 py-2 rounded shadow-md animate-slide-in-right" x-data="{ show: true }"
            x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-full">
            {{ session('success') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="bg-yellow-500 text-white px-4 py-2 rounded shadow-md animate-slide-in-right" x-data="{ show: true }"
            x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-full">
            {{ session('warning') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-500 text-white px-4 py-2 rounded shadow-md animate-slide-in-right" x-data="{ show: true }"
            x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-full">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Dynamic stacking toasts --}}
    <template x-for="toast in toasts" :key="toast.id">
        <div class="flex items-center justify-between gap-4 px-4 py-2 rounded shadow-md min-w-64 transition-all duration-300 ease-out"
            :class="(toast.show ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-full') + ' ' + getColorClass(toast.type)">
            <div class="flex items-center gap-2 cursor-pointer" @click="removeToast(toast.id)">
                {{-- Success Icon --}}
                <svg x-show="toast.type === 'success'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{-- Error Icon --}}
                <svg x-show="toast.type === 'error'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{-- Warning Icon --}}
                <svg x-show="toast.type === 'warning'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{-- Info Icon --}}
                <svg x-show="toast.type === 'info'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span x-text="toast.message"></span>
            </div>
            {{-- Close button --}}
            <button class="text-white hover:text-gray-200 focus:outline-none cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>
</div>
