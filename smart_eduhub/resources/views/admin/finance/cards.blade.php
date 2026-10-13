@extends('layouts.app')

@section('title', 'Daftar Kartu')

@section('content')
<style>
    /* Mobile: sembunyikan navbar putih bawaan layout & rapatkan header ke tepi atas */
    @media (max-width: 767px) {
        body > header, #topbar, #navbar, .topbar, [data-topbar] { display: none !important; }
        main, .main-content, [data-main] { padding-top: 0 !important; }
    }
</style>
@php
    $user = auth()->user();
    $statusMap = [
        'active' => ['label' => 'Aktif',      'badge' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'],
        'frozen' => ['label' => 'Dibekukan',  'badge' => 'bg-amber-50 text-amber-700 ring-amber-600/20'],
    ];
    $fallbackStatus = ['label' => null, 'badge' => 'bg-rose-50 text-rose-700 ring-rose-600/20'];

    // Menu keuangan, mengikuti hak akses masing-masing
    $financeNav = array_values(array_filter([
        $user->canAccessFinanceDashboard()      ? ['Dashboard',           'admin.finance.dashboard',            'space_dashboard', false] : null,
        $user->canManageFinanceRequests()       ? ['Pengajuan Kartu',     'admin.finance.requests',             'assignment',      false] : null,
        ['Daftar Kartu',                                                  'admin.finance.cards',                'credit_card',     true],
        $user->canViewFinancePaymentHistory()   ? ['Riwayat Pembayaran',  'admin.finance.payments.history',     'receipt_long',    false] : null,
        $user->canManageFinanceTransactions()   ? ['Manajemen Transaksi', 'admin.finance.transactions.index',   'swap_horiz',      false] : null,
        $user->canAccessFinanceRekap()          ? ['Rekap Keuangan',      'admin.finance.rekap',                'bar_chart',       false] : null,
    ]));
@endphp

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
                <span class="material-symbols-outlined text-orange-600 text-[18px]">credit_card</span>
                <span class="text-[10px] font-bold text-slate-400">Kartu</span>
                <span class="text-sm font-black">{{ method_exists($cards, 'total') ? $cards->total() : $cards->count() }}</span>
            </div>
        </div>

        <div class="relative mt-4 text-center md:text-left md:-mt-6">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-orange-100">Smart EduHub Wallet</p>
            <h2 class="mt-1 text-3xl sm:text-4xl font-black tracking-tight">Daftar Kartu</h2>
            <p class="mt-1.5 text-xs sm:text-sm text-orange-50/90">Kelola semua kartu dompet digital siswa.</p>
        </div>

        <div class="relative mt-5">
            <a href="{{ route('admin.finance.cards.export-pdf') }}" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-extrabold text-orange-600 shadow-lg hover:bg-orange-50 active:scale-[.98] transition">
                <span class="material-symbols-outlined text-[20px]">picture_as_pdf</span>
                Export PDF
            </a>
        </div>
    </header>

    {{-- ================= NOTIFIKASI ================= --}}
    @if(session('success'))
        <div role="status" class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
            <span class="material-symbols-outlined text-emerald-600">check_circle</span>
            <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif

    {{-- ================= MENU KEUANGAN (geser di mobile) ================= --}}
    <nav class="-mx-4 flex gap-1.5 overflow-x-auto px-4 pb-1 sm:mx-0 sm:flex-wrap sm:px-0" aria-label="Menu keuangan">
        @foreach($financeNav as [$label, $routeName, $icon, $active])
            <a href="{{ route($routeName) }}" @if($active) aria-current="page" @endif
               class="inline-flex shrink-0 items-center gap-2 rounded-2xl px-4 py-2.5 text-sm font-bold transition
                      {{ $active ? 'bg-orange-600 text-white shadow-md shadow-orange-200' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
                {{ $label }}
            </a>
        @endforeach
    </nav>

    {{-- ================= CARI & FILTER ================= --}}
    <section class="rounded-3xl border border-slate-100 bg-white p-4 sm:p-6 shadow-sm space-y-4">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-orange-600">search</span>
            <h3 class="text-base font-extrabold text-slate-900">Cari &amp; Filter</h3>
        </div>

        <form method="GET" action="{{ route('admin.finance.cards') }}" class="grid grid-cols-1 gap-2.5 sm:grid-cols-2 xl:grid-cols-[1fr_220px_auto_auto]">
            <label class="relative sm:col-span-2 xl:col-span-1">
                <span class="sr-only">Nama siswa</span>
                <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">badge</span>
                <input type="text" name="nama" value="{{ $nama ?? '' }}" placeholder="Cari nama siswa"
                       class="w-full rounded-2xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-3 text-sm font-semibold text-slate-700 placeholder:font-medium placeholder:text-slate-400 focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-500/10">
            </label>

            <select name="angkatan" onchange="this.form.submit()" aria-label="Angkatan"
                    class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-500/10">
                <option value="">Semua Angkatan</option>
                @foreach(($angkatanList ?? []) as $angk)
                    <option value="{{ $angk }}" {{ ($angkatan ?? '') == $angk ? 'selected' : '' }}>{{ $angk }}</option>
                @endforeach
            </select>

            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-orange-600 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-orange-200 transition hover:bg-orange-700 active:scale-[.98]">
                <span class="material-symbols-outlined text-[20px]">search</span>
                Cari
            </button>
            <a href="{{ route('admin.finance.cards') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-200">
                <span class="material-symbols-outlined text-[20px]">refresh</span>
                Reset
            </a>
        </form>
    </section>

    {{-- ================= TABEL ================= --}}
    <section class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm">
        <div class="flex items-center gap-2 border-b border-slate-100 px-4 py-4 sm:px-6">
            <span class="material-symbols-outlined text-orange-600">credit_card</span>
            <h3 class="text-base font-extrabold text-slate-900">Kartu Siswa</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px] text-left">
                <thead class="bg-slate-50/80">
                    <tr class="text-xs font-bold text-slate-400">
                        <th class="px-4 sm:px-6 py-3.5">Siswa</th>
                        <th class="hidden sm:table-cell px-4 sm:px-6 py-3.5">Nomor Kartu</th>
                        <th class="px-4 sm:px-6 py-3.5">Saldo</th>
                        <th class="px-4 sm:px-6 py-3.5">Status</th>
                        <th class="px-4 sm:px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($cards as $card)
                        @php
                            $student = $card->student;
                            $name    = $student->nama_lengkap ?? $student->name ?? '-';
                            $s       = $statusMap[$card->status] ?? $fallbackStatus;
                        @endphp
                        <tr class="transition-colors hover:bg-orange-50/40">
                            <td class="px-4 sm:px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-orange-500 to-red-600 text-sm font-black text-white shadow-md shadow-orange-200/60">
                                        {{ mb_strtoupper(mb_substr((string) $name, 0, 1)) }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-extrabold text-slate-900">{{ $name }}</p>
                                        <p class="text-xs text-slate-500">Angkatan {{ $student->angkatan ?? '-' }}</p>
                                        <p class="font-mono text-[11px] font-semibold text-slate-400 sm:hidden">{{ $card->card_number }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="hidden sm:table-cell px-4 sm:px-6 py-3.5">
                                <span class="rounded-lg bg-slate-50 px-2.5 py-1 font-mono text-sm font-bold text-slate-700 ring-1 ring-slate-100">{{ $card->card_number }}</span>
                            </td>
                            <td class="px-4 sm:px-6 py-3.5 whitespace-nowrap">
                                <p class="text-sm font-black text-emerald-600">Rp {{ number_format($card->balance, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-4 sm:px-6 py-3.5">
                                <span class="whitespace-nowrap rounded-full px-3 py-1 text-xs font-black ring-1 ring-inset {{ $s['badge'] }}">
                                    {{ $s['label'] ?? ucfirst($card->status) }}
                                </span>
                            </td>
                            <td class="px-4 sm:px-6 py-3.5">
                                <div class="flex items-center justify-end">
                                    <a href="{{ route('admin.finance.cards.show', $card->id) }}" class="rounded-xl border border-slate-200 p-2 text-orange-600 transition hover:bg-orange-50" title="Detail">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-14 text-center">
                                <span class="material-symbols-outlined text-5xl text-slate-300">credit_card_off</span>
                                <p class="mt-2 font-bold text-slate-600">Belum ada kartu</p>
                                <p class="text-xs text-slate-400">Kartu muncul setelah pengajuan disetujui.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($cards, 'hasPages') && $cards->hasPages())
            <div class="border-t border-slate-100 p-4 sm:p-6">
                {{ $cards->links() }}
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