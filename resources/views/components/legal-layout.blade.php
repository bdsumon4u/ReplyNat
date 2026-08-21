<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name', 'ReplyNat') }} - Legal</title>

        @fonts
        <link rel="icon" type="image/png" href="{{ asset('imgs/icon.png') }}">

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <!-- Standard fallback for styling -->
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
            <script src="https://cdn.tailwindcss.com"></script>
            <script>
                tailwind.config = {
                    darkMode: 'media',
                    theme: {
                        extend: {
                            fontFamily: {
                                sans: ['Instrument Sans', 'sans-serif'],
                            },
                        },
                    },
                };
            </script>
        @endif
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] font-sans antialiased flex flex-col min-h-screen">
        <!-- Header -->
        <header class="w-full max-w-4xl mx-auto px-6 py-6 flex items-center justify-between border-b border-[#19140015] dark:border-[#3E3E3A]/50">
            <a href="/" class="flex items-center gap-2 font-semibold text-lg hover:opacity-80 transition-opacity">
                <img src="{{ asset('imgs/logo.png') }}" alt="{{ config('app.name', 'ReplyNat') }}" class="h-8 w-auto">
            </a>
            <nav class="flex items-center gap-4 text-sm">
                <a href="/" class="text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] transition-colors">
                    Home
                </a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-3 py-1 border border-[#19140035] dark:border-[#3E3E3A] hover:border-black dark:hover:border-white rounded-md transition-all text-xs font-medium">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] transition-colors">
                            Log in
                        </a>
                    @endauth
                @endif
            </nav>
        </header>

        <!-- Main Content -->
        <main class="flex-1 w-full max-w-4xl mx-auto px-6 py-12">
            <div class="bg-white dark:bg-[#161615] shadow-[0px_1px_3px_0px_rgba(0,0,0,0.05),inset_0px_0px_0px_1px_rgba(26,26,0,0.08)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed15] rounded-xl p-8 md:p-12">
                {{ $slot }}
            </div>
        </main>

        <!-- Footer -->
        <footer class="w-full max-w-4xl mx-auto px-6 py-8 border-t border-[#19140015] dark:border-[#3E3E3A]/50 text-center text-xs text-[#706f6c] dark:text-[#A1A09A] flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                &copy; {{ date('Y') }} {{ config('app.name', 'ReplyNat') }}. All rights reserved.
            </div>
            <div class="flex gap-4">
                <a href="{{ route('privacy-policy') }}" class="hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] transition-colors underline underline-offset-4">Privacy Policy</a>
                <a href="{{ route('terms-of-service') }}" class="hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] transition-colors underline underline-offset-4">Terms of Service</a>
                <a href="{{ route('data-deletion') }}" class="hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] transition-colors underline underline-offset-4">Data Deletion</a>
            </div>
        </footer>
    </body>
</html>
