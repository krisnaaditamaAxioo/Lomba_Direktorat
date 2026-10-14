<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8"/>
    {{-- Zoom tidak lagi dikunci (user-scalable=no menyulitkan pengguna dengan gangguan penglihatan) --}}
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta name="theme-color" content="#022c22"/>
    <title>Daftar Wali Murid – Smart Axioo</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'] } } }
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>

    <style>
        body { -webkit-tap-highlight-color: transparent; -webkit-font-smoothing: antialiased; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24; user-select: none; }

        /* Satu animasi masuk untuk seluruh form, bukan per elemen */
        @keyframes rise { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }
        .rise { animation: rise .55s cubic-bezier(.16, 1, .3, 1) both; }

        @keyframes drift { 0%, 100% { transform: translate3d(0, 0, 0); } 50% { transform: translate3d(0, -18px, 0); } }
        .drift { animation: drift 16s ease-in-out infinite; }

        .dots { background-image: radial-gradient(rgba(255,255,255,.12) 1px, transparent 1px); background-size: 26px 26px; }

        @media (prefers-reduced-motion: reduce) {
            .rise, .drift { animation: none; }
            * { scroll-behavior: auto !important; }
        }
    </style>
</head>

@php
    // Kolom teks yang dipakai berulang
    $fields = [
        ['name' => 'name',  'id' => 'name',     'label' => 'Nama lengkap wali', 'icon' => 'person', 'type' => 'text',  'ph' => 'Contoh: Budi Santoso', 'ac' => 'name',  'mode' => 'text', 'span' => 'sm:col-span-2'],
        ['name' => 'email', 'id' => 'regEmail', 'label' => 'Email',             'icon' => 'mail',   'type' => 'email', 'ph' => 'wali@gmail.com',       'ac' => 'email', 'mode' => 'email', 'span' => ''],
        ['name' => 'phone', 'id' => 'regPhone', 'label' => 'No. WhatsApp',      'icon' => 'call',   'type' => 'tel',   'ph' => '081234567890',         'ac' => 'tel',   'mode' => 'tel',   'span' => ''],
    ];

    $steps = [
        ['icon' => 'badge',    'title' => 'Data diri wali',  'desc' => 'Nama, email aktif, dan nomor WhatsApp.'],
        ['icon' => 'school',   'title' => 'Hubungkan siswa', 'desc' => 'Pilih nama putera/puteri Anda dari daftar siswa.'],
        ['icon' => 'verified_user', 'title' => 'Amankan akun', 'desc' => 'Buat password untuk melindungi akses portal.'],
    ];

    $inputBase  = 'peer h-12 w-full rounded-xl border bg-white pl-12 pr-4 text-base font-medium text-slate-800 placeholder:font-normal placeholder:text-slate-400 shadow-sm transition focus:outline-none focus:ring-4 sm:text-sm';
    $inputOk    = 'border-slate-200 hover:border-slate-300 focus:border-emerald-500 focus:ring-emerald-500/15';
    $inputErr   = 'border-rose-400 bg-rose-50/40 focus:border-rose-500 focus:ring-rose-500/15';
    $iconBase   = 'pointer-events-none absolute inset-y-0 left-0 flex w-12 items-center justify-center text-slate-400 transition peer-focus:text-emerald-600';
    $labelClass = 'mb-1.5 block text-sm font-semibold text-slate-700';

    $knownKeys  = ['name', 'student_id', 'email', 'phone', 'gender', 'password'];
    $otherErrs  = collect($errors->keys())->diff($knownKeys)->flatMap(fn ($k) => $errors->get($k));
@endphp

<body class="min-h-dvh bg-emerald-950 font-sans text-slate-800 antialiased">

