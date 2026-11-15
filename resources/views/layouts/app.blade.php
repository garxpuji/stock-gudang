<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Logistik Gudang') - Gayuh Media Informatika</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|jetbrains-mono:400,500" rel="stylesheet" />

    <!-- Tailwind & Vite Assets (Including Bundled Local Alpine.js) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        .font-mono-sn { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white !important; }
        }
    </style>
    @stack('styles')
</head>
<body class="h-full text-slate-800 antialiased selection:bg-emerald-500 selection:text-white" x-data="{ sidebarOpen: false, searchModal: false }">

    <div class="min-h-screen flex flex-col lg:flex-row">
        
        <!-- MOBILE TOPBAR -->
        <header class="lg:hidden sticky top-0 z-40 flex items-center justify-between bg-white border-b border-slate-200 px-3.5 py-2.5 shadow-2xs no-print gap-2">
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" @click="sidebarOpen = true" class="p-2 -ml-1 rounded-lg text-slate-600 hover:text-emerald-600 hover:bg-emerald-50 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 min-h-[40px] min-w-[40px] flex items-center justify-center cursor-pointer" aria-label="Buka Menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-bold shadow-2xs shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
            </div>

            <!-- Search input on mobile (compact & flexible) -->
            <form action="{{ route('products.index') }}" method="GET" class="flex-1 min-w-0 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari SKU / SN..." class="w-full pl-8 pr-2.5 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-hidden focus:ring-1 focus:ring-emerald-500">
                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </form>

            <div class="flex items-center gap-1.5 shrink-0">
                <a href="{{ route('products.create') }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-2xs transition-colors shrink-0" title="Tambah SKU">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>+ SKU</span>
                </a>
            </div>
        </header>

        <!-- MOBILE SLIDE-OUT DRAWER -->
        <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true">
            <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="sidebarOpen = false"></div>

            <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative flex-1 flex flex-col max-w-xs w-full bg-white border-r border-slate-200 shadow-xl h-full">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-bold shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <div>
                            <span class="text-sm font-bold text-slate-900 block">Gayuh Media Informatika</span>
                            <span class="text-[11px] font-medium text-emerald-600 block">Warehouse System</span>
                        </div>
                    </div>
                    <button type="button" @click="sidebarOpen = false" class="p-2 -mr-2 text-slate-400 hover:text-slate-600 rounded-lg min-h-[44px] min-w-[44px] flex items-center justify-center cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto px-4 py-4 space-y-6">
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
            <div class="px-6 py-5 border-b border-slate-200 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-bold shadow-sm ring-4 ring-emerald-50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div>
                    <h1 class="text-sm font-bold text-slate-900 leading-tight">Gayuh Media Informatika</h1>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full mt-0.5 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Gudang ISP Online
                    </span>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-4 py-5 space-y-6">
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
            <header class="hidden lg:flex items-center justify-between bg-white border-b border-slate-200 px-8 py-3.5 sticky top-0 z-20 shadow-2xs no-print">
                <!-- Search bar -->
                <form action="{{ route('products.index') }}" method="GET" class="w-full max-w-md relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari SKU / Serial Number / Perangkat..." class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </form>

                <div class="flex items-center gap-2.5">
                    <!-- Quick Action: Tambah SKU -->
                    <a href="{{ route('products.create') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900 border border-slate-200 transition-colors">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Tambah SKU</span>
                    </a>

                    @unless(request()->routeIs('dashboard'))
                    <!-- Quick Action: Dispatch (Di luar dashboard) -->
                    <a href="{{ route('stock-out.create') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 active:bg-emerald-800 transition-colors shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span>Keluar Barang</span>
                    </a>
                    @endunless
                </div>
            </header>

            <!-- FLASH ALERTS -->
            <div class="px-4 lg:px-8 pt-4 no-print">
                @if(session('success'))
                    <div class="flex items-center justify-between p-4 mb-3 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 shadow-xs" role="alert">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 p-1 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                @endif

                @if(session('error') || $errors->any())
                    <div class="p-4 mb-3 text-sm text-rose-800 rounded-xl bg-rose-50 border border-rose-200 shadow-xs" role="alert">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="flex-1">
                                <span class="font-semibold block mb-1">{{ session('error') ?? 'Terjadi kesalahan pada data formulir:' }}</span>
                                @if($errors->any())
                                    <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-700">
                                        @foreach($errors->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            <button type="button" @click="$el.closest('.rounded-xl').remove()" class="text-rose-600 hover:text-rose-800 p-1 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <!-- PAGE CONTENT -->
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
                @yield('content')
            </main>

            <!-- FOOTER -->
            <footer class="border-t border-slate-200 bg-white py-4 px-4 sm:px-6 lg:px-8 text-xs text-slate-500 flex flex-col md:flex-row items-center justify-between gap-2.5 text-center md:text-left no-print">
                <div>
                    &copy; {{ date('Y') }} <strong class="text-slate-700">PT Gayuh Media Informatika</strong> &bull; Sistem Informasi Inventaris & Logistik Gudang ISP
                </div>
                <div class="flex items-center gap-3 sm:gap-4 text-slate-400">
                    <span>MariaDB Server</span>
                    <span>&bull;</span>
                    <span>Debian 13 Env</span>
                    <span>&bull;</span>
                    <span class="text-emerald-600 font-semibold">Ready Production</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- QUICK FLOAT ACTION BUTTON (Mobile Floor View) -->
    <div class="fixed bottom-6 right-6 lg:hidden z-30 no-print flex flex-col gap-2">
        <a href="{{ route('stock-out.create') }}" class="w-14 h-14 rounded-full bg-emerald-600 text-white shadow-lg flex items-center justify-center active:scale-95 transition-transform" aria-label="Keluar Barang Cepat">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </a>
    </div>

    @stack('scripts')
</body>
</html>
