<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MyUkm')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-[Inter]">
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm py-3 px-6 border-b">
            <div class="max-w-6xl mx-auto flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <a href="{{ url('/') }}" class="flex items-center space-x-2 hover:opacity-75 transition">



                        <span class="font-bold text-lg">MyUkm</span>
                    </a>
                </div>

                @if(Request::is('ukm/*/chat'))
                    <div class="hidden sm:flex items-center">
                        <a href="{{ route('ukm.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-blue-600 transition">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali ke Beranda
                        </a>
                    </div>
                @endif

                @auth
                <div class="flex items-center space-x-6">
                    <a href="{{ route('profile.show') }}" class="flex items-center space-x-3 py-1 px-3 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex flex-col items-end">
                            <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="h-8 w-8 rounded-full overflow-hidden ring-2 ring-gray-100">
                            @if(auth()->user()->photo)
                                <img src="{{ Storage::url(auth()->user()->photo) }}" 
                                     alt="{{ auth()->user()->name }}" 
                                     class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center bg-blue-50">
                                    <span class="text-sm font-medium text-blue-600">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-600 font-medium text-sm transition-colors">
                            Keluar
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>
    </div>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>
    @vite(['resources/js/app.js'])
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
