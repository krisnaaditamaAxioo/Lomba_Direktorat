<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <meta name="theme-color" content="#044e3b"/>
    <title>Reset Password Baru – Smart Axioo</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
    <style>
        * { box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
            user-select: none;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-16px) scale(1.04); }
        }

        .fade-up { animation: fadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) both; }
        .delay-1 { animation-delay: 0.08s; }
        .delay-2 { animation-delay: 0.16s; }
        .delay-3 { animation-delay: 0.24s; }

        .blob-bg {
            position: absolute;
            border-radius: 9999px;
            filter: blur(100px);
            pointer-events: none;
            animation: floatSlow 14s ease-in-out infinite;
            will-change: transform;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .grid-pattern {
            background-image: radial-gradient(rgba(255, 255, 255, 0.12) 1px, transparent 1px);
            background-size: 28px 28px;
        }

        .input-group {
            position: relative;
            transition: all 0.2s ease;
        }
        .input-control {
            width: 100%;
            height: 3.25rem;
            padding-left: 2.85rem;
            padding-right: 2.85rem;
            background-color: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #0f172a;
            outline: none;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .input-control:hover {
            border-color: #cbd5e1;
            background-color: #ffffff;
        }
        .input-control:focus {
            background-color: #ffffff;
            border-color: #059669;
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.14);
        }
        .input-control::placeholder {
            color: #94a3b8;
            font-weight: 500;
        }
        .input-control.is-invalid {
            border-color: #f43f5e;
            background-color: #fff5f5;
        }

        @media (max-width: 1023px) {
            .input-control { font-size: 16px; }
        }

        .btn-emerald-primary {
            position: relative;
            overflow: hidden;
            width: 100%;
            height: 3.25rem;
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            border: none;
            border-radius: 1rem;
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }
        .btn-emerald-primary::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 60%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
            transform: skewX(-20deg);
            transition: left 0.75s ease;
        }
        .btn-emerald-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px -5px rgba(5, 150, 105, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.3);
            filter: brightness(1.04);
        }
        .btn-emerald-primary:hover::after {
            left: 140%;
        }
        .btn-emerald-primary:active {
            transform: translateY(0) scale(0.985);
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1fae5; border-radius: 9999px; }
    </style>
