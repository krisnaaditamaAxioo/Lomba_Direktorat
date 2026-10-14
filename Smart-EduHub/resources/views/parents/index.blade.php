@extends('layouts.app')

@section('title', 'Data Orang Tua')

@section('content')
<style>
    /* Mobile: sembunyikan navbar putih bawaan layout & rapatkan header ke tepi atas */
    @media (max-width: 767px) {
        body > header, #topbar, #navbar, .topbar, [data-topbar] { display: none !important; }
        main, .main-content, [data-main] { padding-top: 0 !important; }
    }
</style>

<div class="space-y-5 lg:space-y-6 pb-28 md:pb-8">

    {{-- ================= HERO HEADER ================= --}}
    <header data-hero class="relative overflow-hidden rounded-b-[2rem] md:rounded-3xl bg-gradient-to-br from-orange-500 via-orange-600 to-red-700 text-white px-5 pt-8 pb-8 sm:px-8 sm:py-9 shadow-xl shadow-orange-200/60">
        <div class="pointer-events-none absolute -top-10 -right-10 h-44 w-44 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute top-16 -right-4 h-28 w-28 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-12 -left-8 h-36 w-36 rounded-full bg-black/10"></div>

        <div class="relative flex items-start justify-between gap-3">
            <a href="{{ url()->previous() }}" class="md:hidden grid h-10 w-10 place-items-center rounded-full bg-white/20 backdrop-blur hover:bg-white/30 transition" aria-label="Kembali">
                <span class="material-symbols-outlined text-[22px]">arrow_back</span>
            </a>
            <div class="hidden md:block"></div>
            <div class="flex items-center gap-2 rounded-full bg-white text-slate-900 px-3.5 py-2 shadow-lg">
                <span class="material-symbols-outlined text-orange-600 text-[18px]">family_restroom</span>
                <span class="text-[10px] font-bold text-slate-400">Akun</span>
                <span class="text-sm font-black">{{ $parents->count() }}</span>
            </div>
        </div>

        <div class="relative mt-4 text-center md:text-left md:-mt-6">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-orange-100">Data Orang Tua</p>
            <h2 class="mt-1 text-3xl sm:text-4xl font-black tracking-tight">Akun Orang Tua</h2>
            <p class="mt-1.5 text-xs sm:text-sm text-orange-50/90">Kelola akun login orang tua / wali siswa.</p>
        </div>

        <div class="relative mt-5">
            <a href="{{ route('parents.create') }}" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-extrabold text-orange-600 shadow-lg hover:bg-orange-50 active:scale-[.98] transition">
                <span class="material-symbols-outlined text-[20px]">person_add</span>
                Tambah Akun
            </a>
        </div>
    </header>

    {{-- ================= ALERTS ================= --}}
    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
            <span class="material-symbols-outlined text-emerald-600">check_circle</span>
            <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4">
            <span class="material-symbols-outlined text-rose-600">error</span>
            <p class="text-sm font-bold text-rose-800">{{ session('error') }}</p>
        </div>
    @endif

    {{-- ================= TABEL ================= --}}
    <section class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm">
        <div class="flex items-center gap-2 border-b border-slate-100 px-4 py-4 sm:px-6">
            <span class="material-symbols-outlined text-orange-600">family_restroom</span>
            <h3 class="text-base font-extrabold text-slate-900">Daftar Akun</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px] text-left">
                <thead class="bg-slate-50/80">
                    <tr class="text-xs font-bold text-slate-400">
                        <th class="px-4 sm:px-6 py-3.5">Nama</th>
                        <th class="hidden lg:table-cell px-6 py-3.5">Email</th>
                        <th class="px-4 sm:px-6 py-3.5">Siswa</th>
                        <th class="hidden sm:table-cell px-4 sm:px-6 py-3.5">HP</th>
                        <th class="px-4 sm:px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($parents as $parent)
                        <tr class="transition-colors hover:bg-orange-50/40">
                            <td class="px-4 sm:px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-orange-500 to-red-600 font-black text-white shadow-md shadow-orange-200/60">
                                        {{ strtoupper(mb_substr((string) ($parent->name ?? '?'), 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate font-extrabold text-slate-900">{{ $parent->name }}</p>
                                        <p class="truncate text-xs text-slate-500">
                                            {{ $parent->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                            <span class="lg:hidden">&middot; {{ $parent->email ?: '-' }}</span>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="hidden lg:table-cell px-6 py-3.5 text-sm text-slate-600">{{ $parent->email ?: '-' }}</td>
                            <td class="px-4 sm:px-6 py-3.5">
                                @if($parent->student)
                                    <span class="inline-block max-w-[180px] truncate rounded-full bg-orange-50 px-3 py-1 text-xs font-black text-orange-600 align-middle">{{ $parent->student->nama_lengkap }}</span>
                                @else
                                    <span class="text-sm text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="hidden sm:table-cell px-4 sm:px-6 py-3.5 text-sm font-semibold text-slate-600 whitespace-nowrap">{{ $parent->phone ?: '-' }}</td>
                            <td class="px-4 sm:px-6 py-3.5">
                                <div class="flex justify-end gap-1.5">
                                    <a href="{{ route('parents.edit', $parent) }}" class="rounded-xl border border-slate-200 p-2 text-slate-600 transition hover:bg-slate-50" title="Edit">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </a>
                                    <form action="{{ route('parents.destroy', $parent) }}" method="POST" onsubmit="return confirm('Hapus akun orang tua ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-xl bg-rose-50 p-2 text-rose-600 transition hover:bg-rose-100" title="Hapus">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-14 text-center">
                                <span class="material-symbols-outlined text-5xl text-slate-300">family_restroom</span>
                                <p class="mt-2 font-bold text-slate-600">Belum ada akun orang tua</p>
                                <a href="{{ route('parents.create') }}" class="mt-1 inline-block text-sm font-bold text-orange-600 hover:underline">Tambah akun pertama</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($parents, 'links'))
            <div class="border-t border-slate-100 p-4 sm:p-6">
                {{ $parents->links() }}
            </div>
        @endif
    </section>
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