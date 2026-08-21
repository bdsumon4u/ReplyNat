<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ReplyNat - Customer Conversation &amp; Workflow Automation Platform</title>

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
                    darkMode: 'class',
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
        <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="bg-[#FAFAFA] text-gray-900 font-sans antialiased flex flex-col min-h-screen selection:bg-indigo-500 selection:text-white transition-colors duration-300">
        <!-- Navigation Header -->
        <header class="sticky top-0 z-50 w-full border-b border-gray-200/80 bg-white/90 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
                <a href="#" class="flex items-center gap-2 font-bold text-xl tracking-tight text-gray-900 hover:opacity-80 transition-opacity">
                    <img src="{{ asset('imgs/logo.png') }}" alt="ReplyNat" class="h-8 w-auto">
                </a>
                <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-gray-600">
                    <a href="#features" class="hover:text-indigo-600 transition-colors">Features</a>
                    <a href="#workflow" class="hover:text-indigo-600 transition-colors">How It Works</a>
                    <a href="#pricing" class="hover:text-indigo-600 transition-colors">Pricing</a>
                    <a href="#faqs" class="hover:text-indigo-600 transition-colors">FAQs</a>
                </nav>
                <div class="flex items-center gap-4">
                    @if (Route::has('filament.app.auth.login'))
                        @auth
                            <a href="{{ route('filament.app.pages.dashboard') }}" class="px-4 py-2 text-xs font-semibold bg-indigo-600 text-white rounded-lg hover:bg-indigo-500 transition-all shadow-sm">
                                Dashboard-এ যান
                            </a>
                        @else
                            <a href="{{ route('filament.app.auth.login') }}" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors">
                                Log In
                            </a>
                            @if (Route::has('filament.app.auth.register'))
                                <a href="{{ route('filament.app.auth.register') }}" class="px-4 py-2 text-xs font-semibold bg-gray-900 text-white rounded-lg hover:bg-black transition-all shadow-sm">
                                    Sign Up
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative overflow-hidden pt-20 pb-16 lg:pt-32 lg:pb-24">
            <!-- Background Gradients & Ambient Blobs -->
            <div class="absolute inset-0 -z-10 flex justify-center overflow-hidden">
                <div class="w-[120rem] flex-none flex justify-end">
                    <div class="w-[80rem] flex-none bg-radial from-indigo-500/10 via-transparent to-transparent h-[40rem] -top-40 opacity-70"></div>
                </div>
            </div>
            <div class="absolute top-1/4 left-1/10 w-96 h-96 bg-purple-500/5 rounded-full filter blur-3xl pointer-events-none -z-10"></div>
            <div class="absolute top-1/3 right-1/10 w-96 h-96 bg-indigo-500/5 rounded-full filter blur-3xl pointer-events-none -z-10"></div>

            <div class="max-w-7xl mx-auto px-6 text-center">
                <!-- Beta Badge -->
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-600 mb-6 border border-indigo-200 shadow-xs">
                    <span>বর্তমানে <strong>Public Beta</strong> সংস্করণে</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-pulse"></span>
                </div>

                <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-gray-900 max-w-4xl mx-auto leading-none">
                    কাস্টমার <strong>Conversation</strong> ও <strong>Workflow</strong> <span class="bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent">অটোমেট করুন একদম <strong>Naturally</strong></span>
                </h1>
                
                <p class="mt-6 text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    আপনার <strong>Omnichannel Inboxes</strong> সিঙ্ক করুন, অ্যাডভান্সড <strong>Automation Workflows</strong> ট্রিগার করুন এবং একটি একক শক্তিশালী <strong>SaaS Platform</strong>-এ সহজেই টিম সাবস্ক্রিপশন ম্যানেজ করুন।
                </p>

                <!-- Hero CTAs -->
                <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ Route::has('filament.app.auth.register') ? route('filament.app.auth.register') : '#' }}" class="px-6 py-3 rounded-lg bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-500 transition-all shadow-md shadow-indigo-600/20 hover:-translate-y-0.5 duration-200">
                        <strong>Free Trial</strong> শুরু করুন
                    </a>
                    <a href="#workflow" class="px-6 py-3 rounded-lg border border-gray-300 bg-white text-gray-700 font-semibold text-sm hover:bg-gray-50 transition-all shadow-xs">
                        কীভাবে কাজ করে দেখুন &rarr;
                    </a>
                </div>

                <!-- CSS High-Fidelity Mockup Dashboard -->
                <div class="mt-16 border border-gray-200/90 rounded-2xl bg-white shadow-xl p-4 max-w-5xl mx-auto relative group">
                    <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500/5 to-violet-500/5 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4 text-xs text-gray-400">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-red-400"></span>
                            <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                            <span class="w-3 h-3 rounded-full bg-green-400"></span>
                            <span class="ml-2 font-mono select-none text-gray-500 font-medium">replynat.com</span>
                        </div>
                        <div class="flex gap-4">
                            <span class="text-gray-500">Status: <span class="text-green-600 font-semibold">Active</span></span>
                        </div>
                    </div>
                    <!-- Internal mockup layout -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-left">
                        <!-- Sidebar mockup -->
                        <div class="border-r border-gray-100 pr-4 space-y-4 hidden md:block">
                            <div class="h-8 bg-gray-100 rounded-md flex items-center text-xs px-2 py-2 font-semibold text-gray-700">
                                <span>Dashboard</span>
                            </div>
                            <div class="space-y-2">
                                <div class="h-6 bg-indigo-50 text-indigo-600 text-xs px-2 py-2 rounded flex items-center gap-2 font-medium">
                                    <span>⚙️</span> Workflow Integrations
                                </div>
                                <div class="h-6 hover:bg-gray-50 text-xs px-2 py-2 rounded flex items-center gap-2 transition-colors text-gray-600">
                                    <span>💬</span> Omnichannel Inboxes
                                </div>
                                <div class="h-6 hover:bg-gray-50 text-xs px-2 py-2 rounded flex items-center gap-2 transition-colors text-gray-600">
                                    <span>💳</span> Subscriptions &amp; Billing
                                </div>
                            </div>
                        </div>
                        <!-- Canvas / Center Mockup -->
                        <div class="md:col-span-2 space-y-4">
                            <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-200/60">
                                <div>
                                    <h4 class="text-xs font-bold uppercase text-gray-400">Active Pipeline</h4>
                                    <p class="text-sm font-semibold text-gray-800">Support Auto-Reply Workflow</p>
                                </div>
                                <span class="px-2.5 py-1 rounded bg-green-100 text-green-700 text-xs font-semibold">Running</span>
                            </div>
                            
                            <!-- Visual Connection Flow -->
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-6 p-4 bg-gray-50/60 rounded-lg border border-dashed border-gray-200">
                                <div class="flex flex-col items-center p-3 bg-white rounded-lg shadow-xs border border-gray-200 w-full sm:w-auto">
                                    <span class="text-2xl">💬</span>
                                    <span class="text-xs font-bold mt-1 text-gray-800">Omnichannel</span>
                                    <span class="text-[10px] text-gray-500">Webhook Trigger</span>
                                </div>
                                <div class="text-indigo-500 animate-pulse text-xl rotate-90 sm:rotate-0">&rarr;</div>
                                <div class="flex flex-col items-center p-3 bg-white rounded-lg shadow-xs border border-gray-200 w-full sm:w-auto">
                                    <span class="text-2xl">🤖</span>
                                    <span class="text-xs font-bold mt-1 text-gray-800">AI Node</span>
                                    <span class="text-[10px] text-gray-500">GPT-4 Analysis</span>
                                </div>
                                <div class="text-indigo-500 animate-pulse text-xl rotate-90 sm:rotate-0">&rarr;</div>
                                <div class="flex flex-col items-center p-3 bg-white rounded-lg shadow-xs border border-gray-200 w-full sm:w-auto">
                                    <span class="text-2xl">🔄</span>
                                    <span class="text-xs font-bold mt-1 text-gray-800">Workflow Execution</span>
                                    <span class="text-[10px] text-gray-500">Response Synced</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Integrations Strip -->
        <section class="border-y border-gray-200/80 py-10 bg-white">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-6">আপনার প্রিয় <strong>Enterprise Support Ecosystem</strong>-এর সাথে ইন্টিগ্রেশনের জন্য প্রস্তুত</h3>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-8 items-center justify-items-center opacity-70 grayscale hover:opacity-100 hover:grayscale-0 transition-all duration-300 text-gray-700">
                    <span class="text-sm font-bold tracking-wider">💬 Omnichannel Inboxes</span>
                    <span class="text-sm font-bold tracking-wider">🔄 Workflow Automations</span>
                    <span class="text-sm font-bold tracking-wider">🤖 AI Routing</span>
                    <span class="text-sm font-bold tracking-wider">👤 Google OAuth</span>
                    <span class="text-sm font-bold tracking-wider">👥 Facebook App</span>
                </div>
            </div>
        </section>

        <!-- Features Grid & Showcase -->
        <section id="features" class="py-20 lg:py-32">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-gray-900">
                        কাস্টমার <strong>Support</strong> অটোমেট করার জন্য যা কিছু প্রয়োজন
                    </h2>
                    <p class="mt-4 text-gray-600 leading-relaxed">
                        আলাদা আলাদা <strong>Server</strong>, <strong>API Hook</strong> এবং <strong>Billing</strong> ম্যানেজ করার ঝামেলা শেষ। ReplyNat আপনার <strong>Omnichannel Messaging System</strong> ও শক্তিশালী <strong>Workflow</strong>-এর মাঝে নিখুঁত সেতুবন্ধন তৈরি করে।
                    </p>
                </div>

                <!-- Showcase Container (Clean White / Light Style) -->
                <div class="w-full rounded-md p-3 sm:p-4 bg-white border border-gray-200/90 shadow-md relative overflow-hidden text-gray-900" style="background-image: radial-gradient(rgba(0,0,0,0.04) 1px, transparent 1px); background-size: 24px 24px;">
                    <!-- Ambient Glow Blobs -->
                    <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-500/5 rounded-full filter blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-purple-500/5 rounded-full filter blur-3xl pointer-events-none"></div>

                    <!-- Top Bar: Logo & Badges -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-100 pb-3 mb-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('imgs/logo.png') }}" alt="ReplyNat" class="h-8 w-auto">
                        </div>
                        <div class="flex flex-wrap gap-2 text-[11px] sm:text-xs font-semibold text-gray-700">
                            <span class="px-3.5 py-1.5 rounded-md bg-gray-50 border border-gray-200 shadow-xs flex items-center gap-1.5">
                                <span class="text-indigo-600 font-bold">✓</span> One login
                            </span>
                            <span class="px-3.5 py-1.5 rounded-md bg-gray-50 border border-gray-200 shadow-xs flex items-center gap-1.5">
                                <span class="text-indigo-600 font-bold">✓</span> One bill
                            </span>
                            <span class="px-3.5 py-1.5 rounded-md bg-gray-50 border border-gray-200 shadow-xs flex items-center gap-1.5">
                                <span class="text-indigo-600 font-bold">✓</span> One source of truth
                            </span>
                        </div>
                    </div>

                    <!-- Main Showcase Layout: Left Large Card + Right 3x3 Grid -->
                    <div class="flex flex-col lg:flex-row gap-4 items-stretch">

                        <!-- LEFT: Large Featured Card (~38% width) -->
                        <div class="w-full lg:w-[38%] p-6 sm:p-8 bg-gray-50/70 rounded-2xl border border-gray-200/90 shadow-sm hover:border-indigo-500/40 transition-all duration-300 flex flex-col justify-between relative overflow-hidden group">
                            <div class="absolute inset-0 bg-gradient-to-b from-indigo-500/5 via-transparent to-purple-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>

                            <div>
                                <!-- Top Tag -->
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-[10px] font-bold tracking-wider uppercase border border-indigo-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span> FEATURED
                                    </span>
                                </div>

                                <!-- Hexagonal Channel Connections Visualization -->
                                <div class="my-10 relative w-64 h-64 mx-auto flex items-center justify-center">
                                    <!-- Connection Lines SVG -->
                                    <svg class="absolute inset-0 w-full h-full text-indigo-400/40 pointer-events-none" viewBox="0 0 256 256">
                                        <!-- Animated Dotted Lines to 6 nodes -->
                                        <line x1="128" y1="128" x2="128" y2="36" stroke="currentColor" stroke-width="1.5" stroke-dasharray="3 3"><animate attributeName="stroke-dashoffset" from="0" to="6" dur="1s" repeatCount="indefinite"/></line>
                                        <line x1="128" y1="128" x2="210" y2="82" stroke="currentColor" stroke-width="1.5" stroke-dasharray="3 3"><animate attributeName="stroke-dashoffset" from="0" to="6" dur="1.2s" repeatCount="indefinite"/></line>
                                        <line x1="128" y1="128" x2="210" y2="174" stroke="currentColor" stroke-width="1.5" stroke-dasharray="3 3"><animate attributeName="stroke-dashoffset" from="0" to="6" dur="1.4s" repeatCount="indefinite"/></line>
                                        <line x1="128" y1="128" x2="128" y2="220" stroke="currentColor" stroke-width="1.5" stroke-dasharray="3 3"><animate attributeName="stroke-dashoffset" from="0" to="6" dur="1.1s" repeatCount="indefinite"/></line>
                                        <line x1="128" y1="128" x2="46" y2="174" stroke="currentColor" stroke-width="1.5" stroke-dasharray="3 3"><animate attributeName="stroke-dashoffset" from="0" to="6" dur="1.3s" repeatCount="indefinite"/></line>
                                        <line x1="128" y1="128" x2="46" y2="82" stroke="currentColor" stroke-width="1.5" stroke-dasharray="3 3"><animate attributeName="stroke-dashoffset" from="0" to="6" dur="1.5s" repeatCount="indefinite"/></line>
                                    </svg>

                                    <!-- Center AI Node -->
                                    <div class="relative w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-600 via-purple-600 to-pink-500 flex items-center justify-center text-white shadow-xl shadow-indigo-500/30 z-10 group-hover:scale-110 transition-transform duration-300">
                                        <span class="text-2xl">&#x2728;</span>
                                    </div>

                                    <!-- Outer Channel Nodes -->
                                    <!-- 1. WhatsApp (Top) -->
                                    <div class="absolute top-1 left-1/2 -translate-x-1/2 w-11 h-11 rounded-2xl bg-[#25D366] text-white flex items-center justify-center shadow-md shadow-[#25D366]/20 text-lg hover:scale-110 transition-transform">
                                        &#x1F4AC;
                                    </div>
                                    <!-- 2. Messenger (Top-Right) -->
                                    <div class="absolute top-12 right-1 w-11 h-11 rounded-2xl bg-gradient-to-tr from-[#0084FF] to-[#A033FF] text-white flex items-center justify-center shadow-md shadow-[#0084FF]/20 text-lg hover:scale-110 transition-transform">
                                        &#x24C2;&#xFE0F;
                                    </div>
                                    <!-- 3. Instagram (Bottom-Right) -->
                                    <div class="absolute bottom-12 right-1 w-11 h-11 rounded-2xl bg-gradient-to-tr from-[#FFDC80] via-[#E1306C] to-[#833AB4] text-white flex items-center justify-center shadow-md shadow-[#E1306C]/20 text-lg hover:scale-110 transition-transform">
                                        &#x1F4F8;
                                    </div>
                                    <!-- 4. Telegram (Bottom) -->
                                    <div class="absolute bottom-1 left-1/2 -translate-x-1/2 w-11 h-11 rounded-2xl bg-[#229ED9] text-white flex items-center justify-center shadow-md shadow-[#229ED9]/20 text-lg hover:scale-110 transition-transform">
                                        &#x2708;&#xFE0F;
                                    </div>
                                    <!-- 5. Email (Bottom-Left) -->
                                    <div class="absolute bottom-12 left-1 w-11 h-11 rounded-2xl bg-[#EA4335] text-white flex items-center justify-center shadow-md shadow-[#EA4335]/20 text-lg hover:scale-110 transition-transform">
                                        &#x2709;&#xFE0F;
                                    </div>
                                    <!-- 6. Voice / Phone (Top-Left) -->
                                    <div class="absolute top-12 left-1 w-11 h-11 rounded-2xl bg-[#F59E0B] text-white flex items-center justify-center shadow-md shadow-[#F59E0B]/20 text-lg hover:scale-110 transition-transform">
                                        &#x1F4DE;
                                    </div>
                                </div>

                                <!-- Card Content -->
                                <div class="mt-4">
                                    <div class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-1">
                                        02 &middot; THE ONE THAT SELLS
                                    </div>
                                    <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                                        One AI, every channel
                                    </h3>
                                    <p class="mt-3 text-xs sm:text-sm text-gray-600 leading-relaxed">
                                        WhatsApp, Messenger, Instagram, Telegram, SMS এবং Email—সবগুলো চ্যানেলকে যুক্ত করুন একটি একক <strong class="text-indigo-600">AI Core</strong>-এ যা প্রতিটি প্রশ্নের উত্তর দেবে, লিড কোয়ালিফাই করবে এবং অটোমেটিক বুকিং সম্পন্ন করবে।
                                    </p>
                                </div>
                            </div>

                            <!-- Bottom Status Pill -->
                            <div class="mt-3 flex items-center gap-2 text-xs text-green-600 font-semibold pt-4 border-t border-gray-200/70">
                                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                Always on &middot; replies in seconds
                            </div>
                        </div>

                        <!-- RIGHT: 3x3 Grid of 9 Cards (~62% width) with Hover Disclosure -->
                        <div class="w-full lg:w-[62%] grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">

                            <!-- CARD 01: Unified Inbox -->
                            <div class="p-4 bg-gray-50/70 hover:bg-white rounded-2xl border border-gray-200/90 hover:border-indigo-500/50 shadow-xs hover:shadow-md transition-all duration-300 flex flex-col group min-h-[170px] relative overflow-hidden cursor-pointer">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-9 h-9 rounded-xl bg-white border border-gray-200/80 text-indigo-600 flex items-center justify-center text-base shadow-xs group-hover:scale-105 transition-transform">
                                        &#x1F4E5;
                                    </div>
                                    <span class="font-mono text-xs font-semibold text-gray-400 group-hover:text-indigo-600 transition-colors">01</span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 mb-1.5">Unified Inbox</h4>
                                    <!-- Short text (default) -->
                                    <p class="text-[12px] text-gray-800 leading-relaxed font-semibold transition-all duration-200 mb-1.5">
                                        সব চ্যানেল একই ইনবক্সে সিঙ্কড।
                                    </p>
                                    <!-- Detailed text (on hover) -->
                                    <p class="text-[11px] text-gray-700 bg-indigo-50/90 p-1.5 rounded-md border border-indigo-200/70 leading-relaxed transition-all duration-200">
                                        WhatsApp, Messenger, Instagram, Email, SMS এবং লাইভ চ্যাটের প্রতিটি মেসেজ একটি সেন্ট্রালাইজড ইনবক্সে চলে আসে—সম্পূর্ণ কাস্টমার হিস্ট্রি সহ।
                                    </p>
                                </div>
                            </div>

                            <!-- CARD 03: Pipeline & Leads -->
                            <div class="p-4 bg-gray-50/70 hover:bg-white rounded-2xl border border-gray-200/90 hover:border-indigo-500/50 shadow-xs hover:shadow-md transition-all duration-300 flex flex-col group min-h-[170px] relative overflow-hidden cursor-pointer">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-9 h-9 rounded-xl bg-white border border-gray-200/80 text-gray-700 flex items-center justify-center text-base shadow-xs group-hover:scale-105 transition-transform">
                                        &#x1F3AF;
                                    </div>
                                    <span class="font-mono text-xs font-semibold text-gray-400 group-hover:text-indigo-600 transition-colors">03</span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 mb-1.5">Pipeline &amp; Leads</h4>
                                    <!-- Short text (default) -->
                                    <p class="text-[12px] text-gray-800 leading-relaxed font-semibold transition-all duration-200 mb-1.5">
                                        প্রতিটি ডিল ও লিড থাকবে ট্র্যাকিংয়ে।
                                    </p>
                                    <!-- Detailed text (on hover) -->
                                    <p class="text-[11px] text-gray-700 bg-indigo-50/90 p-1.5 rounded-md border border-indigo-200/70 leading-relaxed transition-all duration-200">
                                        কনভারসেশন চলাকালীন সম্ভাব্য কাস্টমারদের শনাক্ত করে স্বয়ংক্রিয়ভাবে সেলস পাইপলাইনে যুক্ত করুন এবং ফলো-আপ নিশ্চিত করুন।
                                    </p>
                                </div>
                            </div>

                            <!-- CARD 04: Voice & Calls -->
                            <div class="p-4 bg-gray-50/70 hover:bg-white rounded-2xl border border-gray-200/90 hover:border-indigo-500/50 shadow-xs hover:shadow-md transition-all duration-300 flex flex-col group min-h-[170px] relative overflow-hidden cursor-pointer">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-9 h-9 rounded-xl bg-white border border-gray-200/80 text-gray-700 flex items-center justify-center text-base shadow-xs group-hover:scale-105 transition-transform">
                                        &#x1F4DE;
                                    </div>
                                    <span class="font-mono text-xs font-semibold text-gray-400 group-hover:text-indigo-600 transition-colors">04</span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 mb-1.5">Voice &amp; Calls</h4>
                                    <!-- Short text (default) -->
                                    <p class="text-[12px] text-gray-800 leading-relaxed font-semibold transition-all duration-200 mb-1.5">
                                        হট লিডগুলোতে তাৎক্ষণিক কল করুন।
                                    </p>
                                    <!-- Detailed text (on hover) -->
                                    <p class="text-[11px] text-gray-700 bg-indigo-50/90 p-1.5 rounded-md border border-indigo-200/70 leading-relaxed transition-all duration-200">
                                        যেসব কাস্টমার সরাসরি কথা বলতে চান তাদের সাথে ব্রাউজার থেকেই ওয়ান-ক্লিক ভয়েস কল কানেক্ট ও অডিও ট্র্যাকিং নিশ্চিত করুন।
                                    </p>
                                </div>
                            </div>

                            <!-- CARD 05: Broadcasts -->
                            <div class="p-4 bg-gray-50/70 hover:bg-white rounded-2xl border border-gray-200/90 hover:border-indigo-500/50 shadow-xs hover:shadow-md transition-all duration-300 flex flex-col group min-h-[170px] relative overflow-hidden cursor-pointer">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-9 h-9 rounded-xl bg-white border border-gray-200/80 text-gray-700 flex items-center justify-center text-base shadow-xs group-hover:scale-105 transition-transform">
                                        &#x1F4E3;
                                    </div>
                                    <span class="font-mono text-xs font-semibold text-gray-400 group-hover:text-indigo-600 transition-colors">05</span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 mb-1.5">Broadcasts</h4>
                                    <!-- Short text (default) -->
                                    <p class="text-[12px] text-gray-800 leading-relaxed font-semibold transition-all duration-200 mb-1.5">
                                        এক ক্লিকে হাজারো কাস্টমারকে মেসেজ।
                                    </p>
                                    <!-- Detailed text (on hover) -->
                                    <p class="text-[11px] text-gray-700 bg-indigo-50/90 p-1.5 rounded-md border border-indigo-200/70 leading-relaxed transition-all duration-200">
                                        অফার, ডিসকাউন্ট বা গুরুত্বপূর্ণ আপডেট এক ক্লিকে টার্গেটেড অডিয়েন্সের WhatsApp ও মেসেঞ্জারে ব্রডকাস্ট ক্যাম্পেইন হিসেবে পাঠান।
                                    </p>
                                </div>
                            </div>

                            <!-- CARD 06: Automation -->
                            <div class="p-4 bg-gray-50/70 hover:bg-white rounded-2xl border border-gray-200/90 hover:border-indigo-500/50 shadow-xs hover:shadow-md transition-all duration-300 flex flex-col group min-h-[170px] relative overflow-hidden cursor-pointer">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-9 h-9 rounded-xl bg-white border border-gray-200/80 text-gray-700 flex items-center justify-center text-base shadow-xs group-hover:scale-105 transition-transform">
                                        &#x2699;&#xFE0F;
                                    </div>
                                    <span class="font-mono text-xs font-semibold text-gray-400 group-hover:text-indigo-600 transition-colors">06</span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 mb-1.5">Automation</h4>
                                    <!-- Short text (default) -->
                                    <p class="text-[12px] text-gray-800 leading-relaxed font-semibold transition-all duration-200 mb-1.5">
                                        আপনার অনুপস্থিতিতেও ২৪/৭ সাপোর্ট।
                                    </p>
                                    <!-- Detailed text (on hover) -->
                                    <p class="text-[11px] text-gray-700 bg-indigo-50/90 p-1.5 rounded-md border border-indigo-200/70 leading-relaxed transition-all duration-200">
                                        স্মার্ট ওয়ার্কফ্লো নোড ও কন্ডিশনাল লজিকের মাধ্যমে মানুষের সাহায্য ছাড়াই দিন-রাত ২৪ ঘণ্টা স্বয়ংক্রিয় সার্ভিস নিশ্চিত করুন।
                                    </p>
                                </div>
                            </div>

                            <!-- CARD 07: Templates -->
                            <div class="p-4 bg-gray-50/70 hover:bg-white rounded-2xl border border-gray-200/90 hover:border-indigo-500/50 shadow-xs hover:shadow-md transition-all duration-300 flex flex-col group min-h-[170px] relative overflow-hidden cursor-pointer">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-9 h-9 rounded-xl bg-white border border-gray-200/80 text-gray-700 flex items-center justify-center text-base shadow-xs group-hover:scale-105 transition-transform">
                                        &#x1F4C4;
                                    </div>
                                    <span class="font-mono text-xs font-semibold text-gray-400 group-hover:text-indigo-600 transition-colors">07</span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 mb-1.5">Templates</h4>
                                    <!-- Short text (default) -->
                                    <p class="text-[12px] text-gray-800 leading-relaxed font-semibold transition-all duration-200 mb-1.5">
                                        দ্রুত ও সামঞ্জস্যপূর্ণ রেসপন্স টেমপ্লেট।
                                    </p>
                                    <!-- Detailed text (on hover) -->
                                    <p class="text-[11px] text-gray-700 bg-indigo-50/90 p-1.5 rounded-md border border-indigo-200/70 leading-relaxed transition-all duration-200">
                                        বারবার আসা সাধারণ প্রশ্নগুলোর দ্রুত ও নির্ভুল উত্তরের জন্য প্রি-বিল্ট কাস্টম টেমপ্লেট ও ডাইনামিক ফিল্ড ব্যবহার করুন।
                                    </p>
                                </div>
                            </div>

                            <!-- CARD 08: Reports & ROI -->
                            <div class="p-4 bg-gray-50/70 hover:bg-white rounded-2xl border border-gray-200/90 hover:border-indigo-500/50 shadow-xs hover:shadow-md transition-all duration-300 flex flex-col group min-h-[170px] relative overflow-hidden cursor-pointer">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-9 h-9 rounded-xl bg-white border border-gray-200/80 text-gray-700 flex items-center justify-center text-base shadow-xs group-hover:scale-105 transition-transform">
                                        &#x1F4C8;
                                    </div>
                                    <span class="font-mono text-xs font-semibold text-gray-400 group-hover:text-indigo-600 transition-colors">08</span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 mb-1.5">Reports &amp; ROI</h4>
                                    <!-- Short text (default) -->
                                    <p class="text-[12px] text-gray-800 leading-relaxed font-semibold transition-all duration-200 mb-1.5">
                                        ক্যাম্পেইন পারফরম্যান্স ও এনালিটিক্স।
                                    </p>
                                    <!-- Detailed text (on hover) -->
                                    <p class="text-[11px] text-gray-700 bg-indigo-50/90 p-1.5 rounded-md border border-indigo-200/70 leading-relaxed transition-all duration-200">
                                        কোন চ্যানেল বা ক্যাম্পেইন থেকে বেশি কনভার্সন ও সেলস আসছে তা নিখুঁত গ্রাফ এবং আরওআই মেট্রিক্সের মাধ্যমে পর্যবেক্ষণ করুন।
                                    </p>
                                </div>
                            </div>

                            <!-- CARD 09: Team & Routing -->
                            <div class="p-4 bg-gray-50/70 hover:bg-white rounded-2xl border border-gray-200/90 hover:border-indigo-500/50 shadow-xs hover:shadow-md transition-all duration-300 flex flex-col group min-h-[170px] relative overflow-hidden cursor-pointer">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-9 h-9 rounded-xl bg-white border border-gray-200/80 text-gray-700 flex items-center justify-center text-base shadow-xs group-hover:scale-105 transition-transform">
                                        &#x1F465;
                                    </div>
                                    <span class="font-mono text-xs font-semibold text-gray-400 group-hover:text-indigo-600 transition-colors">09</span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 mb-1.5">Team &amp; Routing</h4>
                                    <!-- Short text (default) -->
                                    <p class="text-[12px] text-gray-800 leading-relaxed font-semibold transition-all duration-200 mb-1.5">
                                        টিম মেম্বারদের মাঝে স্মার্ট ডিস্ট্রিবিউশন।
                                    </p>
                                    <!-- Detailed text (on hover) -->
                                    <p class="text-[11px] text-gray-700 bg-indigo-50/90 p-1.5 rounded-md border border-indigo-200/70 leading-relaxed transition-all duration-200">
                                        কাস্টমারের ভাষা, ডিপার্টমেন্ট ও স্পেশালাইজেশনের ওপর ভিত্তি করে মেসেজগুলো স্বয়ংক্রিয়ভাবে সঠিক এজেন্টের কাছে পৌঁছে যায়।
                                    </p>
                                </div>
                            </div>

                            <!-- CARD 10: Integrations & API -->
                            <div class="p-4 bg-gray-50/70 hover:bg-white rounded-2xl border border-gray-200/90 hover:border-indigo-500/50 shadow-xs hover:shadow-md transition-all duration-300 flex flex-col group min-h-[170px] relative overflow-hidden cursor-pointer">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-9 h-9 rounded-xl bg-white border border-gray-200/80 text-gray-700 flex items-center justify-center text-base shadow-xs group-hover:scale-105 transition-transform font-mono font-bold">
                                        { }
                                    </div>
                                    <span class="font-mono text-xs font-semibold text-gray-400 group-hover:text-indigo-600 transition-colors">10</span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 mb-1.5">Integrations &amp; API</h4>
                                    <!-- Short text (default) -->
                                    <p class="text-[12px] text-gray-800 leading-relaxed font-semibold transition-all duration-200 mb-1.5">
                                        আপনার নিজস্ব সফটওয়্যার কানেক্ট।
                                    </p>
                                    <!-- Detailed text (on hover) -->
                                    <p class="text-[11px] text-gray-700 bg-indigo-50/90 p-1.5 rounded-md border border-indigo-200/70 leading-relaxed transition-all duration-200">
                                        বিদ্যমান CRM, ই-কমার্স ডেটাবেস ও কাস্টম ইন্টারনাল সফটওয়্যারের সাথে ওপেন API ও ওয়েবহুকের মাধ্যমে প্লাগ-ইন করুন।
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Showcase Bottom Strip -->
                    <div class="mt-8 text-center text-xs text-gray-500 border-t border-gray-100 pt-6">
                        Replaces your <s class="text-gray-400">inbox app</s> <s class="text-gray-400">CRM</s> <s class="text-gray-400">dialer</s> <s class="text-gray-400">broadcast tool</s> <s class="text-gray-400">chatbot builder</s> <s class="text-gray-400">analytics suite</s> &rarr; <span class="font-bold text-gray-900">one platform.</span>
                    </div>
                </div>

            </div>
        </section>

        <!-- How It Works Flow Diagram -->
        <section id="workflow" class="py-20 bg-gray-50/60 border-y border-gray-200/80">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900">কীভাবে <strong>ReplyNat</strong> আপনার কাস্টমার <strong>Support</strong> অটোমেট করে</h2>
                    <p class="mt-4 text-gray-600">দেখুন কীভাবে মেসেজগুলো ইন্টারসেপ্ট হয়, <strong>Automation Workflows</strong>-এর মধ্য দিয়ে রান করে এবং প্রাকৃতিকভাবে প্রসেসড হয়।</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative">
                    <!-- Step 1 -->
                    <div class="flex flex-col items-center text-center p-6 bg-white rounded-2xl border border-gray-200/80 shadow-xs">
                        <div class="w-12 h-12 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center mb-4 shadow-sm shadow-indigo-500/20">1</div>
                        <h4 class="text-base font-bold text-gray-900 mb-2">User Connects Profiles</h4>
                        <p class="text-sm text-gray-600">গুগল বা ফেসবুকের মাধ্যমে <strong>OAuth Tokens</strong> রেজিস্টার্ড হয় এবং ইনবক্সগুলো মুহূর্তেই কানেক্ট হয়ে যায়।</p>
                    </div>
                    <!-- Step 2 -->
                    <div class="flex flex-col items-center text-center p-6 bg-white rounded-2xl border border-gray-200/80 shadow-xs">
                        <div class="w-12 h-12 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center mb-4 shadow-sm shadow-indigo-500/20">2</div>
                        <h4 class="text-base font-bold text-gray-900 mb-2">Webhooks Trigger Workflows</h4>
                        <p class="text-sm text-gray-600">আগত টেক্সট সরাসরি একটি <strong>Automation Endpoint</strong>-এ রিকোয়েস্ট ট্রিগার করে লজিক এক্সিকিউট করে এবং ভ্যারিয়েবল রেফারেন্স করে।</p>
                    </div>
                    <!-- Step 3 -->
                    <div class="flex flex-col items-center text-center p-6 bg-white rounded-2xl border border-gray-200/80 shadow-xs">
                        <div class="w-12 h-12 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center mb-4 shadow-sm shadow-indigo-500/20">3</div>
                        <h4 class="text-base font-bold text-gray-900 mb-2">Immediate Auto-Replies</h4>
                        <p class="text-sm text-gray-600">ReplyNat প্রস্তুতকৃত উত্তরটি সরাসরি আপনার <strong>Omnichannel Inbox</strong>-এ পুশ করে, ফলে টিকিটটি রিয়েল-টাইমে সলভ হয়ে যায়।</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-20 lg:py-32 bg-white">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900">Choose Your Subscription Package</h2>
                    <p class="mt-4 text-gray-600">আপনার বিজনেসের প্রবৃদ্ধি অনুযায়ী ফ্লেক্সিবল প্ল্যান। যেকোনো সময় আপগ্রেড বা পরিবর্তন করতে পারবেন।</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 items-stretch mx-auto">
                    @foreach (config('subscription.plans', []) as $key => $plan)
                        @php
                            $isFeatured = $plan['featured'] ?? false;
                            $trialDays = config('subscription.trial_period_days', 21);
                        @endphp
                        
                        <div class="p-8 bg-white rounded-2xl flex flex-col justify-between relative transition-all duration-300 hover:-translate-y-1 hover:shadow-lg {{ $isFeatured ? 'border-2 border-indigo-600 shadow-xl ring-4 ring-indigo-500/10' : 'border border-gray-200 shadow-sm hover:border-indigo-500/30' }}">
                            @if ($isFeatured)
                                <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full bg-indigo-600 text-white font-bold text-[10px] tracking-wider uppercase shadow-xs">
                                    Most Popular
                                </div>
                            @endif
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $plan['name'] }}</h3>
                                <p class="mt-1 text-xs text-gray-500">{{ $plan['description'] ?? 'Premium automation plan' }}</p>
                                
                                <div class="mt-3 flex flex-col">
                                    <div class="flex items-baseline">
                                        <span class="text-2xl font-extrabold text-gray-900">
                                            {{ $plan['currency'] }} {{ number_format($plan['price']) }}
                                        </span>
                                        <span class="text-xs text-gray-500 ml-1">
                                            / {{ $plan['invoice_period'] }} {{ $plan['invoice_period'] > 1 ? Str::plural($plan['invoice_interval'], $plan['invoice_period']) : $plan['invoice_interval'] }}
                                        </span>
                                    </div>
                                    @if ($trialDays > 0)
                                        <span class="mt-2 inline-flex items-center text-[11px] font-medium text-indigo-600">
                                            ✨ {{ $trialDays }} Days Free Trial Included
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-6 border-t border-gray-100 pt-3">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4 font-semibold underline underline-offset-4">Included Features</h4>
                                    <ul class="space-y-3 text-xs text-gray-600 text-left">
                                        @foreach (config('subscription.features', []) as $feature)
                                            <li class="flex items-center gap-2">
                                                <span class="text-green-600 font-bold">✓</span>
                                                <span>{{ $feature }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="mt-8">
                                <a href="{{ Route::has('filament.app.auth.register') ? route('filament.app.auth.register') : '#' }}" 
                                   class="block text-center py-2.5 px-4 rounded-lg font-semibold text-xs transition-all {{ $isFeatured ? 'bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20' : 'bg-gray-50 text-gray-900 border border-gray-200 hover:bg-gray-100' }}">
                                    Sign Up
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- FAQs Accordion -->
        <section id="faqs" class="py-20 bg-gray-50/60 border-t border-gray-200/80" x-data="{ activeFaq: null }">
            <div class="max-w-4xl mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900">Frequently Asked Questions (FAQs)</h2>
                    <p class="mt-4 text-gray-600"><strong>Technical</strong> এবং <strong>Billing</strong> সম্পর্কিত সাধারণ প্রশ্নগুলোর সহজ উত্তর।</p>
                </div>

                <div class="space-y-4">
                    <!-- FAQ 1 -->
                    <div class="border border-gray-200 rounded-xl bg-white shadow-xs">
                        <button type="button" 
                                @click="activeFaq = activeFaq === 1 ? null : 1"
                                class="w-full flex items-center justify-between p-6 text-left font-bold text-gray-900 focus:outline-none">
                            <span><strong>Omnichannel Inbox</strong> কীভাবে <strong>Workflows</strong>-এর সাথে সিঙ্ক করে?</span>
                            <span class="text-xl text-gray-500" x-text="activeFaq === 1 ? '−' : '+'">+</span>
                        </button>
                        <div x-show="activeFaq === 1" x-collapse>
                            <div class="p-6 pt-0 text-sm text-gray-600 leading-relaxed border-t border-gray-100">
                                ReplyNat সরাসরি আপনার <strong>Omnichannel Inboxes</strong>-এ ব্যাকগ্রাউন্ড <strong>Webhooks</strong> রেজিস্টার করে। যখনই কোনো নতুন মেসেজ আসে, আমরা সাথে সাথে সেই <strong>Data</strong> আপনার রানিং <strong>Automation Workflows</strong>-এ পাঠিয়ে দিই প্রসেস করার জন্য।
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="border border-gray-200 rounded-xl bg-white shadow-xs">
                        <button type="button" 
                                @click="activeFaq = activeFaq === 2 ? null : 2"
                                class="w-full flex items-center justify-between p-6 text-left font-bold text-gray-900 focus:outline-none">
                            <span>আমি কি আমার নিজস্ব <strong>AI API Keys</strong> ব্যবহার করতে পারব?</span>
                            <span class="text-xl text-gray-500" x-text="activeFaq === 2 ? '−' : '+'">+</span>
                        </button>
                        <div x-show="activeFaq === 2" x-collapse>
                            <div class="p-6 pt-0 text-sm text-gray-600 leading-relaxed border-t border-gray-100">
                                হ্যাঁ, অবশ্যই! আপনি আপনার <strong>Workflow Parameters</strong>-এ কাস্টম <strong>OpenAI</strong>, <strong>Anthropic</strong> অথবা <strong>Gemini API Keys</strong> কনফিগার করতে পারবেন, যা আপনাকে প্রম্পট এবং টোকেন খরচের ওপর পূর্ণ নিয়ন্ত্রণ দেবে।
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="border border-gray-200 rounded-xl bg-white shadow-xs">
                        <button type="button" 
                                @click="activeFaq = activeFaq === 3 ? null : 3"
                                class="w-full flex items-center justify-between p-6 text-left font-bold text-gray-900 focus:outline-none">
                            <span><strong>Self-hosted</strong> ব্যবহারের কোনো সুযোগ আছে কি?</span>
                            <span class="text-xl text-gray-500" x-text="activeFaq === 3 ? '−' : '+'">+</span>
                        </button>
                        <div x-show="activeFaq === 3" x-collapse>
                            <div class="p-6 pt-0 text-sm text-gray-600 leading-relaxed border-t border-gray-100">
                                বর্তমানে ReplyNat একটি হোস্টেড <strong>SaaS Solution</strong> হিসেবে কাজ করে। তবে আমাদের মডুলার প্লাগইনগুলো ব্যবহার করে ডেভেলপাররা চাইলে লোকাল <strong>Workflows</strong> বা কাস্টম মেসেজিং প্ল্যাটফর্মের সাথে এটি ইন্টিগ্রেট করতে পারবেন।
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Footer Section -->
        <section class="py-20 bg-indigo-600 text-white text-center relative overflow-hidden">
            <div class="max-w-4xl mx-auto px-6 relative z-10">
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">আপনার কাস্টমার <strong>Support Automation</strong>-কে আরও গতিশীল করতে প্রস্তুত তো?</h2>
                <p class="mt-4 text-indigo-100 max-w-xl mx-auto text-sm sm:text-base">আজই <strong>ReplyNat</strong>-এ <strong>Sign Up</strong> করুন। কোনো কোডিং ছাড়াই ৫ মিনিটেরও কম সময়ে সেটআপ করুন।</p>
                <div class="mt-8">
                    <a href="{{ Route::has('filament.app.auth.register') ? route('filament.app.auth.register') : '#' }}" class="px-6 py-3 rounded-lg bg-white text-indigo-600 font-bold hover:bg-gray-100 transition-colors shadow-lg">
                        Get Started For Free
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="w-full border-t border-gray-200 bg-white py-12">
            <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="space-y-4">
                    <a href="#" class="flex items-center gap-2 font-bold text-xl tracking-tight text-gray-900 hover:opacity-80 transition-opacity">
                        <img src="{{ asset('imgs/logo.png') }}" alt="ReplyNat" class="h-8 w-auto">
                    </a>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        বাংলাদেশের ব্যবসাগুলোর জন্য <strong>Conversation Synchronization</strong> এবং <strong>Automation Environment</strong> প্রদান করছে।
                    </p>
                </div>
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Product</h5>
                    <ul class="space-y-2 text-xs text-gray-600">
                        <li><a href="#features" class="hover:underline">Features</a></li>
                        <li><a href="#workflow" class="hover:underline">How It Works</a></li>
                        <li><a href="#pricing" class="hover:underline">Pricing</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Support</h5>
                    <ul class="space-y-2 text-xs text-gray-600">
                        <li><a href="#faqs" class="hover:underline">FAQs</a></li>
                        <li><a href="mailto:support@replynat.com" class="hover:underline">Contact Email</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Legal</h5>
                    <ul class="space-y-2 text-xs text-gray-600">
                        <li><a href="{{ route('privacy-policy') }}" class="hover:underline">Privacy Policy</a></li>
                        <li><a href="{{ route('terms-of-service') }}" class="hover:underline">Terms of Service</a></li>
                        <li><a href="{{ route('data-deletion') }}" class="hover:underline">Data Deletion</a></li>
                    </ul>
                </div>
            </div>
            <div class="max-w-7xl mx-auto px-6 mt-12 pt-6 border-t border-gray-100 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} ReplyNat. All rights reserved. Built with passion and <a target="_blank" class="text-red-500 font-bold underline" href="https://hotash.tech">Hotash Tech</a>.
            </div>
        </footer>
    </body>
</html>
