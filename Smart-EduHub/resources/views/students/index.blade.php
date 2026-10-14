@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
<style>
    /* Mobile: sembunyikan navbar putih bawaan layout & rapatkan header ke tepi atas */
    @media (max-width: 767px) {
        body > header, #topbar, #navbar, .topbar, [data-topbar] { display: none !important; }
        main, .main-content, [data-main] { padding-top: 0 !important; }
    }
</style>
@php
    $statusClass = fn ($s) => $s->status === 0
        ? 'bg-emerald-50 text-emerald-600 ring-emerald-100'
        : 'bg-amber-50 text-amber-600 ring-amber-100';
@endphp

<div class="space-y-5 lg:space-y-6 pb-28 md:pb-8">

    {{-- ================= HERO HEADER (gaya seperti halaman Pembayaran Cepat) ================= --}}
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
                <span class="material-symbols-outlined text-orange-600 text-[18px]">groups</span>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total</span>
                <span class="text-sm font-black">{{ ($stats['total'] ?? 0) }}</span>
            </div>
        </div>

        <div class="relative mt-4 text-center md:text-left md:mt-0 md:-mt-6">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-orange-100">Kelola Siswa</p>
            <h2 class="mt-1 text-3xl sm:text-4xl font-black tracking-tight">Data Siswa</h2>
            <p class="mt-1.5 text-xs sm:text-sm text-orange-50/90">Daftar siswa berbasis angkatan tanpa kelas dan jurusan.</p>
        </div>

        <div class="relative mt-5 flex flex-col sm:flex-row gap-2.5 md:justify-start">
            <a href="{{ route('students.create') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-extrabold text-orange-600 shadow-lg hover:bg-orange-50 active:scale-[.98] transition">
                <span class="material-symbols-outlined text-[20px]">person_add</span>
                Tambah Siswa
            </a>
            <a href="{{ route('students.print-cards') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white/15 px-5 py-3 text-sm font-bold text-white ring-1 ring-white/30 backdrop-blur hover:bg-white/25 active:scale-[.98] transition">
                <span class="material-symbols-outlined text-[20px]">badge</span>
                Cetak Kartu
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

    @if(session('debug'))
        <div class="rounded-2xl border border-orange-200 bg-orange-50 p-4">
            <p class="text-xs font-bold text-orange-800">{{ session('debug') }}</p>
        </div>
    @endif

    @if(session('import_errors'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
            <div class="mb-3 flex items-start gap-3">
                <span class="material-symbols-outlined text-rose-600">error</span>
                <p class="text-sm font-bold text-rose-800">
                    Import selesai dengan error!
                    <span class="font-extrabold">Berhasil: {{ session('imported', 0) }} data</span>,
                    <span class="font-extrabold">Error: {{ session('error_count', 0) }} data</span>
                </p>
            </div>
            <div class="max-h-80 overflow-y-auto rounded-xl border border-rose-100 bg-white p-4">
                <ul class="space-y-2">
                    @foreach((array) session('import_errors') as $msg)
                        <li class="border-b border-rose-50 pb-2 text-sm text-rose-700 last:border-0">{{ $msg }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- ================= STATISTIK ================= --}}
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        @php
            $cards = [
                ['label' => 'Total',       'value' => ($stats['total'] ?? 0),    'icon' => 'groups',        'text' => 'text-slate-900',   'bg' => 'bg-slate-100 text-slate-600'],
                ['label' => 'Aktif',       'value' => ($stats['active'] ?? 0),   'icon' => 'check_circle',  'text' => 'text-emerald-600', 'bg' => 'bg-emerald-50 text-emerald-600'],
                ['label' => 'Tidak Aktif', 'value' => ($stats['pending'] ?? 0),  'icon' => 'pause_circle',  'text' => 'text-amber-600',   'bg' => 'bg-amber-50 text-amber-600'],
                ['label' => 'Cuti',        'value' => ($stats['on_leave'] ?? 0), 'icon' => 'event_busy',    'text' => 'text-rose-600',    'bg' => 'bg-rose-50 text-rose-600'],
            ];
        @endphp
        @foreach($cards as $c)
            <div class="flex items-center gap-3 rounded-3xl border border-slate-100 bg-white p-4 sm:p-5 shadow-sm">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl {{ $c['bg'] }}">
                    <span class="material-symbols-outlined">{{ $c['icon'] }}</span>
                </span>
                <div class="min-w-0">
                    <p class="truncate text-[11px] font-bold text-slate-400">{{ $c['label'] }}</p>
                    <p class="text-2xl sm:text-3xl font-black leading-tight {{ $c['text'] }}">{{ $c['value'] }}</p>
                </div>
            </div>
        @endforeach
    </section>

    {{-- ================= FILTER & AKSI ================= --}}
    <section class="rounded-3xl border border-slate-100 bg-white p-4 sm:p-6 shadow-sm space-y-4">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-orange-600">search</span>
            <h3 class="text-base font-extrabold text-slate-900">Cari &amp; Filter</h3>
        </div>

        <form action="{{ route('students') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-[1fr_190px_190px_auto] gap-2.5">
            <label class="relative sm:col-span-2 xl:col-span-1">
                <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">badge</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, username, atau ID siswa"
                       class="w-full rounded-2xl border-slate-200 bg-slate-50 py-3 pl-11 pr-3 text-sm font-semibold text-slate-700 placeholder:font-medium placeholder:text-slate-400 focus:border-orange-400 focus:ring-4 focus:ring-orange-500/10">
            </label>

            <select name="status" onchange="this.form.submit()"
                    class="rounded-2xl border-slate-200 bg-slate-50 py-3 px-4 text-sm font-semibold text-slate-700 focus:border-orange-400 focus:ring-4 focus:ring-orange-500/10">
                <option value="all">Semua Status</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Tidak Aktif</option>
                <option value="on leave" @selected(request('status') === 'on leave')>Cuti</option>
            </select>

            <select name="angkatan" onchange="this.form.submit()"
                    class="rounded-2xl border-slate-200 bg-slate-50 py-3 px-4 text-sm font-semibold text-slate-700 focus:border-orange-400 focus:ring-4 focus:ring-orange-500/10">
                <option value="all">Semua Angkatan</option>
                @foreach(($angkatans ?? []) as $angkatan)
                    <option value="{{ $angkatan->code }}" @selected((string) request('angkatan') === (string) $angkatan->code)>{{ $angkatan->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="sm:col-span-2 xl:col-span-1 inline-flex items-center justify-center gap-2 rounded-2xl bg-orange-600 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-orange-200 hover:bg-orange-700 active:scale-[.98] transition">
                <span class="material-symbols-outlined text-[20px]">search</span>
                Cari Siswa
            </button>
        </form>

        {{-- Aksi ekspor/impor: scroll horizontal di mobile, wrap di desktop --}}
        <div class="-mx-4 sm:mx-0 overflow-x-auto sm:overflow-visible px-4 sm:px-0 pt-1 border-t border-slate-100">
            <div class="flex sm:flex-wrap gap-2 pt-3 w-max sm:w-auto">
                <a href="{{ route('students.exportPdf', ['status' => request('status', 'all'), 'angkatan' => request('angkatan', 'all'), 'search' => request('search', '')]) }}"
                   class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-xl bg-orange-50 px-3.5 py-2 text-xs sm:text-sm font-bold text-orange-700 hover:bg-orange-100 transition">
                    <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span> PDF
                </a>
                <a href="{{ route('students.exportExcel') }}" class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-xl border border-slate-200 px-3.5 py-2 text-xs sm:text-sm font-bold text-slate-700 hover:bg-slate-50 transition">
                    <span class="material-symbols-outlined text-[18px]">table_view</span> Excel
                </a>
                <a href="{{ route('students.export') }}" class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-xl border border-slate-200 px-3.5 py-2 text-xs sm:text-sm font-bold text-slate-700 hover:bg-slate-50 transition">
                    <span class="material-symbols-outlined text-[18px]">csv</span> CSV
                </a>
                <a href="{{ route('students.downloadTemplate') }}" class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-xl border border-slate-200 px-3.5 py-2 text-xs sm:text-sm font-bold text-slate-700 hover:bg-slate-50 transition">
                    <span class="material-symbols-outlined text-[18px]">download</span> Template
                </a>
                <button type="button" onclick="openImport()" class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-xl bg-slate-900 px-3.5 py-2 text-xs sm:text-sm font-bold text-white hover:bg-slate-800 transition">
                    <span class="material-symbols-outlined text-[18px]">upload</span> Import
                </button>
            </div>
        </div>
    </section>

    {{-- ================= DAFTAR SISWA ================= --}}
    <section>
        {{-- DESKTOP / TABLET: tabel --}}
        <div class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px]">
                    <thead class="bg-slate-50/80 text-left">
                        <tr class="text-xs font-bold text-slate-400">
                            <th class="px-4 sm:px-6 py-3.5">Siswa</th>
                            <th class="px-4 sm:px-6 py-3.5">ID Siswa</th>
                            <th class="hidden lg:table-cell px-6 py-4">Email</th>
                            <th class="px-4 sm:px-6 py-3.5">Angkatan</th>
                            <th class="px-4 sm:px-6 py-3.5">Status</th>
                            <th class="px-4 sm:px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($students as $student)
                            <tr class="transition-colors hover:bg-orange-50/40">
                                <td class="px-4 sm:px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-orange-500 to-red-600 font-black text-white">
                                            {{ strtoupper(mb_substr((string) ($student->nama_lengkap ?? '?'), 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="truncate font-extrabold text-slate-900">{{ $student->nama_lengkap }}</div>
                                            <div class="truncate text-xs text-slate-500">{{ $student->username }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 sm:px-6 py-3.5 text-sm font-semibold text-slate-600">{{ $student->id_siswa }}</td>
                                <td class="hidden lg:table-cell px-6 py-4 text-sm text-slate-600">{{ $student->email ?: '-' }}</td>
                                <td class="px-4 sm:px-6 py-3.5">
                                    <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-black text-orange-600 whitespace-nowrap">Angkatan {{ $student->angkatan ?: '-' }}</span>
                                </td>
                                <td class="px-4 sm:px-6 py-3.5">
                                    <span class="rounded-full px-3 py-1 text-xs font-black ring-1 {{ $statusClass($student) }}">{{ $student->status_text }}</span>
                                </td>
                                <td class="px-4 sm:px-6 py-3.5">
                                    <div class="flex justify-end gap-1.5">
                                        <a href="{{ route('students.show', $student) }}" class="rounded-xl border border-slate-200 p-2 text-slate-600 transition hover:bg-slate-50" title="Lihat">
                                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                                        </a>
                                        <a href="{{ route('students.edit', $student) }}" class="rounded-xl border border-slate-200 p-2 text-slate-600 transition hover:bg-slate-50" title="Edit">
                                            <span class="material-symbols-outlined text-[20px]">edit</span>
                                        </a>
                                        <form action="{{ route('students.reset-pin', $student) }}" method="POST" onsubmit="return confirm('Reset PIN siswa ini ke 123456?')">
                                            @csrf
                                            <button type="submit" class="rounded-xl bg-amber-50 p-2 text-amber-600 transition hover:bg-amber-100" title="Reset PIN">
                                                <span class="material-symbols-outlined text-[20px]">lock_reset</span>
                                            </button>
                                        </form>
                                        <form action="{{ route('students.destroy', $student) }}" method="POST" onsubmit="return confirm('Hapus siswa ini?')">
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
                                <td colspan="6" class="px-6 py-14 text-center">
                                    <span class="material-symbols-outlined text-5xl text-slate-300">person_off</span>
                                    <p class="mt-2 font-bold text-slate-600">Belum ada data siswa</p>
                                    <p class="text-xs text-slate-400">Tambah siswa baru atau import dari file Excel.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $students->links() }}
        </div>
    </section>
</div>

{{-- ================= MODAL IMPORT (bottom sheet di mobile, dialog di desktop) ================= --}}
<div id="importModal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center bg-slate-900/60 backdrop-blur-sm sm:p-4" onclick="if(event.target===this) closeImport()">
    <div class="w-full max-w-lg space-y-4 rounded-t-[2rem] sm:rounded-3xl bg-white p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="mx-auto h-1.5 w-12 rounded-full bg-slate-200 sm:hidden"></div>

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-orange-50 text-orange-600">
                    <span class="material-symbols-outlined">upload_file</span>
                </span>
                <h3 class="text-xl font-black text-slate-900">Import Data</h3>
            </div>
            <button type="button" onclick="closeImport()" class="grid h-9 w-9 place-items-center rounded-full text-slate-400 hover:bg-slate-100" aria-label="Tutup">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
            <p class="text-xs font-bold text-slate-400">Format header file</p>
            <p class="mt-1 break-words text-xs leading-relaxed text-slate-600">
                ID Siswa, Username, PIN, Status, Nama Lengkap, ID Angkatan, NISN, No NIK, Jenis Kelamin (L/P), Nama Ibu, Agama, Tinggi Badan, Berat Badan, Tempat Lahir, Tanggal Lahir (YYYY-MM-DD), Alamat, NIS Siswa, Email
            </p>
        </div>

        <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="file" name="file" accept=".csv,.xlsx,.xls" required
                   class="w-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-3 text-sm text-slate-600 file:mr-3 file:rounded-xl file:border-0 file:bg-orange-600 file:px-4 file:py-2 file:text-sm file:font-bold file:text-white hover:file:bg-orange-700">
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5">
                <button type="button" onclick="closeImport()" class="rounded-2xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit" class="rounded-2xl bg-orange-600 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-orange-200 hover:bg-orange-700">Upload</button>
            </div>
        </form>
    </div>
</div>

<script>
    const importModal = document.getElementById('importModal');
    function openImport()  { importModal.classList.remove('hidden'); importModal.classList.add('flex'); }
    function closeImport() { importModal.classList.add('hidden');    importModal.classList.remove('flex'); }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeImport(); });

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