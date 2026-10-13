@extends('layouts.auth')

@section('title', 'Login')

@section('content')
@php
    $loginRole = $loginRole ?? 'admin';
    $isGuru = $loginRole === 'guru';

    // Class penuh ditulis eksplisit agar terbaca oleh Tailwind JIT
    $accentGrad   = $isGuru ? 'from-emerald-500 to-teal-600' : 'from-orange-600 to-amber-500';
    $accentText   = $isGuru ? 'text-emerald-600' : 'text-orange-600';
    $focusIcon    = $isGuru ? 'group-focus-within:text-emerald-600' : 'group-focus-within:text-orange-600';
    $focusInput   = $isGuru ? 'focus:ring-emerald-500/15 focus:border-emerald-500' : 'focus:ring-orange-500/15 focus:border-orange-500';
    $focusBtn     = $isGuru ? 'focus-visible:ring-emerald-500/30' : 'focus-visible:ring-orange-500/30';
    $shadowBtn    = $isGuru ? 'shadow-emerald-500/30' : 'shadow-orange-500/30';
    $badgeCls     = $isGuru ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-orange-50 text-orange-700 ring-orange-200';
    $roleLabel    = $isGuru ? 'Guru' : 'Admin';
    $roleIcon     = $isGuru ? 'school' : 'admin_panel_settings';
    $oldEmail     = old('email');
@endphp

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
    .fade-up.d3 { animation-delay: .24s; }
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

<!-- BACKGROUND MESH (terlihat di mobile/tablet) -->
<div class="fixed inset-0 mesh-auth-bg -z-10 overflow-hidden">
    <div class="blob-auth absolute -top-[10%] -left-[5%] w-[32rem] h-[32rem] rounded-full {{ $isGuru ? 'bg-emerald-400/25' : 'bg-orange-400/30' }} blur-[110px]"></div>
    <div class="blob-auth d1 absolute -bottom-[15%] -right-[8%] w-[28rem] h-[28rem] rounded-full bg-amber-400/20 blur-[110px]"></div>
    <div class="blob-auth d2 absolute top-[40%] right-[30%] w-72 h-72 rounded-full bg-blue-400/15 blur-[90px]"></div>
</div>

