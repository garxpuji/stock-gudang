<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Sistem Gudang - Gayuh Media Informatika</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 text-slate-800 antialiased selection:bg-emerald-500 selection:text-white">

    <div class="w-full max-w-md space-y-6">

        <!-- LOGO & BRANDING -->
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-emerald-600 flex items-center justify-center text-white font-bold mx-auto shadow-md ring-8 ring-emerald-50">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Gayuh Media Informatika</h1>
            <p class="text-xs text-slate-500 font-medium">Sistem Informasi Inventaris & Logistik Gudang ISP</p>
        </div>

        <!-- LOGIN CARD -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl p-8 space-y-6">
            
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900">Masuk Petugas Gudang</h2>
                <p class="text-xs text-slate-400 mt-0.5">Gunakan kredensial akun staf gudang atau administrator</p>
            </div>

            @if($errors->any())
                <div class="p-3 text-xs text-rose-800 bg-rose-50 border border-rose-200 rounded-xl">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Email Petugas</label>
                    <input type="email" name="email" id="email" value="{{ old('email', 'admin@gayuhmedia.id') }}" required autofocus class="w-full py-3 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 min-h-[44px]">
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Kata Sandi</label>
                    <input type="password" name="password" id="password" value="password" required class="w-full py-3 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 min-h-[44px]">
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                        <span class="text-slate-600">Ingat sesi saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-sm shadow-md transition-all min-h-[44px]">
                    Masuk ke Sistem Gudang
                </button>
            </form>

            <!-- DEMO LOGIN QUICK ACCESS -->
            <div class="pt-4 border-t border-slate-100 space-y-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block text-center">Akses Cepat (Demo Akun)</span>
                
                <div class="grid grid-cols-2 gap-2">
                    <form method="POST" action="{{ route('quick-login') }}">
                        @csrf
                        <input type="hidden" name="email" value="admin@gayuhmedia.id">
                        <button type="submit" class="w-full p-2.5 rounded-xl border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50/50 text-left transition-colors">
                            <span class="text-xs font-bold text-slate-800 block">Budi Santoso</span>
                            <span class="text-[10px] text-emerald-700 font-semibold block">Admin Gudang</span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('quick-login') }}">
                        @csrf
                        <input type="hidden" name="email" value="tegar@gayuhmedia.id">
                        <button type="submit" class="w-full p-2.5 rounded-xl border border-slate-200 hover:border-purple-300 hover:bg-purple-50/50 text-left transition-colors">
                            <span class="text-xs font-bold text-slate-800 block">Tegar Pratama</span>
                            <span class="text-[10px] text-purple-700 font-semibold block">Principal NOC</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <p class="text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} PT Gayuh Media Informatika &bull; All Rights Reserved
        </p>

    </div>

</body>
</html>
