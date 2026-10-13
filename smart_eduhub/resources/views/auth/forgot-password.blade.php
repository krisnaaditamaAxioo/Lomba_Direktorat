@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')
<style>
    .mesh-auth-bg {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 35%, #7c2d12 70%, #c2410c 100%);
        background-size: 300% 300%;
        animation: meshAuthShift 20s ease infinite;
    }
    @keyframes meshAuthShift {
        0%, 100% { background-position: 0% 50%; }
        50%      { background-position: 100% 50%; }
    }
    @keyframes floatAuth {
        0%, 100% { transform: translate(0,0) scale(1); }
        33%      { transform: translate(30px,-40px) scale(1.08); }
        66%      { transform: translate(-40px,20px) scale(0.95); }
    }
    .blob-auth { animation: floatAuth 18s ease-in-out infinite; }
    .blob-auth.d1 { animation-delay: -6s; }
    .blob-auth.d2 { animation-delay: -12s; }

    .glass-auth {
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(28px);
        -webkit-backdrop-filter: blur(28px);
        border: 1px solid rgba(255,255,255,0.6);
        box-shadow: 0 30px 80px -20px rgba(0,0,0,0.55), inset 0 1px 0 rgba(255,255,255,0.7);
    }
    @media (min-width: 1024px) {
        .glass-auth { background: transparent; backdrop-filter: none; -webkit-backdrop-filter: none; border: 0; box-shadow: none; }
    }
    .fade-up { animation: fadeUp .6s cubic-bezier(.16,1,.3,1) both; }
    .fade-up.d1 { animation-delay: .08s; }
    .fade-up.d2 { animation-delay: .16s; }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .grid-dots {
        background-image: radial-gradient(rgba(255,255,255,.14) 1px, transparent 1px);
        background-size: 22px 22px;
    }
    @media (prefers-reduced-motion: reduce) {
        .mesh-auth-bg, .blob-auth, .fade-up { animation: none !important; }
    }
</style>

<!-- BACKGROUND MESH (mobile/tablet) -->
<div class="fixed inset-0 mesh-auth-bg -z-10 overflow-hidden">
    <div class="blob-auth absolute -top-[10%] -left-[5%] w-[32rem] h-[32rem] rounded-full bg-orange-400/30 blur-[110px]"></div>
    <div class="blob-auth d1 absolute -bottom-[15%] -right-[8%] w-[28rem] h-[28rem] rounded-full bg-amber-400/20 blur-[110px]"></div>
    <div class="blob-auth d2 absolute top-[40%] right-[30%] w-72 h-72 rounded-full bg-blue-400/15 blur-[90px]"></div>
</div>

@php
    $steps = [
        ['support_agent', 'Hubungi administrator', 'Temui staf IT atau administrator sekolah Anda.'],
        ['badge', 'Verifikasi identitas', 'Berikan username atau email akun Anda.'],
        ['lock_reset', 'Password direset', 'Admin akan mengatur ulang password ke default: '],
        ['key', 'Ganti password', 'Setelah login, segera ganti password di halaman profil.'],
    ];
@endphp