<div class="min-h-screen w-full lg:grid lg:grid-cols-[1.05fr_1fr] xl:grid-cols-[1.15fr_1fr]">

    <!-- ===================== PANEL BRAND (DESKTOP) ===================== -->
    <aside class="relative hidden lg:flex flex-col justify-between overflow-hidden p-10 xl:p-14 text-white bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E]">
        <div class="absolute inset-0 grid-dots opacity-60 pointer-events-none"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute bottom-0 -left-20 w-80 h-80 rounded-full bg-amber-300/20 blur-3xl pointer-events-none"></div>

        <!-- Logo -->
        <div class="relative z-10 flex items-center gap-3 fade-up">
            <div class="w-11 h-11 rounded-2xl bg-white/15 ring-1 ring-white/25 backdrop-blur flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1;">school</span>
            </div>
            <div class="leading-tight">
                <p class="text-lg font-black tracking-tight">Smart EduHub</p>
                <p class="text-[11px] font-semibold text-orange-100/80 uppercase tracking-widest">SEH Platform</p>
            </div>
        </div>

        <!-- Headline -->
        <div class="relative z-10 max-w-lg fade-up d1">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 ring-1 ring-white/20 text-[11px] font-extrabold uppercase tracking-widest">
                <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">{{ $roleIcon }}</span>
                Portal {{ $isGuru ? 'Guru' : 'Administrator' }}
            </span>
            <h1 class="mt-5 text-4xl xl:text-5xl font-black tracking-tight leading-[1.1]">
                Kelola sekolah dengan<br>lebih cerdas &amp; cepat.
            </h1>
            <p class="mt-4 text-base text-orange-50/80 font-medium leading-relaxed">
                Satu platform terpadu untuk administrasi, pembelajaran, dan pemantauan akademik.
            </p>

            <div class="mt-8 grid grid-cols-2 gap-3">
                @foreach ([
                    ['dashboard', 'Dashboard Real-time', 'Pantau data sekolah sekilas'],
                    ['groups', 'Manajemen Terpusat', 'Siswa, guru, dan kelas'],
                    ['bolt', 'Akses Cepat', 'Quick login tanpa ribet'],
                    ['lock', 'Aman Terjaga', 'Koneksi & sesi terlindungi'],
                ] as [$icon, $title, $desc])
                    <div class="rounded-2xl bg-white/10 ring-1 ring-white/15 backdrop-blur-sm p-4 hover:bg-white/15 transition-colors">
                        <span class="material-symbols-outlined text-xl text-amber-200" style="font-variation-settings:'FILL' 1;">{{ $icon }}</span>
                        <p class="mt-2 text-sm font-extrabold leading-tight">{{ $title }}</p>
                        <p class="text-xs text-orange-100/70 font-medium mt-0.5">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <p class="relative z-10 text-xs text-orange-100/60 font-medium fade-up d2">&copy; 2026 Smart EduHub. All rights reserved.</p>
    </aside>

    <!-- ===================== PANEL FORM ===================== -->
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
                        <span class="material-symbols-outlined text-emerald-500 text-sm" style="font-variation-settings:'FILL' 1;">verified_user</span>
                        {{ $roleLabel }}
                    </div>
                </div>
                <div class="text-center space-y-1">
                    <p class="text-[10px] text-orange-200/80 font-bold uppercase tracking-widest">Portal {{ $isGuru ? 'Guru' : 'Administrator' }}</p>
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight">Smart EduHub</h2>
                    <p class="text-xs text-orange-100/70 font-medium">Masuk ke akun Anda untuk melanjutkan.</p>
                </div>
            </div>
        </header>

        <div class="flex-1 flex items-start lg:items-center justify-center px-4 sm:px-6 lg:px-12 -mt-10 sm:-mt-12 lg:mt-0 pb-8 lg:py-12">
            <div class="w-full max-w-md lg:max-w-[26rem] xl:max-w-md fade-up d1">

                <div class="glass-auth rounded-[2rem] p-6 sm:p-8 lg:p-0">

                    <!-- Judul -->
                    <div class="mb-7 text-center lg:text-left">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full ring-1 {{ $badgeCls }} text-[10px] font-black uppercase tracking-wider">
                            <span class="material-symbols-outlined text-xs" style="font-variation-settings:'FILL' 1;">{{ $roleIcon }}</span>
                            Portal {{ $roleLabel }}
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-3">Selamat Datang</h2>
                        <p class="text-sm text-slate-500 font-medium mt-1.5">Masukkan email &amp; password untuk melanjutkan.</p>
                    </div>

                    <!-- FORM -->
                    <form id="loginForm" class="space-y-5" method="POST" action="{{ route('login.' . $loginRole . '.submit') }}">
                        @csrf

                        <!-- Email -->
                        <div class="space-y-2 group">
                            <label class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest ml-1" for="email">Alamat Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="material-symbols-outlined text-slate-400 {{ $focusIcon }} text-xl transition-colors">mail</span>
                                </div>
                                <input id="email" name="email" type="email" required autofocus autocomplete="username"
                                       value="{{ $oldEmail }}"
                                       placeholder="{{ $isGuru ? 'guru@smarteduhub.com' : 'admin@smarteduhub.com' }}"
                                       class="block w-full pl-11 pr-4 py-3.5 bg-white lg:bg-white border border-slate-200 rounded-2xl text-sm font-semibold text-slate-900 placeholder:text-slate-400 shadow-sm focus:outline-none focus:ring-4 {{ $focusInput }} transition-all duration-200 @error('email') border-rose-500 @enderror">
                            </div>
                            @error('email')
                                <p class="text-rose-500 text-xs font-medium mt-1.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">error</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="space-y-2 group">
                            <label class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest ml-1" for="password">Kata Sandi</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="material-symbols-outlined text-slate-400 {{ $focusIcon }} text-xl transition-colors">lock</span>
                                </div>
                                <input id="password" name="password" type="password" required autocomplete="current-password"
                                       placeholder="••••••••"
                                       class="block w-full pl-11 pr-12 py-3.5 bg-white border border-slate-200 rounded-2xl text-sm font-semibold text-slate-900 placeholder:text-slate-400 shadow-sm focus:outline-none focus:ring-4 {{ $focusInput }} transition-all duration-200 @error('password') border-rose-500 @enderror">
                                <div class="absolute inset-y-0 right-0 pr-1.5 flex items-center">
                                    <button id="togglePw" type="button" aria-label="Tampilkan password"
                                            class="h-10 w-10 flex items-center justify-center rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-all">
                                        <span class="material-symbols-outlined text-xl">visibility</span>
                                    </button>
                                </div>
                            </div>
                            @error('password')
                                <p class="text-rose-500 text-xs font-medium mt-1.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">error</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Remember & forgot -->
                        <div class="flex items-center justify-between gap-3 pt-1">
                            <label class="flex items-center cursor-pointer select-none">
                                <input id="remember" name="remember" type="checkbox"
                                       class="h-4 w-4 border-slate-300 rounded-md {{ $isGuru ? 'accent-emerald-600' : 'accent-orange-600' }}">
                                <span class="ml-2.5 text-sm text-slate-500 hover:text-slate-700 transition-colors font-medium">Ingat saya</span>
                            </label>
                            <a href="{{ route('password.request') }}"
                               class="text-sm font-bold {{ $accentText }} hover:underline underline-offset-4">Lupa password?</a>
                        </div>

                        <!-- Submit -->
                        <button id="submitBtn" type="submit"
                                class="w-full flex justify-center items-center gap-2 py-3.5 px-4 rounded-2xl text-white bg-gradient-to-r {{ $accentGrad }} hover:brightness-110 font-extrabold text-sm focus:outline-none focus-visible:ring-4 {{ $focusBtn }} active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed transition-all duration-200 shadow-lg {{ $shadowBtn }}">
                            <span id="submitLabel">Masuk ke Portal</span>
                            <span id="submitIcon" class="material-symbols-outlined text-lg">arrow_forward</span>
                            <svg id="submitSpin" class="hidden animate-spin h-5 w-5" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
                                <path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round" class="opacity-90"/>
                            </svg>
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="flex items-center gap-3 my-6">
                        <div class="h-px flex-1 bg-slate-200"></div>
                        <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest">atau</span>
                        <div class="h-px flex-1 bg-slate-200"></div>
                    </div>

                    <!-- Quick login -->
                    <button type="button" id="quickLoginTrigger"
                            class="w-full flex items-center gap-4 p-3.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300 hover:shadow-md transition-all duration-200 group text-left">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br {{ $accentGrad }} flex items-center justify-center text-white shadow-md group-hover:scale-105 transition-transform flex-shrink-0">
                            <span class="material-symbols-outlined text-xl">manage_accounts</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-black text-slate-900 leading-none">Akun Terbaru — Quick Login</p>
                            <p class="text-xs text-slate-500 font-medium mt-1">Pilih akun tanpa password</p>
                        </div>
                        <span class="material-symbols-outlined text-slate-400 group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                    </button>
                </div>

                <!-- Footer -->
                <div class="mt-6 flex flex-col items-center gap-1.5 text-center">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined {{ $isGuru ? 'text-emerald-400' : 'text-orange-300' }} lg:text-emerald-500 text-lg" style="font-variation-settings:'FILL' 1;">verified_user</span>
                        <p class="text-[11px] font-semibold text-white/60 lg:text-slate-400 uppercase tracking-wider">Secure Connection</p>
                    </div>
                    <p class="text-[11px] text-white/40 lg:hidden font-medium">&copy; 2026 Smart EduHub. All rights reserved.</p>
                </div>
            </div>
        </div>
    </main>
