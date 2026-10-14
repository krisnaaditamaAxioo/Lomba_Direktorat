<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <title>Lupa PIN - Smart Axioo</title>
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
                <img src="{{ asset('images/SEH.png') }}" alt="Smart Axioo Logo" class="h-12 w-auto object-contain">
            </div>
            
            <div class="max-w-md pb-12 space-y-4">
                <span class="px-3.5 py-1.5 bg-brand-500/25 text-brand-300 rounded-full text-xs font-black uppercase tracking-wider inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs" style="font-variation-settings: 'FILL' 1;">lock_reset</span>
                    Lupa PIN
                </span>
                <h2 class="text-5xl font-black leading-tight tracking-tight">Reset PIN dengan Bantuan Admin</h2>
                <p class="text-base text-brand-100/90 leading-relaxed font-medium">Hubungi administrator sekolah untuk mereset PIN Anda ke default.</p>
            </div>
        </div>
    </section>

    <!-- RIGHT SIDE: Instructions (Mobile & Desktop) -->
    <main class="w-full lg:w-[500px] xl:w-[600px] flex flex-col justify-between relative min-h-screen px-4 py-6 sm:px-6 lg:px-8 bg-gradient-to-b from-[#0b3394] via-[#09297a] to-[#04123b] lg:bg-white">
        
        <!-- Mobile Brand Identity -->
        <div class="lg:hidden flex flex-col items-center text-white text-center pt-4">
            <div class="animate-fade-in-up flex flex-col items-center">
                <img src="{{ asset('images/SEH.png') }}" alt="Smart Axioo Logo" class="h-10 w-auto object-contain mb-1">
                <h2 class="text-xl font-bold tracking-tight mt-1">Smart EduHub</h2>
            </div>
            <p class="text-xs text-brand-200/80 font-medium mt-1">Lupa PIN?</p>
        </div>

        <!-- Instructions Container -->
        <div class="w-full max-w-[400px] mx-auto z-20 flex-1 flex flex-col justify-center gap-5 my-4">

            <!-- Card Utama Putih -->
            <div class="bg-white lg:bg-transparent rounded-[2.5rem] lg:rounded-none p-6 sm:p-8 lg:p-0 shadow-[0_24px_50px_rgba(4,18,59,0.3)] lg:shadow-none border border-slate-100/10 lg:border-none">
                
                <!-- Welcome Title (Visible on Desktop Only) -->
                <header class="hidden lg:block mb-8">
                    <span class="px-3 py-1 bg-brand-50 text-brand-600 rounded-full text-[10px] font-black uppercase tracking-wider">Portal Siswa</span>
                    <h1 class="text-4xl font-black text-slate-900 tracking-tight mt-3 mb-2">Lupa PIN?</h1>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Hubungi admin untuk mereset PIN Anda.</p>
                </header>

                @if($errors->any())
                <div class="mb-5 p-3.5 bg-rose-50 border border-rose-100 rounded-2xl text-rose-800 text-xs font-bold tracking-tight flex items-center gap-3 animate-fade-in-up">
                    <span class="material-symbols-outlined text-rose-500 text-lg">error</span>
                    <span>{{ $errors->first() }}</span>
                </div>
                @endif
                @if(session('success'))
                <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-800 text-xs font-bold tracking-tight flex items-center gap-3 animate-fade-in-up">
                    <span class="material-symbols-outlined text-emerald-500 text-lg">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                <form action="{{ route('siswa.forgot-pin.process') }}" method="POST" class="space-y-6 animate-fade-in-up">
                    @csrf
                    <!-- Instructions -->
                    <div class="space-y-3 text-center lg:text-left">
                        <h3 class="lg:hidden text-2xl font-extrabold text-[#4a0f0f] tracking-tight">Reset PIN Mandiri</h3>
                        <p class="text-xs text-slate-500 font-medium">Verifikasi data diri Anda untuk mereset PIN secara mandiri.</p>
                    </div>

                    <!-- Identifier -->
                    <div class="space-y-2">
                        <label class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest ml-1" for="identifier">Username / NISN</label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-brand-500 transition-colors text-lg">badge</span>
                            <input class="w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 focus:bg-white transition-all placeholder:text-slate-400 @error('identifier') border-rose-400 focus:ring-rose-500/10 focus:border-rose-500 @enderror"
                                   id="identifier" name="identifier" value="{{ old('identifier') }}" placeholder="Masukkan NISN / Username Anda" type="text" required/>
                        </div>
                        @error('identifier')
                        <p class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Verification Field Select -->
                    <div class="space-y-2">
                        <label class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest ml-1" for="verification_field">Pilih Data Verifikasi</label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-brand-500 transition-colors text-lg pointer-events-none">verified_user</span>
                            <select class="w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 focus:bg-white transition-all appearance-none cursor-pointer"
                                    id="verification_field" name="verification_field" required>
                                <option value="email" {{ old('verification_field') == 'email' ? 'selected' : '' }}>Email</option>
                                <option value="phone" {{ old('verification_field') == 'phone' ? 'selected' : '' }}>No. HP / No. HP Orang Tua</option>
                                <option value="mother_name" {{ old('verification_field') == 'mother_name' ? 'selected' : '' }}>Nama Ibu Kandung</option>
                                <option value="dateofbirth" {{ old('verification_field') == 'dateofbirth' ? 'selected' : '' }}>Tanggal Lahir (YYYY-MM-DD)</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">expand_more</span>
                        </div>
                    </div>

                    <!-- Verification Value -->
                    <div class="space-y-2">
                        <label class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest ml-1" for="verification_value">Isi Data Verifikasi</label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-brand-500 transition-colors text-lg">edit_note</span>
                            <input class="w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 focus:bg-white transition-all placeholder:text-slate-400 @error('verification_value') border-rose-400 focus:ring-rose-500/10 focus:border-rose-500 @enderror"
                                   id="verification_value" name="verification_value" value="{{ old('verification_value') }}" placeholder="Masukkan data sesuai pilihan di atas" type="text" required/>
                        </div>
                        @error('verification_value')
                        <p class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-[#0a2540] hover:bg-[#12355a] text-white font-extrabold py-4 rounded-2xl shadow-lg active:scale-[0.98] transition-all uppercase tracking-widest text-xs">
                        Verifikasi & Lanjutkan Reset PIN
                    </button>

                    <!-- Alternative: Admin Guide -->
                    <div class="bg-brand-50 border border-brand-200 rounded-2xl p-5">
                        <h3 class="text-sm font-black text-brand-800 mb-3 inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">support_agent</span>
                            Atau Hubungi Admin Sekolah
                        </h3>
                        <ol class="list-decimal list-inside space-y-1.5 text-[12px] text-brand-700 leading-relaxed">
                            <li>Hubungi wali kelas, BK, atau staf sekolah</li>
                            <li>Berikan NIS atau username Anda untuk verifikasi</li>
                            <li>Admin akan mereset PIN ke default: <code class="bg-brand-200 px-1.5 py-0.5 rounded font-bold text-brand-800">123456</code></li>
                        </ol>
                    </div>

                    <!-- Back Button -->
                    <a href="{{ route('siswa.login') }}" class="block w-full bg-white border border-slate-200 text-[#4a0f0f] font-extrabold py-4 rounded-2xl text-center hover:bg-slate-50 transition-all">
                        Kembali ke Login
                    </a>
                </form>
            </div>
        </div>

        <!-- Footer Copyright -->
        <div class="py-2 text-center">
            <p class="text-[10px] text-white/50 lg:text-slate-400 font-medium">&copy; 2026 Smart Axioo. All rights reserved.</p>
        </div>

    </main>
</body>
</html>
