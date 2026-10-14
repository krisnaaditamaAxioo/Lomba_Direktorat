@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
@php
    $initials = collect(preg_split('/\s+/', trim($user->name)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    $inputBase = 'block w-full bg-slate-50 border border-slate-200 rounded-2xl py-3.5 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:bg-white focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 transition-all duration-200';
@endphp

<style>
    .fade-up { animation: fadeUp .55s cubic-bezier(.16,1,.3,1) both; }
    .fade-up.d1 { animation-delay: .07s; }
    .fade-up.d2 { animation-delay: .14s; }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .grid-dots {
        background-image: radial-gradient(rgba(255,255,255,.14) 1px, transparent 1px);
        background-size: 22px 22px;
    }
    @media (prefers-reduced-motion: reduce) { .fade-up { animation: none !important; } }
</style>

<div class="w-full max-w-6xl mx-auto">

    <!-- ===== PAGE HEADER ===== -->
    <div class="mb-6 lg:mb-8 fade-up">
        <p class="text-[11px] font-black text-orange-600 uppercase tracking-widest">Akun</p>
        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">My Profile</h2>
        <p class="text-sm text-slate-500 font-medium mt-1">Manage your account settings and security.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 flex items-start gap-3 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 fade-up" role="status">
            <span class="material-symbols-outlined text-emerald-500 text-xl flex-shrink-0" style="font-variation-settings:'FILL' 1;">check_circle</span>
            <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif

    <form id="profileForm" action="{{ route('profile.update') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 items-start">
        @csrf

        <!-- ===== KARTU PROFIL (kiri) ===== -->
        <aside class="lg:col-span-1 lg:sticky lg:top-6 fade-up d1">
            <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] text-white shadow-2xl shadow-orange-950/10">
                <div class="absolute inset-0 grid-dots opacity-60 pointer-events-none"></div>
                <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/10 rounded-full pointer-events-none"></div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-amber-300/20 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10 p-6 sm:p-8 flex flex-row lg:flex-col items-center lg:text-center gap-5 lg:gap-0">
                    <div class="relative flex-shrink-0">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=256&color=C2410C&background=FFEDD5&bold=true"
                             alt="Avatar {{ $user->name }}"
                             onerror="this.replaceWith(Object.assign(document.createElement('div'),{className:'w-24 h-24 lg:w-28 lg:h-28 rounded-3xl bg-white/20 ring-4 ring-white/30 flex items-center justify-center text-3xl font-black',textContent:'{{ $initials }}'}))"
                             class="w-20 h-20 sm:w-24 sm:h-24 lg:w-28 lg:h-28 rounded-3xl ring-4 ring-white/30 shadow-xl object-cover">
                        <span class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-emerald-500 ring-4 ring-[#EB4325] flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-sm" style="font-variation-settings:'FILL' 1;">check</span>
                        </span>
                    </div>

                    <div class="min-w-0 lg:mt-5">
                        <h3 class="text-xl sm:text-2xl font-black tracking-tight truncate">{{ $user->name }}</h3>
                        <p class="text-sm text-orange-100/80 font-medium truncate mt-0.5">{{ $user->email }}</p>
                        <span class="inline-flex items-center gap-1.5 mt-3 px-3 py-1 rounded-full bg-white text-slate-800 text-[11px] font-extrabold shadow-sm">
                            <span class="material-symbols-outlined text-emerald-500 text-sm" style="font-variation-settings:'FILL' 1;">verified_user</span>
                            Administrator
                        </span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- ===== FORM (kanan) ===== -->
        <div class="lg:col-span-2 space-y-6 fade-up d2">

            <!-- Informasi akun -->
            <section class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-orange-600 to-amber-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/25">
                        <span class="material-symbols-outlined text-xl">badge</span>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 tracking-tight leading-tight">Informasi Akun</h3>
                        <p class="text-xs text-slate-500 font-medium">Nama dan email yang digunakan untuk login.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-2 group">
                        <label for="name" class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-orange-600 text-xl transition-colors pointer-events-none">person</span>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required autocomplete="name"
                                   class="{{ $inputBase }} pl-11 pr-4 @error('name') border-rose-500 @enderror">
                        </div>
                        @error('name')
                            <p class="text-rose-500 text-xs font-medium flex items-center gap-1"><span class="material-symbols-outlined text-sm">error</span>{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2 group">
                        <label for="email" class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-orange-600 text-xl transition-colors pointer-events-none">mail</span>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required autocomplete="email"
                                   class="{{ $inputBase }} pl-11 pr-4 @error('email') border-rose-500 @enderror">
                        </div>
                        @error('email')
                            <p class="text-rose-500 text-xs font-medium flex items-center gap-1"><span class="material-symbols-outlined text-sm">error</span>{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <!-- Keamanan -->
            <section class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-orange-600 to-amber-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/25">
                        <span class="material-symbols-outlined text-xl">lock</span>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 tracking-tight leading-tight">Keamanan</h3>
                        <p class="text-xs text-slate-500 font-medium">Kosongkan jika tidak ingin mengganti password.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-2 group">
                        <label for="password" class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest ml-1">New Password</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-orange-600 text-xl transition-colors pointer-events-none">key</span>
                            <input type="password" name="password" id="password" autocomplete="new-password" placeholder="••••••••"
                                   class="{{ $inputBase }} pl-11 pr-12 @error('password') border-rose-500 @enderror">
                            <button type="button" data-toggle="password" aria-label="Tampilkan password"
                                    class="absolute inset-y-0 right-1.5 my-auto h-10 w-10 flex items-center justify-center rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-all">
                                <span class="material-symbols-outlined text-xl">visibility</span>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-rose-500 text-xs font-medium flex items-center gap-1"><span class="material-symbols-outlined text-sm">error</span>{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2 group">
                        <label for="password_confirmation" class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest ml-1">Confirm New Password</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-orange-600 text-xl transition-colors pointer-events-none">lock_reset</span>
                            <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password" placeholder="••••••••"
                                   class="{{ $inputBase }} pl-11 pr-12">
                            <button type="button" data-toggle="password_confirmation" aria-label="Tampilkan password"
                                    class="absolute inset-y-0 right-1.5 my-auto h-10 w-10 flex items-center justify-center rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-all">
                                <span class="material-symbols-outlined text-xl">visibility</span>
                            </button>
                        </div>
                        <p id="pwMatch" class="hidden text-xs font-medium items-center gap-1"></p>
                    </div>
                </div>

                <div class="mt-5 flex items-start gap-3 rounded-2xl bg-amber-50 border border-amber-200 p-4">
                    <span class="material-symbols-outlined text-amber-500 text-xl flex-shrink-0" style="font-variation-settings:'FILL' 1;">tips_and_updates</span>
                    <p class="text-xs sm:text-[13px] text-amber-800 font-medium leading-relaxed">
                        Gunakan kombinasi huruf, angka, dan simbol agar password lebih aman.
                    </p>
                </div>
            </section>

            <!-- Aksi -->
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                <button type="reset" id="resetBtn"
                        class="w-full sm:w-auto px-6 py-3.5 rounded-2xl border-2 border-slate-200 text-slate-700 hover:border-slate-300 hover:bg-slate-50 font-bold text-sm transition-all">
                    Reset
                </button>
                <button type="submit" id="submitBtn"
                        class="w-full sm:w-auto sm:min-w-[12rem] flex justify-center items-center gap-2 py-3.5 px-6 rounded-2xl text-white bg-gradient-to-r from-orange-600 to-amber-500 hover:brightness-110 font-extrabold text-sm focus:outline-none focus-visible:ring-4 focus-visible:ring-orange-500/30 active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed transition-all duration-200 shadow-lg shadow-orange-500/30">
                    <span id="submitIcon" class="material-symbols-outlined text-[20px]">save</span>
                    <svg id="submitSpin" class="hidden animate-spin h-5 w-5" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
                        <path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round" class="opacity-90"/>
                    </svg>
                    <span id="submitLabel">Update Profile</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
(function () {
    const $ = id => document.getElementById(id);

    // Toggle visibilitas password
    document.querySelectorAll('[data-toggle]').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = $(btn.dataset.toggle);
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.querySelector('span').textContent = show ? 'visibility_off' : 'visibility';
            btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        });
    });

    // Indikator kecocokan password
    const pw = $('password'), pc = $('password_confirmation'), msg = $('pwMatch');
    function checkMatch() {
        if (!pc.value) { msg.classList.add('hidden'); msg.classList.remove('flex'); return; }
        const ok = pw.value === pc.value;
        msg.classList.remove('hidden'); msg.classList.add('flex');
        msg.className = 'flex text-xs font-medium items-center gap-1 ' + (ok ? 'text-emerald-600' : 'text-rose-500');
        msg.innerHTML = `<span class="material-symbols-outlined text-sm">${ok ? 'check_circle' : 'error'}</span>${ok ? 'Password cocok' : 'Password belum cocok'}`;
    }
    pw.addEventListener('input', checkMatch);
    pc.addEventListener('input', checkMatch);
    $('resetBtn').addEventListener('click', () => setTimeout(checkMatch, 0));

    // Loading state saat submit
    $('profileForm').addEventListener('submit', () => {
        $('submitBtn').disabled = true;
        $('submitLabel').textContent = 'Menyimpan...';
        $('submitIcon').classList.add('hidden');
        $('submitSpin').classList.remove('hidden');
    });
    window.addEventListener('pageshow', e => {
        if (!e.persisted) return;
        $('submitBtn').disabled = false;
        $('submitLabel').textContent = 'Update Profile';
        $('submitIcon').classList.remove('hidden');
        $('submitSpin').classList.add('hidden');
    });
})();
</script>
@endsection