</div>

{{-- ===== QUICK LOGIN: bottom sheet (mobile) / modal tengah (md+) ===== --}}
<div id="qlBackdrop" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-[105] hidden opacity-0 transition-opacity duration-300"></div>

<div id="qlSheet" role="dialog" aria-modal="true" aria-label="Quick Access"
     class="fixed z-[110] bg-white shadow-2xl overflow-y-auto transition-all duration-300 ease-out
            bottom-0 inset-x-0 max-h-[88vh] rounded-t-[2rem]
            md:inset-0 md:m-auto md:h-fit md:max-h-[85vh] md:max-w-lg md:rounded-[2rem]
            translate-y-full md:translate-y-0 md:opacity-0 md:scale-95 invisible">
    <div class="p-6 md:p-8">
        <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-6 md:hidden"></div>

        <div class="flex items-start justify-between mb-5 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $accentGrad }} flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-base">{{ $roleIcon }}</span>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">Quick Access</h3>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Pilih akun dari riwayat login</p>
                </div>
            </div>
            <button id="qlSheetClose" aria-label="Tutup"
                    class="p-2 text-slate-400 hover:text-slate-700 rounded-xl bg-slate-100 hover:bg-slate-200 transition-all flex-shrink-0">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <div class="relative mb-4">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl pointer-events-none">search</span>
            <input id="qlSearch" type="text" autocomplete="off" placeholder="Cari nama atau email..."
                   class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3.5 pl-12 pr-4 text-sm font-medium focus:ring-4 {{ $focusInput }} focus:bg-white outline-none transition-all placeholder:text-slate-400">
        </div>

        <div id="qlList" class="space-y-2 max-h-[40vh] overflow-y-auto pr-1">
            <div id="qlListInner"></div>
        </div>

        <div class="mt-6 pt-5 border-t border-slate-100">
            <div class="flex items-center justify-between gap-3 mb-4">
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">atau login manual</p>
                <button type="button" id="qlClearAll"
                        class="flex items-center gap-1 px-3 py-1.5 rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-100 text-xs font-bold transition-all">
                    <span class="material-symbols-outlined text-sm">delete_sweep</span>
                    Hapus Semua
                </button>
            </div>
            <button type="button" id="qlClose2"
                    class="w-full flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl border-2 border-slate-200 text-slate-700 hover:border-slate-300 hover:bg-slate-50 transition-all font-bold text-sm">
                <span class="material-symbols-outlined text-lg">edit</span>
                Gunakan Form Login
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    const LOGIN_KEY  = 'smart_acp_login_history';
    const LOGIN_ROLE = @json($loginRole);
    const CSRF_TOKEN = @json(csrf_token());
    const QUICK_URL  = @json(url('quick-login'));

    const $ = id => document.getElementById(id);
    const backdrop = $('qlBackdrop'), sheet = $('qlSheet'), searchInput = $('qlSearch'), listInner = $('qlListInner');

    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const readAll = () => { try { return JSON.parse(localStorage.getItem(LOGIN_KEY) || '[]'); } catch (e) { return []; } };
    const isGuruUser = u => u.role === 'guru' || (u.role_label || '').toLowerCase() === 'guru';

    /* ---------- Toggle password ---------- */
    $('togglePw')?.addEventListener('click', function () {
        const p = $('password');
        const show = p.type === 'password';
        p.type = show ? 'text' : 'password';
        this.querySelector('span').textContent = show ? 'visibility_off' : 'visibility';
        this.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
    });

    /* ---------- Loading state submit ---------- */
    $('loginForm')?.addEventListener('submit', () => {
        const b = $('submitBtn');
        b.disabled = true;
        $('submitLabel').textContent = 'Memproses...';
        $('submitIcon').classList.add('hidden');
        $('submitSpin').classList.remove('hidden');
    });
    // Reset saat kembali via tombol back (bfcache)
    window.addEventListener('pageshow', e => {
        if (!e.persisted) return;
        $('submitBtn').disabled = false;
        $('submitLabel').textContent = 'Masuk ke Portal';
        $('submitIcon').classList.remove('hidden');
        $('submitSpin').classList.add('hidden');
    });

    /* ---------- Quick login ---------- */
    function getSavedAccounts(filter) {
        return readAll()
            .filter(u => u && u.type === 'user' && (LOGIN_ROLE === 'guru' ? isGuruUser(u) : !isGuruUser(u)))
            .filter(u => {
                if (!filter) return true;
                const f = filter.toLowerCase();
                return (u.name || '').toLowerCase().includes(f)
                    || (u.secondary || '').toLowerCase().includes(f)
                    || (u.role_label || '').toLowerCase().includes(f);
            });
    }

    function renderList(filter = '') {
        const users = getSavedAccounts(filter);

        if (!users.length) {
            listInner.innerHTML = `
                <div class="py-12 flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-4xl text-slate-300">person_off</span>
                    </div>
                    <p class="text-sm font-black text-slate-500 mb-1">${filter ? 'Tidak ada akun yang cocok' : 'Belum ada riwayat login'}</p>
                    <p class="text-xs text-slate-400 max-w-xs leading-relaxed mt-1">${filter ? 'Coba kata kunci lain.' : 'Login standar sekali, akun akan tersimpan otomatis.'}</p>
                </div>`;
            return;
        }

        listInner.innerHTML = users.map(u => {
            const initials = ((u.name || '?').match(/\b\w/g) || []).slice(0, 2).join('').toUpperCase();
            const rawRole  = (u.role || '').toLowerCase();
            const label    = (u.role_label || rawRole || 'USER').toUpperCase();
            const isAdmin  = rawRole !== 'guru';
            const avatar   = isAdmin ? 'from-orange-500 to-amber-500' : 'from-emerald-500 to-teal-600';
            const badge    = isAdmin ? 'bg-orange-50 text-orange-600 border-orange-200' : 'bg-emerald-50 text-emerald-600 border-emerald-200';
            const hover    = isAdmin ? 'hover:bg-orange-50/60 hover:border-orange-200' : 'hover:bg-emerald-50/60 hover:border-emerald-200';
            const arrow    = isAdmin ? 'group-hover:bg-orange-600' : 'group-hover:bg-emerald-600';
            const id       = encodeURIComponent(u.id);

            return `
            <div class="flex items-center gap-2 mb-2">
                <form method="POST" action="${QUICK_URL}/${id}" class="flex-1 min-w-0">
                    <input type="hidden" name="_token" value="${esc(CSRF_TOKEN)}">
                    <button type="submit"
                        class="w-full flex items-center gap-4 p-3.5 rounded-2xl border border-slate-100 bg-white ${hover} transition-all group text-left active:scale-[0.99]">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br ${avatar} flex items-center justify-center text-white font-black text-sm flex-shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            ${esc(initials || '?')}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-bold text-slate-900 truncate">${esc(u.name || 'User')}</p>
                                <span class="text-[9px] font-black px-2 py-0.5 rounded-full border ${badge} uppercase tracking-wider">${esc(label)}</span>
                            </div>
                            <p class="text-xs text-slate-500 truncate mt-0.5">${esc(u.secondary || u.email || '—')}</p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center ${arrow} group-hover:text-white transition-all flex-shrink-0">
                            <span class="material-symbols-outlined text-lg">arrow_forward</span>
                        </div>
                    </button>
                </form>
                <button type="button" data-remove="${esc(u.id)}" title="Hapus dari riwayat" aria-label="Hapus dari riwayat"
                    class="flex-shrink-0 w-10 h-10 rounded-2xl bg-slate-100 hover:bg-rose-100 text-slate-400 hover:text-rose-500 flex items-center justify-center transition-all">
                    <span class="material-symbols-outlined text-lg">delete</span>
                </button>
            </div>`;
        }).join('');
    }

    listInner.addEventListener('click', e => {
        const btn = e.target.closest('[data-remove]');
        if (!btn) return;
        const id = btn.dataset.remove;
        try {
            localStorage.setItem(LOGIN_KEY, JSON.stringify(readAll().filter(u => u && String(u.id) !== id)));
        } catch (err) {}
        renderList(searchInput.value || '');
    });

    $('qlClearAll')?.addEventListener('click', () => {
        try {
            const keep = readAll().filter(u => {
                if (!u || u.type !== 'user') return true;
                return LOGIN_ROLE === 'guru' ? !isGuruUser(u) : isGuruUser(u);
            });
            localStorage.setItem(LOGIN_KEY, JSON.stringify(keep));
        } catch (err) {}
        renderList('');
    });

    const CLOSED = ['translate-y-full', 'md:opacity-0', 'md:scale-95', 'invisible'];
    function openSheet() {
        backdrop.classList.remove('hidden');
        requestAnimationFrame(() => backdrop.classList.remove('opacity-0'));
        sheet.classList.remove(...CLOSED);
        renderList('');
        setTimeout(() => searchInput?.focus(), 350);
        document.body.style.overflow = 'hidden';
    }
    function closeSheet() {
        backdrop.classList.add('opacity-0');
        sheet.classList.add(...CLOSED);
        setTimeout(() => backdrop.classList.add('hidden'), 300);
        document.body.style.overflow = '';
    }

    $('quickLoginTrigger')?.addEventListener('click', openSheet);
    $('qlSheetClose')?.addEventListener('click', closeSheet);
    $('qlClose2')?.addEventListener('click', () => { closeSheet(); setTimeout(() => $('email')?.focus(), 320); });
    backdrop?.addEventListener('click', closeSheet);
    searchInput?.addEventListener('input', e => renderList(e.target.value));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSheet(); });
})();
</script>
@endsection