<div class="min-h-dvh lg:grid lg:grid-cols-[minmax(0,1fr)_minmax(520px,600px)]">

    {{-- ================= Panel brand (desktop) ================= --}}
    <aside class="relative sticky top-0 hidden h-dvh overflow-hidden bg-gradient-to-br from-[#022c22] via-[#064e3b] to-[#047857] lg:block">
        <div class="drift pointer-events-none absolute -left-24 -top-24 h-[30rem] w-[30rem] rounded-full bg-emerald-400/20 blur-[100px]"></div>
        <div class="drift pointer-events-none absolute -right-16 bottom-10 h-96 w-96 rounded-full bg-teal-300/15 blur-[100px]" style="animation-delay: -6s"></div>
        <div class="dots absolute inset-0 opacity-40"></div>
        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/40 to-transparent"></div>

        <div class="relative z-10 flex h-full flex-col justify-between p-12 xl:p-16">
            <img src="{{ asset('images/SEH.png') }}" alt="Smart Axioo"
                 class="h-11 w-auto self-start object-contain brightness-0 invert"
                 onerror="this.style.display='none'">

            <div class="max-w-lg space-y-10">
                <div>
                    <h1 class="text-4xl font-extrabold leading-[1.15] tracking-tight text-white xl:text-5xl">
                        Dampingi pendidikan putera-puteri Anda, langsung dari ponsel.
                    </h1>
                    <p class="mt-4 text-base leading-relaxed text-emerald-100/80">
                        Buat akun wali murid dan kaitkan dengan data siswa untuk memantau akademik dan kedisiplinan secara cepat dan transparan.
                    </p>
                </div>

                {{-- Tahapan: urutan memang berurutan, jadi garis penghubung dipakai --}}
                <ol class="relative space-y-5">
                    <span class="absolute bottom-6 left-[1.35rem] top-6 w-px bg-white/15" aria-hidden="true"></span>
                    @foreach($steps as $s)
                        <li class="relative flex items-start gap-4">
                            <span class="relative z-10 flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-400/15 text-emerald-200 ring-1 ring-inset ring-white/20 backdrop-blur">
                                <span class="material-symbols-outlined text-[22px]">{{ $s['icon'] }}</span>
                            </span>
                            <div class="pt-0.5">
                                <p class="font-bold text-white">{{ $s['title'] }}</p>
                                <p class="mt-0.5 text-sm text-emerald-100/70">{{ $s['desc'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>

            <p class="text-sm text-emerald-100/50">&copy; {{ date('Y') }} Smart Axioo Class Program. All rights reserved.</p>
        </div>
    </aside>

    {{-- ================= Form ================= --}}
    <main class="relative flex min-h-dvh flex-col bg-gradient-to-b from-[#022c22] via-[#064e3b] to-[#047857] lg:bg-white lg:from-white lg:via-white lg:to-white">

        {{-- Header mobile --}}
        <div class="relative overflow-hidden px-6 pb-14 pt-8 text-center text-white lg:hidden">
            <div class="drift pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-emerald-400/25 blur-3xl"></div>
            <div class="dots pointer-events-none absolute inset-0 opacity-20"></div>
            <img src="{{ asset('images/SEH.png') }}" alt="Smart Axioo"
                 class="relative mx-auto h-9 w-auto object-contain brightness-0 invert"
                 onerror="this.style.display='none'">
            <p class="relative mt-4 text-sm text-emerald-100/80">Pendaftaran wali murid</p>
        </div>

        {{-- Kartu form --}}
        <div class="relative -mt-6 flex-1 rounded-t-[2rem] bg-white px-5 pb-28 pt-8 shadow-[0_-12px_40px_-12px_rgba(2,44,34,.35)] sm:px-10 lg:mt-0 lg:flex lg:items-center lg:rounded-none lg:px-12 lg:pb-12 lg:pt-12 lg:shadow-none xl:px-14">
            <div class="rise mx-auto w-full max-w-md lg:max-w-[28rem]">

                <header class="mb-7">
                    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Daftar akun wali</h2>
                    <p class="mt-1.5 text-sm text-slate-500">Lengkapi data berikut. Kolom bertanda <span class="font-semibold text-rose-500">*</span> wajib diisi.</p>
                </header>

                {{-- Ringkasan error --}}
                @if($errors->any())
                    <div role="alert" class="mb-6 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4">
                        <span class="material-symbols-outlined mt-0.5 shrink-0 text-xl text-rose-500">error</span>
                        <div class="text-sm leading-relaxed text-rose-800">
                            <p class="font-semibold">Pendaftaran belum berhasil</p>
                            <p class="text-rose-700/90">Periksa kolom yang ditandai merah di bawah.</p>
                            @foreach($otherErrs as $err)
                                <p class="mt-1 font-medium">{{ $err }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form id="register-form" action="{{ route('parent.register') }}" method="POST" class="space-y-5" novalidate>
                    @csrf

                    <div class="grid gap-5 sm:grid-cols-2">

                        {{-- Nama wali --}}
                        @php $f = $fields[0]; $has = $errors->has($f['name']); @endphp
                        <div class="{{ $f['span'] }}">
                            <label for="{{ $f['id'] }}" class="{{ $labelClass }}">{{ $f['label'] }} <span class="text-rose-500" aria-hidden="true">*</span></label>
                            <div class="relative">
                                <input type="{{ $f['type'] }}" id="{{ $f['id'] }}" name="{{ $f['name'] }}" required autocomplete="{{ $f['ac'] }}" inputmode="{{ $f['mode'] }}"
                                       value="{{ old($f['name']) }}" placeholder="{{ $f['ph'] }}"
                                       class="{{ $inputBase }} {{ $has ? $inputErr : $inputOk }}">
                                <span class="{{ $iconBase }}"><span class="material-symbols-outlined text-[22px]">{{ $f['icon'] }}</span></span>
                            </div>
                            @error($f['name']) <p class="mt-1.5 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Pilih siswa --}}
                        <div class="sm:col-span-2">
                            <label for="student_id" class="{{ $labelClass }}">Putera / puteri (siswa) <span class="text-rose-500" aria-hidden="true">*</span></label>
                            <div class="relative">
                                <select id="student_id" name="student_id" required
                                        class="{{ $inputBase }} {{ $errors->has('student_id') ? $inputErr : $inputOk }} cursor-pointer appearance-none truncate pr-11">
                                    <option value="" disabled {{ old('student_id') ? '' : 'selected' }}>Pilih nama siswa</option>
                                    @foreach($students as $s)
                                        <option value="{{ $s->id }}" {{ old('student_id') == $s->id ? 'selected' : '' }}>
                                            {{ $s->nama_lengkap }} (NISN: {{ $s->nisn ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                <span class="{{ $iconBase }}"><span class="material-symbols-outlined text-[22px]">school</span></span>
                                <span class="pointer-events-none absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-[22px]">expand_more</span>
                                </span>
                            </div>
                            @error('student_id') <p class="mt-1.5 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Email & WhatsApp --}}
                        @foreach([$fields[1], $fields[2]] as $f)
                            @php $has = $errors->has($f['name']); @endphp
                            <div>
                                <label for="{{ $f['id'] }}" class="{{ $labelClass }}">{{ $f['label'] }} <span class="text-rose-500" aria-hidden="true">*</span></label>
                                <div class="relative">
                                    <input type="{{ $f['type'] }}" id="{{ $f['id'] }}" name="{{ $f['name'] }}" required autocomplete="{{ $f['ac'] }}" inputmode="{{ $f['mode'] }}"
                                           value="{{ old($f['name']) }}" placeholder="{{ $f['ph'] }}"
                                           class="{{ $inputBase }} {{ $has ? $inputErr : $inputOk }}">
                                    <span class="{{ $iconBase }}"><span class="material-symbols-outlined text-[22px]">{{ $f['icon'] }}</span></span>
                                </div>
                                @error($f['name']) <p class="mt-1.5 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        @endforeach
                    </div>

                    {{-- Jenis kelamin --}}
                    <fieldset>
                        <legend class="{{ $labelClass }}">Jenis kelamin wali <span class="text-rose-500" aria-hidden="true">*</span></legend>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach([['L', 'Laki-laki', 'Ayah / wali', 'man'], ['P', 'Perempuan', 'Ibu / wali', 'woman']] as [$val, $title, $sub, $icon])
                                <label class="relative block cursor-pointer">
                                    <input type="radio" name="gender" value="{{ $val }}" class="peer sr-only"
                                           {{ old('gender', 'L') == $val ? 'checked' : '' }}>
                                    <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition hover:border-slate-300
                                                peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:ring-4 peer-checked:ring-emerald-500/10
                                                peer-focus-visible:ring-4 peer-focus-visible:ring-emerald-500/30">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                                            <span class="material-symbols-outlined text-[22px]">{{ $icon }}</span>
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block truncate text-sm font-semibold text-slate-800">{{ $title }}</span>
                                            <span class="block truncate text-xs text-slate-500">{{ $sub }}</span>
                                        </span>
                                    </div>
                                    <span class="absolute right-2.5 top-2.5 flex h-5 w-5 items-center justify-center rounded-full bg-emerald-600 text-white opacity-0 transition peer-checked:opacity-100" aria-hidden="true">
                                        <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'wght' 700">check</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('gender') <p class="mt-1.5 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                    </fieldset>

                    {{-- Password --}}
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="regPassword" class="{{ $labelClass }}">Password <span class="text-rose-500" aria-hidden="true">*</span></label>
                            <div class="relative">
                                <input type="password" id="regPassword" name="password" required minlength="6" autocomplete="new-password"
                                       placeholder="Minimal 6 karakter"
                                       class="{{ $inputBase }} {{ $errors->has('password') ? $inputErr : $inputOk }} pr-12">
                                <span class="{{ $iconBase }}"><span class="material-symbols-outlined text-[22px]">lock</span></span>
                                <button type="button" data-toggle="regPassword" aria-label="Tampilkan password"
                                        class="absolute inset-y-1 right-1 flex w-10 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                            {{-- Indikator kekuatan --}}
                            <div class="mt-2 flex gap-1.5" aria-hidden="true">
                                <span class="h-1 flex-1 rounded-full bg-slate-200 transition-colors" data-bar></span>
                                <span class="h-1 flex-1 rounded-full bg-slate-200 transition-colors" data-bar></span>
                                <span class="h-1 flex-1 rounded-full bg-slate-200 transition-colors" data-bar></span>
                            </div>
                            <p id="strength-text" class="mt-1 text-xs text-slate-500" aria-live="polite">&nbsp;</p>
                            @error('password') <p class="mt-1 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="regPasswordConfirm" class="{{ $labelClass }}">Ulangi password <span class="text-rose-500" aria-hidden="true">*</span></label>
                            <div class="relative">
                                <input type="password" id="regPasswordConfirm" name="password_confirmation" required autocomplete="new-password"
                                       placeholder="Ketik ulang password"
                                       class="{{ $inputBase }} {{ $inputOk }} pr-12">
                                <span class="{{ $iconBase }}"><span class="material-symbols-outlined text-[22px]">lock_reset</span></span>
                                <button type="button" data-toggle="regPasswordConfirm" aria-label="Tampilkan konfirmasi password"
                                        class="absolute inset-y-1 right-1 flex w-10 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                            <p id="match-text" class="mt-2 hidden text-xs font-medium" aria-live="polite"></p>
                        </div>
                    </div>

                    {{-- Kirim --}}
                    <button type="submit" id="submit-btn"
                            class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 px-6 text-sm font-bold text-white shadow-lg shadow-emerald-600/25 transition duration-200 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-emerald-600/30 focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-500/30 active:translate-y-0 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-70 disabled:hover:translate-y-0">
                        <span class="material-symbols-outlined text-[20px]">how_to_reg</span>
                        <span data-label>Daftar akun wali murid</span>
                    </button>
                </form>

                <p class="mt-6 border-t border-slate-100 pt-5 text-center text-sm text-slate-500">
                    Sudah punya akun?
                    <a href="{{ route('parent.login') }}" class="ml-1 font-bold text-emerald-700 underline-offset-4 hover:underline">Masuk di sini</a>
                </p>

                <p class="mt-6 text-center text-xs text-slate-400 lg:hidden">&copy; {{ date('Y') }} Smart Axioo. All rights reserved.</p>
            </div>
        </div>

        {{-- Tombol bantuan WhatsApp --}}
        <a href="https://wa.me/6282289635649" target="_blank" rel="noopener"
           aria-label="Bantuan lewat WhatsApp" title="Bantuan WhatsApp"
           class="fixed right-4 z-50 flex h-14 items-center gap-2 rounded-full bg-emerald-600 px-4 text-white shadow-xl shadow-emerald-950/30 transition hover:bg-emerald-500 active:scale-95 focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-400/50 sm:right-6"
           style="bottom: max(1rem, env(safe-area-inset-bottom));">
            <svg class="h-6 w-6 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.488-.491-.672-.5-.198-.01-.422-.015-.645-.015-.223 0-.585.085-.892.413-.307.329-1.168 1.137-1.168 2.773 0 1.636 1.195 3.213 1.363 3.449.167.237 2.36 3.605 5.719 5.033.799.335 1.437.531 1.927.678.813.243 1.551.208 2.134.125.651-.09 1.998-.814 2.276-1.599.278-.785.278-1.455.198-1.599-.08-.144-.297-.237-.621-.386l-.001.001z"/>
                <path d="M12.002 2.163c-5.435 0-9.84 4.405-9.84 9.842 0 1.73.464 3.346 1.271 4.779l-1.338 4.894 5.024-1.309c1.39.755 2.988 1.189 4.883 1.189h.001c5.435 0 9.84-4.406 9.84-9.843 0-2.636-1.027-5.107-2.891-6.97a9.804 9.804 0 0 0-6.949-2.881z" fill-opacity=".9"/>
            </svg>
            <span class="hidden pr-1 text-sm font-semibold sm:inline">Butuh bantuan?</span>
        </a>
    </main>
</div>

<script>
(function () {
    var form = document.getElementById('register-form');
    var pass = document.getElementById('regPassword');
    var confirmPass = document.getElementById('regPasswordConfirm');
    var bars = document.querySelectorAll('[data-bar]');
    var strengthText = document.getElementById('strength-text');
    var matchText = document.getElementById('match-text');
    var btn = document.getElementById('submit-btn');
    var btnHtml = btn.innerHTML;

    // Tampilkan / sembunyikan password
    document.querySelectorAll('[data-toggle]').forEach(function (b) {
        b.addEventListener('click', function () {
            var el = document.getElementById(this.dataset.toggle);
            var show = el.type === 'password';
            el.type = show ? 'text' : 'password';
            this.querySelector('span').textContent = show ? 'visibility_off' : 'visibility';
            this.setAttribute('aria-label', (show ? 'Sembunyikan ' : 'Tampilkan ') + 'password');
        });
    });

    // Kekuatan password
    var levels = [
        { text: 'Terlalu pendek', cls: 'bg-rose-400' },
        { text: 'Cukup', cls: 'bg-amber-400' },
        { text: 'Bagus', cls: 'bg-emerald-500' },
        { text: 'Kuat', cls: 'bg-emerald-600' }
    ];
    function strength(v) {
        if (v.length < 6) return 0;
        var s = 1;
        if (v.length >= 8) s++;
        if (/[A-Za-z]/.test(v) && /\d/.test(v)) s++;
        return Math.min(s, 3);
    }
    function renderStrength() {
        var v = pass.value;
        bars.forEach(function (b) {
            b.className = 'h-1 flex-1 rounded-full transition-colors bg-slate-200';
        });
        if (!v) { strengthText.innerHTML = '&nbsp;'; return; }
        var s = strength(v);
        var lvl = levels[s];
        // s = 0 → 1 batang merah; s = 1..3 → 1..3 batang
        for (var i = 0; i < Math.max(s, 1); i++) {
            bars[i].classList.remove('bg-slate-200');
            bars[i].classList.add(lvl.cls);
        }
        strengthText.textContent = lvl.text;
    }

    // Kecocokan password
    function renderMatch() {
        if (!confirmPass.value) { matchText.classList.add('hidden'); return true; }
        var ok = pass.value === confirmPass.value;
        matchText.classList.remove('hidden', 'text-rose-600', 'text-emerald-600');
        matchText.classList.add(ok ? 'text-emerald-600' : 'text-rose-600');
        matchText.textContent = ok ? 'Password sudah sama' : 'Password belum sama';
        return ok;
    }

    pass.addEventListener('input', function () { renderStrength(); renderMatch(); });
    confirmPass.addEventListener('input', renderMatch);

    // Validasi ringan + cegah kirim ganda
    form.addEventListener('submit', function (e) {
        if (!form.checkValidity() || !renderMatch()) {
            e.preventDefault();
            form.reportValidity();
            if (pass.value !== confirmPass.value) confirmPass.focus();
            return;
        }
        setTimeout(function () {
            btn.disabled = true;
            btn.innerHTML = '<svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">' +
                '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>' +
                '<path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path></svg>' +
                '<span>Mendaftarkan…</span>';
        }, 0);
    });

    // Pulihkan tombol jika pengguna kembali lewat tombol Back
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) { btn.disabled = false; btn.innerHTML = btnHtml; }
    });

    renderStrength();
})();
</script>
</body>
</html>