</head>
<body class="bg-slate-50 lg:bg-white text-slate-800 flex flex-col lg:flex-row min-h-screen overflow-x-hidden">

    {{-- LEFT SIDE: Hero Brand Banner (Desktop Only) --}}
    <aside class="hidden lg:flex flex-col flex-1 relative overflow-hidden sticky top-0 h-screen bg-gradient-to-br from-[#022c22] via-[#064e3b] to-[#047857]">
        <div class="blob-bg w-[32rem] h-[32rem] bg-emerald-400/20 -top-24 -left-24" style="animation-duration: 16s;"></div>
        <div class="blob-bg w-[26rem] h-[26rem] bg-teal-300/15 bottom-12 -right-16" style="animation-duration: 20s; animation-delay: 3s;"></div>
        <div class="blob-bg w-64 h-64 bg-emerald-200/10 top-1/2 left-1/3" style="animation-duration: 24s; animation-delay: 6s;"></div>
        
        <div class="absolute inset-0 grid-pattern opacity-40"></div>
        <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/40 via-transparent to-transparent pointer-events-none"></div>

        <div class="relative z-10 flex flex-col justify-between h-full p-12 xl:p-16">
            <div class="fade-up">
                <img src="{{ asset('images/SEH.png') }}" alt="Smart Axioo"
                     class="h-11 w-auto object-contain brightness-0 invert"
                     onerror="this.style.display='none'">
            </div>

            <div class="my-auto space-y-8 max-w-lg">
                <div class="fade-up delay-1">
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full glass-card text-emerald-200 text-xs font-bold uppercase tracking-widest mb-6">
                        <span class="material-symbols-outlined text-sm">vpn_key</span>
                        Password Baru
                    </span>
                    <h1 class="text-4xl xl:text-5xl font-black text-white leading-[1.12] tracking-tight">
                        Buat Kata Sandi Baru<br>
                        <span class="bg-gradient-to-r from-emerald-200 via-teal-200 to-emerald-400 bg-clip-text text-transparent">
                            Untuk Akun Anda.
                        </span>
                    </h1>
                    <p class="mt-4 text-emerald-100/80 text-sm xl:text-base leading-relaxed font-medium">
                        Pastikan menggunakan kombinasi huruf, angka, dan panjang minimal 6 karakter demi keamanan akun portal Wali Murid.
                    </p>
                </div>

                {{-- Security Indicators --}}
                <div class="grid grid-cols-2 gap-3.5 fade-up delay-2">
                    <div class="glass-card rounded-2xl p-4">
                        <span class="material-symbols-outlined text-emerald-300 text-2xl mb-1">lock</span>
                        <p class="text-2xl font-black text-white">Min. 6</p>
                        <p class="text-[11px] font-extrabold text-emerald-200/80 uppercase tracking-wider mt-0.5">Karakter Sandi</p>
                    </div>
                    <div class="glass-card rounded-2xl p-4">
                        <span class="material-symbols-outlined text-teal-300 text-2xl mb-1">enhanced_encryption</span>
                        <p class="text-2xl font-black text-white">Terenkripsi</p>
                        <p class="text-[11px] font-extrabold text-emerald-200/80 uppercase tracking-wider mt-0.5">Keamanan Hash</p>
                    </div>
                </div>
            </div>

            <div class="fade-up delay-3">
                <p class="text-xs text-emerald-200/50 font-medium">
                    &copy; {{ date('Y') }} Smart Axioo Class Program. All rights reserved.
                </p>
            </div>
        </div>
    </aside>

    {{-- RIGHT SIDE: Reset Password Form --}}
    <main class="w-full lg:w-[480px] xl:w-[540px] flex flex-col justify-between relative min-h-screen lg:min-h-0 lg:overflow-y-auto bg-gradient-to-b from-[#022c22] via-[#064e3b] to-[#047857] lg:bg-white">
        
        <div class="lg:hidden blob-bg w-72 h-72 bg-emerald-400/20 -top-20 -right-20 pointer-events-none"></div>
        <div class="lg:hidden absolute inset-0 grid-pattern opacity-20 pointer-events-none"></div>

        <div class="lg:hidden flex flex-col items-center text-white text-center pt-8 pb-2 relative z-10 fade-up">
            <img src="{{ asset('images/SEH.png') }}" alt="Smart Axioo"
                 class="h-10 w-auto object-contain mb-3 brightness-0 invert"
                 onerror="this.style.display='none'">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-[10px] text-emerald-200 font-extrabold uppercase tracking-widest backdrop-blur">
                <span class="material-symbols-outlined text-xs">vpn_key</span>
                Password Baru
            </span>
        </div>

        <div class="flex-1 flex flex-col justify-center px-4 sm:px-8 lg:px-12 xl:px-14 py-6 relative z-10">
            <div class="w-full max-w-sm mx-auto">

                <div class="bg-white rounded-[2rem] p-6 sm:p-8 shadow-2xl shadow-emerald-950/20 lg:bg-transparent lg:rounded-none lg:p-0 lg:shadow-none border border-white/20 lg:border-none relative">
                    
                    <header class="hidden lg:block mb-8 fade-up">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-[10px] font-black uppercase tracking-widest">
                            <span class="material-symbols-outlined text-xs">key</span>
                            Pembaruan Sandi
                        </span>
                        <h2 class="text-3xl font-black text-slate-900 tracking-tight mt-3 mb-1.5">Buat Kata Sandi Baru</h2>
                        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Masukkan kata sandi baru untuk akun wali murid Anda.</p>
                    </header>

                    <div class="lg:hidden text-center mb-6 fade-up">
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Buat Password Baru</h2>
                        <p class="text-slate-500 text-xs font-semibold mt-1">Masukkan kata sandi baru Anda</p>
                    </div>

                    @if(session('success'))
                    <div class="mb-5 flex items-start gap-3 p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl fade-up">
                        <span class="material-symbols-outlined text-emerald-600 text-xl shrink-0 mt-0.5">check_circle</span>
                        <p class="text-xs font-semibold text-emerald-800 leading-relaxed">{{ session('success') }}</p>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="mb-5 flex items-start gap-3 p-3.5 bg-rose-50 border border-rose-200 rounded-2xl fade-up">
                        <span class="material-symbols-outlined text-rose-500 text-xl shrink-0 mt-0.5">error</span>
                        <div class="text-xs font-semibold text-rose-800 leading-relaxed">
                            <p>{{ $errors->first() }}</p>
                        </div>
                    </div>
                    @endif

                    <form action="{{ route('parent.reset-password.update') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">

                        {{-- New Password Field --}}
                        <div class="fade-up delay-1">
                            <div class="flex items-center justify-between mb-1.5 px-0.5">
                                <label for="password" class="text-xs font-bold text-slate-700">Password Baru</label>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">Min. 6 Karakter</span>
                            </div>
                            <div class="group relative flex items-center rounded-2xl border-2 border-slate-200/90 bg-slate-50/80 p-1.5 transition-all duration-200 hover:border-slate-300 hover:bg-slate-50 focus-within:border-emerald-500 focus-within:bg-white focus-within:ring-4 focus-within:ring-emerald-500/15 focus-within:shadow-sm @error('password') !border-rose-400 !bg-rose-50/30 !ring-rose-400/20 @enderror">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white border border-slate-200/80 text-slate-400 shadow-xs transition-all group-focus-within:border-emerald-300 group-focus-within:bg-emerald-50 group-focus-within:text-emerald-600">
                                    <span class="material-symbols-outlined text-[20px]">lock</span>
                                </div>
                                <input id="password" name="password" type="password" required autocomplete="new-password"
                                       placeholder="Minimal 6 karakter"
                                       class="w-full bg-transparent border-0 px-3.5 py-2 text-sm font-semibold text-slate-800 placeholder:text-slate-400 placeholder:font-normal focus:outline-none focus:ring-0"/>
                                <button type="button" aria-label="Tampilkan password"
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700 mr-0.5"
                                        onclick="const el = document.getElementById('password'); const isP = el.type === 'password'; el.type = isP ? 'text' : 'password'; this.querySelector('span').textContent = isP ? 'visibility_off' : 'visibility';">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                            @error('password')
                            <div class="mt-1.5 flex items-center gap-1.5 text-rose-600 text-xs font-semibold px-1">
                                <span class="material-symbols-outlined text-sm">info</span>
                                <span>{{ $message }}</span>
                            </div>
                            @enderror
                        </div>

                        {{-- Confirm New Password Field --}}
                        <div class="fade-up delay-2">
                            <div class="flex items-center justify-between mb-1.5 px-0.5">
                                <label for="password_confirmation" class="text-xs font-bold text-slate-700">Konfirmasi Password Baru</label>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ulangi</span>
                            </div>
                            <div class="group relative flex items-center rounded-2xl border-2 border-slate-200/90 bg-slate-50/80 p-1.5 transition-all duration-200 hover:border-slate-300 hover:bg-slate-50 focus-within:border-emerald-500 focus-within:bg-white focus-within:ring-4 focus-within:ring-emerald-500/15 focus-within:shadow-sm @error('password_confirmation') !border-rose-400 !bg-rose-50/30 !ring-rose-400/20 @enderror">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white border border-slate-200/80 text-slate-400 shadow-xs transition-all group-focus-within:border-emerald-300 group-focus-within:bg-emerald-50 group-focus-within:text-emerald-600">
                                    <span class="material-symbols-outlined text-[20px]">lock_reset</span>
                                </div>
                                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                       placeholder="Ulangi password baru"
                                       class="w-full bg-transparent border-0 px-3.5 py-2 text-sm font-semibold text-slate-800 placeholder:text-slate-400 placeholder:font-normal focus:outline-none focus:ring-0"/>
                                <button type="button" aria-label="Tampilkan password"
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700 mr-0.5"
                                        onclick="const el = document.getElementById('password_confirmation'); const isP = el.type === 'password'; el.type = isP ? 'text' : 'password'; this.querySelector('span').textContent = isP ? 'visibility_off' : 'visibility';">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                            @error('password_confirmation')
                            <div class="mt-1.5 flex items-center gap-1.5 text-rose-600 text-xs font-semibold px-1">
                                <span class="material-symbols-outlined text-sm">info</span>
                                <span>{{ $message }}</span>
                            </div>
                            @enderror
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-2 fade-up delay-3">
                            <button type="submit" class="btn-emerald-primary">
                                <span class="relative z-10 flex items-center justify-center gap-2">
                                    Simpan Password Baru
                                    <span class="material-symbols-outlined text-lg">check_circle</span>
                                </span>
                            </button>
                        </div>
                    </form>

                    {{-- Navigation Links --}}
                    <div class="mt-6 pt-5 border-t border-slate-100 text-center space-y-2 fade-up delay-3">
                        <p class="text-xs text-slate-500 font-semibold">
                            <a href="{{ route('parent.login') }}"
                               class="text-emerald-700 font-extrabold hover:text-emerald-800 hover:underline">
                                Kembali ke Halaman Login
                            </a>
                        </p>
                    </div>

                </div>
            </div>
        </div>

        {{-- Mobile Footer Copyright --}}
        <div class="py-4 text-center relative z-10 lg:hidden">
            <p class="text-[11px] text-emerald-100/60 font-medium">
                &copy; {{ date('Y') }} Smart Axioo. All rights reserved.
            </p>
        </div>

        {{-- Floating WhatsApp Help Button --}}
        <a href="https://wa.me/6282289635649" target="_blank"
           title="Bantuan WhatsApp"
           class="fixed bottom-6 right-6 w-13 h-13 bg-emerald-600 hover:bg-emerald-500 text-white rounded-full flex items-center justify-center shadow-xl shadow-emerald-950/30 transition-all hover:scale-110 active:scale-95 z-50 p-3">
            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.488-.491-.672-.5-.198-.01-.422-.015-.645-.015-.223 0-.585.085-.892.413-.307.329-1.168 1.137-1.168 2.773 0 1.636 1.195 3.213 1.363 3.449.167.237 2.36 3.605 5.719 5.033.799.335 1.437.531 1.927.678.813.243 1.551.208 2.134.125.651-.09 1.998-.814 2.276-1.599.278-.785.278-1.455.198-1.599-.08-.144-.297-.237-.621-.386l-.001.001z"/>
                <path d="M12.002 2.163c-5.435 0-9.84 4.405-9.84 9.842 0 1.73.464 3.346 1.271 4.779l-1.338 4.894 5.024-1.309c1.39.755 2.988 1.189 4.883 1.189h.001c5.435 0 9.84-4.406 9.84-9.843 0-2.636-1.027-5.107-2.891-6.97a9.804 9.804 0 0 0-6.949-2.881zm6.875 14.407c-.198.56-.967 1.064-1.624 1.139-.415.047-.968.068-1.637-.087-.376-.088-.859-.278-1.478-.544-2.624-1.138-4.336-3.773-4.448-3.945-.112-.172-.919-1.22-.919-2.335 0-1.115.482-1.663.656-1.895.174-.232.379-.29.504-.29.126 0 .251.004.362.009.124.007.325-.047.507.372.184.42.626 1.453.68 1.558.055.106.092.227.017.363-.075.137-.112.224-.223.341-.112.118-.232.259-.333.347-.113.098-.227.213-.097.42.131.208 1.166 1.916 2.513 3.093.343.3.623.478.892.574.293.104.56.089.771.054.197-.034.624-.253.951-.497.327-.244.654-.206.904-.122.25.085 1.566.658 1.836.778.27.12.451.18.515.278.065.096.011.193-.055.273-.168.15-.129.16-.347.073-.47-.042-.123-.118-.435-.764-.579-.402-.178-.783-.208-.895-.216z"/>
            </svg>
        </a>
    </main>

</body>
</html>
