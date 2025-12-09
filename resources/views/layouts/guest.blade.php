<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />
        
        <script src="https://unpkg.com/lucide@latest"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="h-full font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-indigo-50 via-white to-purple-50 p-6">
            
            <div class="mb-8 text-center">
                <a href="/" class="flex flex-col items-center gap-2 group">
                    <div class="bg-indigo-600 p-3 rounded-xl shadow-lg shadow-indigo-200 group-hover:scale-110 transition-transform duration-200">
                        <i data-lucide="sparkles" class="w-8 h-8 text-white fill-indigo-400"></i>
                    </div>
                    <span class="text-2xl font-bold text-gray-800 tracking-tight mt-3">AI Writer</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white shadow-xl shadow-gray-100 border border-gray-100 rounded-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
                
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} AI Writer Assistant. All rights reserved.
            </div>
        </div>
        
        <script>
            lucide.createIcons();
        </script>
    </body>
</html>