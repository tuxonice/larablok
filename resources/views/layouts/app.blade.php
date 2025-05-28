<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LaraBlok') }} - @yield('title', 'Blog')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('home') }}" class="text-2xl font-bold text-blue-600">
                                {{ config('app.name', 'LaraBlok') }}
                            </a>
                        </div>
                        <div class="hidden md:ml-10 md:flex md:space-x-8">
                            <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('home') && !request()->query('category') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                Home
                            </a>
                            @if(request()->query('category'))
                                <span class="inline-flex items-center px-1 pt-1 border-b-2 border-blue-500 text-gray-900">
                                    Category: {{ request()->query('category') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center">
                        <a href="https://storyblok.com" target="_blank" class="flex items-center text-sm text-gray-500 hover:text-gray-700">
                            <span class="mr-2">Powered by Storyblok</span>
                            <svg width="24" height="24" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg">
                                <path d="M36.63,15.45c0,.57-.47,1.04-1.04,1.04h-4.15v-6.25h4.15c.57,0,1.04,.47,1.04,1.04v4.17Z" fill="#09b3af"/>
                                <path d="M36.63,28.96c0,.57-.47,1.04-1.04,1.04h-4.15v-6.25h4.15c.57,0,1.04,.47,1.04,1.04v4.17Z" fill="#09b3af"/>
                                <path d="M36.63,42.47c0,.57-.47,1.04-1.04,1.04h-4.15v-6.25h4.15c.57,0,1.04,.47,1.04,1.04v4.17Z" fill="#09b3af"/>
                                <path d="M24.96,49.76h-4.17c-.57,0-1.04-.47-1.04-1.04v-37.71c0-.57,.47-1.04,1.04-1.04h4.17v39.79Z" fill="#09b3af"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-700 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-4xl font-bold">{{ config('app.name', 'LaraBlok') }}</h1>
                <p class="mt-2 text-xl">A modern blog powered by Laravel, Livewire, and Storyblok CMS</p>
            </div>
        </div>

        <!-- Page Content -->
        <main class="py-10 flex-grow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white shadow-inner mt-auto">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="text-gray-500 text-sm">
                        &copy; {{ date('Y') }} {{ config('app.name', 'LaraBlok') }}. All rights reserved.
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="https://github.com/tuxonice/larablok" class="text-gray-500 hover:text-gray-700">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                    <div class="text-gray-500 text-sm">
                        Powered by <a href="https://laravel.com" class="text-blue-600 hover:text-blue-800">Laravel</a>, 
                        <a href="https://livewire.laravel.com" class="text-blue-600 hover:text-blue-800">Livewire</a>,
                        and <a href="https://storyblok.com" class="text-blue-600 hover:text-blue-800">Storyblok</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
