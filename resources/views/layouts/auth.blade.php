<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Cinema XXL - Authentication' }}</title>
    <meta name="description" content="Cinema XXL - Sign in or create an account">

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
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @livewireStyles

    <style>
        html {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-900 text-white antialiased">
    {{-- Background with Movie Poster Effect --}}
    <div class="fixed inset-0 z-0">
        <div class="absolute inset-0 bg-[url('https://image.tmdb.org/t/p/original/zOpe0eHsq0A2NvNyBbtT6sj53qV.jpg')] bg-cover bg-center opacity-20"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-900/95 to-gray-900"></div>
    </div>

    {{-- Main Content --}}
    <div class="relative z-10 min-h-screen flex flex-col">
        {{-- Header --}}
        <header class="py-6 px-4 md:px-8">
            <div class="container mx-auto">
                <a href="/" class="inline-flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-900" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm3 2h6v4H7V5zm8 8v2h1v-2h-1zm-2-2H7v4h6v-4zm2 0h1V9h-1v2zm1-4V5h-1v2h1zM5 5v2H4V5h1zm0 4H4v2h1V9zm-1 4h1v2H4v-2z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white">Cinema<span class="text-amber-500">XXL</span></span>
                </a>
            </div>
        </header>

        {{-- Auth Form Container --}}
        <main class="flex-1 flex items-center justify-center px-4 py-12">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="py-6 px-4 text-center text-gray-500 text-sm">
            <p>&copy; {{ date('Y') }} Cinema XXL. All rights reserved.</p>
        </footer>
    </div>

    @livewireScripts
</body>
</html>
