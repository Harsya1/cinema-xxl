<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Cinema XXL - Experience Movies Like Never Before' }}</title>
    <meta name="description" content="Cinema XXL - Premium cinema experience with the latest movies, comfortable seating, and amazing food & beverages.">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect fill='%23f59e0b' rx='15' width='100' height='100'/><text x='50%' y='58%' font-size='50' text-anchor='middle' fill='%231f2937'>🎬</text></svg>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        amber: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    }
                }
            }
        }
    </script>
    
    @livewireStyles

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #1f2937;
        }
        ::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }

        /* Line clamp utility */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-gray-900 text-white antialiased">
    {{-- Navigation --}}
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-b from-gray-900 to-transparent">
        <div class="container mx-auto px-4 md:px-8 lg:px-16">
            <div class="flex items-center justify-between h-20">
                {{-- Logo --}}
                <a href="/" class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-900" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm3 2h6v4H7V5zm8 8v2h1v-2h-1zm-2-2H7v4h6v-4zm2 0h1V9h-1v2zm1-4V5h-1v2h1zM5 5v2H4V5h1zm0 4H4v2h1V9zm-1 4h1v2H4v-2z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white hidden sm:block">Cinema<span class="text-amber-500">XXL</span></span>
                </a>

                {{-- Navigation Links --}}
                <div class="hidden md:flex items-center gap-8">
                    <a href="/" class="text-white font-medium hover:text-amber-400 transition-colors">Home</a>
                    <a href="#" class="text-gray-400 font-medium hover:text-amber-400 transition-colors">Now Playing</a>
                    <a href="#" class="text-gray-400 font-medium hover:text-amber-400 transition-colors">Coming Soon</a>
                    <a href="#" class="text-gray-400 font-medium hover:text-amber-400 transition-colors">Promotions</a>
                </div>

                {{-- Auth Buttons --}}
                <div class="flex items-center gap-4">
                    <a href="/admin" class="text-gray-400 hover:text-white font-medium transition-colors">
                        Sign In
                    </a>
                    <a href="#" class="bg-amber-500 hover:bg-amber-600 text-gray-900 font-semibold px-5 py-2.5 rounded-lg transition-colors">
                        Book Now
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main>
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
