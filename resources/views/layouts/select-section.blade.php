<!DOCTYPE html>
<html lang="en">
@include('template.head')

<body class="bg-gradient-to-br from-green-600 to-green-800 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4 py-8">
        <div class="text-center mb-8">
            <img src="{{ asset('storage/images/LISO_LogoColored.png') }}"
                class="w-24 h-24 mx-auto mb-4 bg-white rounded-full p-2">
            <h1 class="text-3xl font-bold text-white mb-2">Select Library Section</h1>
            <p class="text-white/80">Choose which section to manage</p>
        </div>

        @if (session('error'))
            <div class="max-w-4xl mx-auto mb-6 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="max-w-7xl mx-auto space-y-8">
            {{-- 1st Floor Sections --}}
            <div>
                <h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-3">
                    <span>1st Floor</span>
                    <div class="flex-1 h-px bg-white/30"></div>
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($sections as $section)
                        @if (in_array($section->code, ['entrance', 'periodicals', 'makers', 'multimedia']))
                            <form action="{{ route('set-section') }}" method="POST">
                                @csrf
                                <input type="hidden" name="section" value="{{ $section->code }}">
                                <button type="submit"
                                    class="w-full bg-white hover:bg-green-50 rounded-xl shadow-lg p-8 transition-all transform hover:scale-105 hover:shadow-2xl group">
                                    <div
                                        class="mb-4 transform group-hover:scale-110 transition-transform flex justify-center">
                                        @switch($section->code)
                                            @case('entrance')
                                                <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                                </svg>
                                            @break

                                            @case('periodicals')
                                                <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                                </svg>
                                            @break

                                            @case('makers')
                                                <svg class="w-16 h-16 text-orange-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                                </svg>
                                            @break

                                            @case('multimedia')
                                                <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            @break
                                        @endswitch
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-800 mb-2">
                                        {{ $section->name }}
                                    </h3>
                                    <p class="text-gray-600 text-sm">Click to manage</p>
                                </button>
                            </form>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- 2nd Floor Sections --}}
            <div>
                <h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-3">
                    <span>2nd Floor</span>
                    <div class="flex-1 h-px bg-white/30"></div>
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($sections as $section)
                        @if (in_array($section->code, ['humanities', 'science', 'filipiniana']))
                            <form action="{{ route('set-section') }}" method="POST">
                                @csrf
                                <input type="hidden" name="section" value="{{ $section->code }}">
                                <button type="submit"
                                    class="w-full bg-white hover:bg-green-50 rounded-xl shadow-lg p-8 transition-all transform hover:scale-105 hover:shadow-2xl group">
                                    <div
                                        class="mb-4 transform group-hover:scale-110 transition-transform flex justify-center">
                                        @switch($section->code)
                                            @case('humanities')
                                                <svg class="w-16 h-16 text-purple-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                            @break

                                            @case('science')
                                                <svg class="w-16 h-16 text-indigo-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                                </svg>
                                            @break

                                            @case('filipiniana')
                                                <svg class="w-16 h-16 text-yellow-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                                                </svg>
                                            @break
                                        @endswitch
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-800 mb-2">
                                        {{ $section->name }}
                                    </h3>
                                    <p class="text-gray-600 text-sm">Click to manage</p>
                                </button>
                            </form>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="text-center mt-8">
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button class="text-white/80 hover:text-white text-sm flex items-center gap-2 mx-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>
</body>

</html>
