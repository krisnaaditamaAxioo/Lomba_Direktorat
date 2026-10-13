<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <title>Reset PIN - Smart Axioo</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            -webkit-tap-highlight-color: transparent;
        }
        .login-image-container {
            background-image: url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=1470&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }
        @keyframes fade-in-up {
            0% { opacity: 0; transform: translateY(16px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        ::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }
    </style>
</head>
<body class="text-slate-900 min-h-screen flex flex-col lg:flex-row overflow-x-hidden">

    <!-- LEFT SIDE: Hero Brand Image (Visible on Desktop Only) -->
    <section class="hidden lg:flex flex-1 relative login-image-container">
        <div class="absolute inset-0 bg-gradient-to-tr from-[#4a0f0f]/95 via-[#8A1E1E]/80 to-[#F6863B]/70 backdrop-blur-[2px]"></div>
        
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 right-12 w-80 h-80 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 p-16 flex flex-col justify-between h-full text-white">
            <div class="flex flex-col items-start gap-2">
                <img src="{{ asset('images/SEH.png') }}" alt="Smart Axioo Logo" class="h-12 w-auto object-contain" onerror="this.style.display='none'">
            </div>
            
            <div class="max-w-md pb-12 space-y-4">
                <span class="px-3.5 py-1.5 bg-brand-500/25 text-brand-300 rounded-full text-xs font-black uppercase tracking-wider inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs" style="font-variation-settings: 'FILL' 1;">stars</span>
                    SACP Priority Member
                </span>
                <h2 class="text-5xl font-black leading-tight tracking-tight">Masa depan cerah dimulai dari sini.</h2>
                <p class="text-base text-brand-100/90 leading-relaxed font-medium">Akses portal siswa untuk memantau perkembangan akademik, presensi harian, dan mutasi poin kedisiplinan Anda secara real-time.</p>
            </div>
        </div>
    </section>

    <!-- RIGHT SIDE: Form (Mobile & Desktop) -->
    <main class="w-full lg:w-[500px] xl:w-[600px] flex flex-col justify-between relative min-h-screen px-4 py-6 sm:px-6 lg:px-8 bg-gradient-to-b from-[#0b3394] via-[#09297a] to-[#04123b] lg:bg-white">
        
        <!-- Mobile Brand Identity -->
        <div class="lg:hidden flex flex-col items-center text-white text-center pt-4">
            <div class="animate-fade-in-up flex flex-col items-center">
                <img src="{{ asset('images/SEH.png') }}" alt="Smart Axioo Logo" class="h-10 w-auto object-contain mb-1" onerror="this.style.display='none'">
                <h2 class="text-xl font-bold tracking-tight mt-1">Smart EduHub</h2>
            </div>
            <p class="text-xs text-brand-200/80 font-medium mt-1">Masukkan PIN baru Anda</p>
        </div>

        <!-- LOGO & CARD CONTAINER -->
        <div class="w-full max-w-[400px] mx-auto z-20 flex-1 flex flex-col justify-center gap-5 my-4">

            <!-- Card Utama Putih -->
            <div class="bg-white lg:bg-transparent rounded-[2.5rem] lg:rounded-none p-6 sm:p-8 lg:p-0 shadow-[0_24px_50px_rgba(4,18,59,0.3)] lg:shadow-none border border-slate-100/10 lg:border-none">
                
                <!-- Welcome Title (Visible on Desktop Only) -->
                <header class="hidden lg:block mb-8">
                    <span class="px-3 py-1 bg-brand-50 text-[#4a0f0f] rounded-full text-[10px] font-black uppercase tracking-wider">Portal Siswa</span>
                    <h1 class="text-4xl font-black text-slate-900 tracking-tight mt-3 mb-2">Reset PIN</h1>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Masukkan PIN baru Anda.</p>
                </header>

                @if($errors->any())
                <div class="mb-5 p-3.5 bg-rose-50 border border-rose-100 rounded-2xl text-rose-800 text-xs font-bold tracking-tight flex items-center gap-3 animate-fade-in-up">
                    <span class="material-symbols-outlined text-rose-500 text-lg">error</span>
                    <span>{{ $errors->first() }}</span>
                </div>
                @endif

                <!-- Form -->
                <form action="{{ route('siswa.reset-pin.update') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="identifier" value="{{ $identifier }}">
                    
                    <div class="space-y-5 animate-fade-in-up">
                        <div class="space-y-2 text-center lg:text-left">
                            <h3 class="lg:hidden text-2xl font-extrabold text-[#4a0f0f] tracking-tight">PIN Baru</h3>
                            <p class="lg:hidden text-xs text-slate-400 font-medium">Masukkan PIN baru 6 digit</p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest ml-1" for="pin">PIN Baru (6 digit)</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-brand-500 transition-colors text-lg">lock</span>
                                <input class="w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 focus:bg-white transition-all placeholder:text-slate-400" id="pin" name="pin" placeholder="Masukkan PIN baru" type="password" maxlength="6" inputmode="numeric" pattern="[0-9]*" required/>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest ml-1" for="pin_confirmation">Konfirmasi PIN Baru</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-brand-500 transition-colors text-lg">lock</span>
                                <input class="w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 focus:bg-white transition-all placeholder:text-slate-400" id="pin_confirmation" name="pin_confirmation" placeholder="Konfirmasi PIN baru" type="password" maxlength="6" inputmode="numeric" pattern="[0-9]*" required/>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full bg-[#0a2540] hover:bg-[#12355a] text-white font-extrabold py-4 rounded-2xl shadow-lg active:scale-[0.98] transition-all uppercase tracking-widest text-xs mt-2">
                            Reset PIN
                        </button>
                    </div>
                </form>
                
                <!-- Footer Help Options inside card -->
                <footer class="mt-6 pt-4 border-t border-slate-100 text-center">
                    <p class="text-xs text-slate-500 font-semibold">
                        <a class="text-[#0a2540] hover:underline font-extrabold" href="{{ route('siswa.login') }}">Kembali ke Login</a>
                    </p>
                </footer>
            </div>
        </div>

        <!-- Footer Copyright -->
        <div class="py-2 text-center">
            <p class="text-[10px] text-white/50 lg:text-slate-400 font-medium">&copy; 2026 Smart Axioo. All rights reserved.</p>
        </div>

    </main>
</body>
</html>
