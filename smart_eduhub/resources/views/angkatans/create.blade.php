@extends('layouts.app')

@section('title', 'Tambah Angkatan')

@section('content')
<style>
    /* Mobile: sembunyikan navbar putih bawaan layout & rapatkan header ke tepi atas */
    @media (max-width: 767px) {
        body > header, #topbar, #navbar, .topbar, [data-topbar] { display: none !important; }
        main, .main-content, [data-main] { padding-top: 0 !important; }
    }
    .field {
        width: 100%;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        padding: .8rem 1rem;
        font-size: .875rem;
        font-weight: 600;
        color: #334155;
    }
    .field:focus { outline: none; border-color: #fb923c; box-shadow: 0 0 0 4px rgba(249, 115, 22, .12); }
    .field::placeholder { color: #94a3b8; font-weight: 500; }
</style>

<div class="mx-auto max-w-3xl space-y-5 lg:space-y-6 pb-28 md:pb-8">

    {{-- ================= HERO HEADER ================= --}}
    <header data-hero class="relative overflow-hidden rounded-b-[2rem] md:rounded-3xl bg-gradient-to-br from-orange-500 via-orange-600 to-red-700 text-white px-5 pt-8 pb-8 sm:px-8 sm:py-9 shadow-xl shadow-orange-200/60">
        <div class="pointer-events-none absolute -top-10 -right-10 h-44 w-44 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute top-16 -right-4 h-28 w-28 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-12 -left-8 h-36 w-36 rounded-full bg-black/10"></div>

        <div class="relative flex items-start justify-between gap-3">
            <a href="{{ route('angkatans.index') }}" class="grid h-10 w-10 place-items-center rounded-full bg-white/20 backdrop-blur hover:bg-white/30 transition" aria-label="Kembali">
                <span class="material-symbols-outlined text-[22px]">arrow_back</span>
            </a>
            <div class="flex items-center gap-2 rounded-full bg-white text-slate-900 px-3.5 py-2 shadow-lg">
                <span class="material-symbols-outlined text-orange-600 text-[18px]">add_circle</span>
                <span class="text-[10px] font-bold text-slate-400">Data baru</span>
            </div>
        </div>

        <div class="relative mt-4 text-center md:text-left">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-orange-100">Master Data</p>
            <h2 class="mt-1 text-3xl sm:text-4xl font-black tracking-tight">Tambah Angkatan</h2>
            <p class="mt-1.5 text-xs sm:text-sm text-orange-50/90">Tambahkan master angkatan baru untuk dipakai pada siswa dan pendaftaran.</p>
        </div>
    </header>

    {{-- ================= ERROR RINGKAS ================= --}}
    @if($errors->any())
        <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4">
            <span class="material-symbols-outlined text-rose-600">error</span>
            <div class="text-sm">
                <p class="font-bold text-rose-800">Data belum bisa disimpan</p>
                <p class="mt-0.5 text-rose-700">Periksa kembali kolom yang ditandai merah di bawah.</p>
            </div>
        </div>
    @endif

    {{-- ================= FORM ================= --}}
    <form action="{{ route('angkatans.store') }}" method="POST" class="space-y-5">
        @csrf

        {{-- Identitas --}}
        <section class="rounded-3xl border border-slate-100 bg-white p-5 sm:p-7 shadow-sm">
            <div class="mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-orange-600">badge</span>
                <h3 class="text-base font-extrabold text-slate-900">Identitas Angkatan</h3>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5">
                <div>
                    <label for="code" class="mb-1.5 block text-xs font-bold text-slate-500">Kode Angkatan</label>
                    <input id="code" type="text" name="code" value="{{ old('code') }}" placeholder="Contoh: 2024" class="field @error('code') !border-rose-300 @enderror" required>
                    @error('code') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="name" class="mb-1.5 block text-xs font-bold text-slate-500">Nama Angkatan</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Angkatan 2024" class="field @error('name') !border-rose-300 @enderror" required>
                    @error('name') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tahun_masuk" class="mb-1.5 block text-xs font-bold text-slate-500">Tahun Masuk</label>
                    <input id="tahun_masuk" type="number" name="tahun_masuk" value="{{ old('tahun_masuk') }}" min="1900" max="2100" inputmode="numeric" placeholder="Contoh: 2024" class="field @error('tahun_masuk') !border-rose-300 @enderror">
                    @error('tahun_masuk') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="sort_order" class="mb-1.5 block text-xs font-bold text-slate-500">Urutan</label>
                    <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', 1) }}" min="1" inputmode="numeric" class="field @error('sort_order') !border-rose-300 @enderror" required>
                    @error('sort_order') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        {{-- Pengelolaan --}}
        <section class="rounded-3xl border border-slate-100 bg-white p-5 sm:p-7 shadow-sm">
            <div class="mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-orange-600">manage_accounts</span>
                <h3 class="text-base font-extrabold text-slate-900">Pengelolaan</h3>
            </div>

            <div class="space-y-4 sm:space-y-5">
                <div>
                    <label for="wali_kelas_id" class="mb-1.5 block text-xs font-bold text-slate-500">Wali Kelas</label>
                    <select id="wali_kelas_id" name="wali_kelas_id" class="field @error('wali_kelas_id') !border-rose-300 @enderror">
                        <option value="">Pilih Wali Kelas</option>
                        @foreach(($waliKelasList ?? []) as $waliKelas)
                            <option value="{{ $waliKelas->id }}" {{ old('wali_kelas_id') == $waliKelas->id ? 'selected' : '' }}>{{ $waliKelas->name }}</option>
                        @endforeach
                    </select>
                    @error('wali_kelas_id') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <label for="is_active" class="flex cursor-pointer items-center justify-between gap-4 rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <span class="flex items-start gap-3">
                        <span class="material-symbols-outlined mt-0.5 text-slate-400">info</span>
                        <span>
                            <span class="block text-sm font-bold text-slate-700">Angkatan aktif</span>
                            <span class="block text-xs text-slate-500">Hanya angkatan aktif yang bisa dipilih pada siswa dan pendaftaran.</span>
                        </span>
                    </span>
                    <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', true))
                           class="h-6 w-6 shrink-0 rounded-lg border-slate-300 text-orange-600 focus:ring-orange-500">
                </label>
            </div>
        </section>

        {{-- Aksi --}}
        <div class="flex flex-col-reverse gap-2.5 sm:flex-row sm:justify-end">
            <a href="{{ route('angkatans.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-3.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">Batal</a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-orange-600 px-6 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-orange-200 transition hover:bg-orange-700 active:scale-[.98]">
                <span class="material-symbols-outlined text-[20px]">save</span>
                Simpan Angkatan
            </button>
        </div>
    </form>
</div>

<script>
    // Mobile: tarik header sampai menempel penuh ke tepi atas/kiri/kanan layar,
    // berapa pun padding yang dipasang layout di atasnya.
    (function () {
        const hero = document.querySelector('[data-hero]');
        if (!hero) return;
        function fit() {
            hero.style.marginTop = hero.style.marginLeft = hero.style.marginRight = '';
            if (window.innerWidth >= 768) return;
            const r = hero.getBoundingClientRect();
            const top = r.top + window.scrollY;
            hero.style.marginTop   = -Math.max(top, 0) + 'px';
            hero.style.marginLeft  = -Math.max(r.left, 0) + 'px';
            hero.style.marginRight = -Math.max(window.innerWidth - r.right, 0) + 'px';
        }
        fit();
        window.addEventListener('load', fit);
        window.addEventListener('resize', fit);
        if (document.fonts && document.fonts.ready) document.fonts.ready.then(fit);
    })();
</script>
@endsection