<div class="min-h-screen w-full lg:grid lg:grid-cols-[1.05fr_1fr] xl:grid-cols-[1.15fr_1fr]">

    <!-- ===================== PANEL BRAND (DESKTOP) ===================== -->
    <aside class="relative hidden lg:flex flex-col justify-between overflow-hidden p-10 xl:p-14 text-white bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E]">
        <div class="absolute inset-0 grid-dots opacity-60 pointer-events-none"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute bottom-0 -left-20 w-80 h-80 rounded-full bg-amber-300/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex items-center gap-3 fade-up">
            <div class="w-11 h-11 rounded-2xl bg-white/15 ring-1 ring-white/25 backdrop-blur flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1;">school</span>
            </div>
            <div class="leading-tight">
                <p class="text-lg font-black tracking-tight">Smart EduHub</p>
                <p class="text-[11px] font-semibold text-orange-100/80 uppercase tracking-widest">SEH Platform</p>
            </div>
        </div>

        <div class="relative z-10 max-w-lg fade-up d1">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 ring-1 ring-white/20 text-[11px] font-extrabold uppercase tracking-widest">
                <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">lock_reset</span>
                Pemulihan Akun
            </span>
            <h1 class="mt-5 text-4xl xl:text-5xl font-black tracking-tight leading-[1.1]">
                Lupa password?<br>Tenang, kami bantu.
            </h1>
            <p class="mt-4 text-base text-orange-50/80 font-medium leading-relaxed">
                Reset password dilakukan oleh administrator sekolah demi menjaga keamanan akun Anda.
            </p>

            <div class="mt-8 flex items-start gap-3 rounded-2xl bg-white/10 ring-1 ring-white/15 backdrop-blur-sm p-4">
                <span class="material-symbols-outlined text-xl text-amber-200" style="font-variation-settings:'FILL' 1;">shield</span>
                <p class="text-sm text-orange-50/90 font-medium leading-relaxed">
                    Demi keamanan, jangan pernah membagikan password Anda kepada siapa pun, termasuk kepada staf yang mengaku dari sekolah.
                </p>
            </div>
        </div>

        <p class="relative z-10 text-xs text-orange-100/60 font-medium fade-up d2">&copy; 2026 Smart EduHub. All rights reserved.</p>
    </aside>

    <!-- ===================== PANEL KONTEN ===================== -->
    <main class="relative flex flex-col min-h-screen lg:bg-slate-50">

        <!-- Header kompak (mobile / tablet) -->
        <header class="lg:hidden relative overflow-hidden bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] text-white px-6 pt-8 pb-16 sm:pb-20 rounded-b-[2.5rem] shadow-2xl shadow-orange-950/20">
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/5 rounded-full pointer-events-none"></div>
            <div class="absolute top-24 -left-12 w-40 h-40 bg-white/5 rounded-full pointer-events-none"></div>
            <div class="relative z-10 mx-auto w-full max-w-md space-y-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full bg-white/15 flex items-center justify-center backdrop-blur-sm">
                            <span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 1;">school</span>
                        </div>
                        <span class="text-base font-black tracking-tight">SEH</span>
                    </div>
                    <div class="bg-white text-slate-800 px-3 py-1.5 rounded-full text-xs font-extrabold flex items-center gap-1 shadow-sm">
                        <span class="material-symbols-outlined text-amber-500 text-sm" style="font-variation-settings:'FILL' 1;">lock_reset</span>
                        Reset
                    </div>
                </div>
                <div class="text-center space-y-1">
                    <p class="text-[10px] text-orange-200/80 font-bold uppercase tracking-widest">Portal Administrator</p>
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight">Lupa Password?</h2>
                    <p class="text-xs text-orange-100/70 font-medium">Hubungi administrator sekolah untuk mereset password.</p>
                </div>
            </div>
        </header>

        <div class="flex-1 flex items-start lg:items-center justify-center px-4 sm:px-6 lg:px-12 -mt-10 sm:-mt-12 lg:mt-0 pb-8 lg:py-12">
            <div class="w-full max-w-md lg:max-w-[28rem] fade-up d1">

                <div class="glass-auth rounded-[2rem] p-6 sm:p-8 lg:p-0">

                    <!-- Judul -->
                    <div class="mb-6 text-center lg:text-left">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 ring-1 ring-amber-200 text-[10px] font-black uppercase tracking-wider">
                            <span class="material-symbols-outlined text-xs" style="font-variation-settings:'FILL' 1;">help</span>
                            Bantuan Akun
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-3">Cara Mereset Password</h2>
                        <p class="text-sm text-slate-500 font-medium mt-1.5">Ikuti 4 langkah berikut untuk kembali mengakses akun Anda.</p>
                    </div>

                    <!-- Stepper -->
                    <ol class="relative space-y-4">
                        <span class="absolute left-[1.35rem] top-6 bottom-6 w-px bg-gradient-to-b from-orange-300 via-orange-200 to-transparent" aria-hidden="true"></span>
                        @foreach ($steps as $i => [$icon, $title, $desc])
                            <li class="relative flex gap-4 items-start">
                                <div class="relative z-10 w-11 h-11 rounded-2xl bg-gradient-to-br from-orange-600 to-amber-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/25 flex-shrink-0">
                                    <span class="material-symbols-outlined text-xl">{{ $icon }}</span>
                                </div>
                                <div class="flex-1 min-w-0 rounded-2xl bg-white border border-slate-200 p-3.5 shadow-sm">
                                    <p class="text-[10px] font-black text-orange-600 uppercase tracking-widest">Langkah {{ $i + 1 }}</p>
                                    <p class="text-sm font-extrabold text-slate-900 mt-0.5">{{ $title }}</p>
                                    <p class="text-xs sm:text-[13px] text-slate-500 font-medium mt-1 leading-relaxed">
                                        {{ $desc }}@if ($i === 2)<code class="ml-0.5 bg-amber-100 text-amber-800 px-2 py-0.5 rounded-md font-bold text-xs select-all">password123</code>@endif
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ol>

                    <!-- Catatan -->
                    <div class="mt-6 flex items-start gap-3 rounded-2xl bg-amber-50 border border-amber-200 p-4">
                        <span class="material-symbols-outlined text-amber-500 text-xl flex-shrink-0" style="font-variation-settings:'FILL' 1;">info</span>
                        <p class="text-xs sm:text-[13px] text-amber-800 font-medium leading-relaxed">
                            Password default hanya bersifat sementara. Pastikan Anda menggantinya segera setelah berhasil masuk.
                        </p>
                    </div>

                    <!-- Aksi -->
                    <a href="{{ route('login') }}"
                       class="mt-6 w-full flex justify-center items-center gap-2 py-3.5 px-4 rounded-2xl text-white bg-gradient-to-r from-orange-600 to-amber-500 hover:brightness-110 font-extrabold text-sm focus:outline-none focus-visible:ring-4 focus-visible:ring-orange-500/30 active:scale-[0.98] transition-all duration-200 shadow-lg shadow-orange-500/30 group">
                        <span class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
                        Kembali ke Login
                    </a>
                </div>

                <!-- Footer -->
                <div class="mt-6 flex flex-col items-center gap-1.5 text-center">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-orange-300 lg:text-emerald-500 text-lg" style="font-variation-settings:'FILL' 1;">verified_user</span>
                        <p class="text-[11px] font-semibold text-white/60 lg:text-slate-400 uppercase tracking-wider">EduAdmin Portal</p>
                    </div>
                    <p class="text-[11px] text-white/40 lg:hidden font-medium">&copy; 2026 Smart EduHub. All rights reserved.</p>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection