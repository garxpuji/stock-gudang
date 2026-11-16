<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Logistik Gudang') - Gayuh Media Informatika</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|jetbrains-mono:400,500" rel="stylesheet" />

    <!-- Tailwind & Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }

        .font-mono-sn {
            font-family: 'JetBrains Mono', monospace;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .print-only {
                display: block !important;
            }

            body {
                background: white !important;
            }
        }
    </style>
    @stack('styles')
</head>

<body class="h-full text-slate-800 antialiased selection:bg-emerald-500 selection:text-white" x-data="{ sidebarOpen: false }" @keydown.window.escape="sidebarOpen = false">

    <div class="min-h-screen flex flex-col lg:flex-row">

        <!-- MOBILE TOPBAR -->
        <header class="lg:hidden sticky top-0 z-40 flex items-center justify-between bg-white border-b border-slate-200 px-3 py-2 no-print gap-2">
            <button type="button" @click="sidebarOpen = true" class="inline-flex items-center gap-1.5 px-2.5 py-2 rounded-lg text-slate-700 hover:text-emerald-700 hover:bg-slate-100 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 min-h-[44px] shrink-0 cursor-pointer" aria-label="Buka Menu Navigasi">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <span class="text-xs font-bold">Menu</span>
            </button>

            <!-- Search input on mobile -->
            <form action="{{ route('products.index') }}" method="GET" class="flex-1 min-w-0 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari SKU / SN / Barang..." class="w-full pl-8 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-500 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500 min-h-[40px]">
                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-500">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </form>

            <a href="{{ route('stock-out.create') }}" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition-colors min-h-[44px] shrink-0 focus:outline-hidden focus:ring-2 focus:ring-emerald-500" title="Keluar Barang">
                <span>+ Keluar</span>
            </a>
        </header>

        <!-- MOBILE SLIDE-OUT DRAWER -->
        <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true" aria-label="Menu Navigasi">
            <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/50" @click="sidebarOpen = false"></div>

            <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative flex-1 flex flex-col max-w-xs w-full bg-white border-r border-slate-200 h-full">
                <div class="flex items-center justify-between px-4 py-3.5 border-b border-slate-200">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-bold shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="text-sm font-bold text-slate-900 block">Gayuh Media Informatika</span>
                            <span class="text-xs text-slate-600 block">Inventaris Gudang</span>
                        </div>
                    </div>
                    <button type="button" @click="sidebarOpen = false" class="p-2 text-slate-500 hover:text-slate-800 rounded-lg min-h-[44px] min-w-[44px] flex items-center justify-center cursor-pointer focus:outline-hidden focus:ring-2 focus:ring-emerald-500" aria-label="Tutup Menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto px-3 py-4 space-y-4">
                    @include('layouts.navigation-links')
                </div>

                <div class="p-4 border-t border-slate-200 bg-slate-50">
                    @include('layouts.user-footer')
                </div>
            </div>
        </div>

        <!-- DESKTOP SIDEBAR -->
        <aside class="hidden lg:flex lg:flex-col w-64 shrink-0 bg-white border-r border-slate-200 sticky top-0 h-screen overflow-y-auto no-print">
            <!-- Brand Header -->
            <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-bold shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-sm font-bold text-slate-900 leading-tight">Gayuh Media Informatika</h1>
                    <span class="text-xs text-slate-600 block mt-0.5">Inventaris & Logistik Gudang</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-3 py-4 space-y-4">
                @include('layouts.navigation-links')
            </div>

            <!-- User Footer -->
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                @include('layouts.user-footer')
            </div>
        </aside>

        <!-- MAIN WORKSPACE -->
        <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">

            <!-- DESKTOP TOPBAR -->
            <header class="hidden lg:flex items-center justify-between bg-white border-b border-slate-200 px-8 py-3 sticky top-0 z-20 no-print">
                <!-- Search bar -->
                <form action="{{ route('products.index') }}" method="GET" class="w-full max-w-md relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari SKU, Serial Number, atau Nama Barang..." class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-500 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors min-h-[44px]">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </form>

                <div class="flex items-center gap-2.5">
                    <a href="{{ route('stock-in.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 min-h-[44px] text-xs font-semibold rounded-lg bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 transition-colors">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Barang Masuk</span>
                    </a>

                    <a href="{{ route('stock-out.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 min-h-[44px] text-xs font-semibold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 active:bg-emerald-800 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Keluar Barang</span>
                    </a>
                </div>
            </header>

            <!-- FLASH ALERTS -->
            <div class="px-4 lg:px-8 pt-4 no-print">
                @if(session('success'))
                <div class="flex items-center justify-between p-4 mb-3 text-sm text-emerald-900 rounded-xl bg-emerald-50 border border-emerald-200" role="alert">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 p-2 rounded-lg min-h-[44px] min-w-[44px] inline-flex items-center justify-center" aria-label="Tutup Notifikasi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                @endif

                @if(session('error') || $errors->any())
                <div class="p-4 mb-3 text-sm text-rose-900 rounded-xl bg-rose-50 border border-rose-200" role="alert">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <span class="font-semibold block mb-1">{{ session('error') ?? 'Periksa kembali isian formulir:' }}</span>
                            @if($errors->any())
                            <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-800">
                                @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                        <button type="button" @click="$el.closest('.rounded-xl').remove()" class="text-rose-700 hover:text-rose-900 p-2 rounded-lg min-h-[44px] min-w-[44px] inline-flex items-center justify-center" aria-label="Tutup Pesan Kesalahan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                @endif
            </div>

            <!-- PAGE CONTENT (pb-24 on mobile reserves space for the bottom navigation bar) -->
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-5 pb-24 lg:pb-8">
                @yield('content')
            </main>

            <!-- FOOTER -->
            <footer class="border-t border-slate-200 bg-white py-4 px-4 sm:px-6 lg:px-8 pb-20 lg:pb-4 text-xs text-slate-600 text-center md:text-left no-print">
                &copy; {{ date('Y') }} <strong class="text-slate-800">PT Gayuh Media Informatika</strong> &bull; Sistem Inventaris & Logistik Gudang
            </footer>
        </div>
    </div>

    <!-- MOBILE BOTTOM NAVIGATION BAR -->
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-white border-t border-slate-200 grid grid-cols-4 no-print" aria-label="Navigasi Cepat Mobile">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center py-2 min-h-[52px] text-[11px] font-semibold {{ request()->routeIs('dashboard') ? 'text-emerald-700' : 'text-slate-600 hover:text-slate-900' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('products.index') }}" class="flex flex-col items-center justify-center py-2 min-h-[52px] text-[11px] font-semibold {{ request()->routeIs('products.*') ? 'text-emerald-700' : 'text-slate-600 hover:text-slate-900' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <span>Stok</span>
        </a>
        <a href="{{ route('stock-out.index') }}" class="flex flex-col items-center justify-center py-2 min-h-[52px] text-[11px] font-semibold {{ request()->routeIs('stock-out.*') ? 'text-emerald-700' : 'text-slate-600 hover:text-slate-900' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            <span>Keluar</span>
        </a>
        <a href="{{ route('returns.index') }}" class="flex flex-col items-center justify-center py-2 min-h-[52px] text-[11px] font-semibold {{ request()->routeIs('returns.*') ? 'text-emerald-700' : 'text-slate-600 hover:text-slate-900' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18M13 8l4-4m0 0l4 4m-4-4v12"></path>
            </svg>
            <span>Retur</span>
        </a>
    </nav>

    @stack('scripts')
</body>

</html>