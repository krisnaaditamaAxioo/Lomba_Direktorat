@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')

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
    $isPaginator = method_exists($transactions, 'total');
    $totalCount  = $isPaginator ? $transactions->total() : $transactions->count();
    $pageItems   = $isPaginator ? collect($transactions->items()) : collect($transactions);
    $pageSum     = $pageItems->sum('amount');
    $hasFilter   = request()->filled('student_name') || request()->filled('date');

    // Menu keuangan, mengikuti hak akses masing-masing
    $financeNav = array_values(array_filter([
        $user->canAccessFinanceDashboard() ? ['Dashboard',           'admin.finance.dashboard',        'space_dashboard', false] : null,
        $user->canViewFinanceCards()       ? ['Transaksi Kartu',     'admin.finance.cards',            'credit_card',     false] : null,
        ['Riwayat Pembayaran',                                       'admin.finance.payments.history', 'receipt_long',    true],
        $user->canAccessFinanceRekap()     ? ['Rekap Keuangan',      'admin.finance.rekap',            'bar_chart',       false] : null,
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
                <span class="material-symbols-outlined text-orange-600 text-[18px]">receipt_long</span>
                <span class="text-[10px] font-bold text-slate-400">Transaksi</span>
                <span class="text-sm font-black">{{ $totalCount }}</span>
            </div>
        </div>

        <div class="relative mt-4 text-center md:text-left md:-mt-6">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-orange-100">Smart EduHub Wallet</p>
            <h2 class="mt-1 text-3xl sm:text-4xl font-black tracking-tight">Riwayat Pembayaran</h2>
            <p class="mt-1.5 text-xs sm:text-sm text-orange-50/90">Daftar pembayaran yang diproses pada kartu siswa.</p>
        </div>
    </header>

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

    {{-- ================= RINGKASAN ================= --}}
    <section class="grid grid-cols-2 gap-3 sm:gap-4">
        <div class="flex items-center gap-3 rounded-3xl border border-slate-100 bg-white p-4 sm:p-5 shadow-sm">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-slate-100 text-slate-600">
                <span class="material-symbols-outlined">receipt_long</span>
            </span>
            <div class="min-w-0">
                <p class="truncate text-[11px] font-bold text-slate-400">{{ $hasFilter ? 'Hasil filter' : 'Total transaksi' }}</p>
                <p class="text-2xl sm:text-3xl font-black leading-tight text-slate-900">{{ $totalCount }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3 rounded-3xl border border-slate-100 bg-white p-4 sm:p-5 shadow-sm">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-rose-50 text-rose-600">
                <span class="material-symbols-outlined">payments</span>
            </span>
            <div class="min-w-0">
                <p class="truncate text-[11px] font-bold text-slate-400">Nominal halaman ini</p>
                <p class="truncate text-lg sm:text-2xl font-black leading-tight text-rose-600">Rp {{ number_format($pageSum, 0, ',', '.') }}</p>
            </div>
        </div>
    </section>

    {{-- ================= CARI & FILTER ================= --}}
    <section class="rounded-3xl border border-slate-100 bg-white p-4 sm:p-6 shadow-sm space-y-4">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-orange-600">search</span>
            <h3 class="text-base font-extrabold text-slate-900">Cari &amp; Filter</h3>
        </div>

        <form method="GET" action="{{ route('admin.finance.payments.history') }}" class="grid grid-cols-1 gap-2.5 sm:grid-cols-2 xl:grid-cols-[1fr_220px_auto_auto]">
            <label class="relative sm:col-span-2 xl:col-span-1">
                <span class="sr-only">Nama siswa</span>
                <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">badge</span>
                <input type="text" name="student_name" value="{{ request('student_name') }}" placeholder="Cari nama siswa"
                       class="w-full rounded-2xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-3 text-sm font-semibold text-slate-700 placeholder:font-medium placeholder:text-slate-400 focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-500/10">
            </label>

            <input type="date" name="date" value="{{ request('date') }}" aria-label="Tanggal"
                   class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-500/10">

            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-orange-600 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-orange-200 transition hover:bg-orange-700 active:scale-[.98]">
                <span class="material-symbols-outlined text-[20px]">filter_alt</span>
                Filter
            </button>
            @if($hasFilter)
                <a href="{{ route('admin.finance.payments.history') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-200">
                    <span class="material-symbols-outlined text-[20px]">refresh</span>
                    Reset
                </a>
            @endif
        </form>
    </section>

    {{-- ================= TABEL ================= --}}
    <section class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm">
        <div class="flex items-center gap-2 border-b border-slate-100 px-4 py-4 sm:px-6">
            <span class="material-symbols-outlined text-orange-600">receipt_long</span>
            <h3 class="text-base font-extrabold text-slate-900">Daftar Pembayaran</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[420px] text-left">
                <thead class="bg-slate-50/80">
                    <tr class="text-xs font-bold text-slate-400">
                        <th class="hidden sm:table-cell px-4 sm:px-6 py-3.5">Waktu</th>
                        <th class="px-4 sm:px-6 py-3.5">Siswa</th>
                        <th class="hidden lg:table-cell px-6 py-3.5">Kartu</th>
                        <th class="hidden md:table-cell px-6 py-3.5">Deskripsi</th>
                        <th class="hidden xl:table-cell px-6 py-3.5">Petugas</th>
                        <th class="hidden xl:table-cell px-6 py-3.5">Referensi</th>
                        <th class="px-4 sm:px-6 py-3.5 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $transaction)
                        @php
                            $studentName = $transaction->card->student->nama_lengkap ?? '-';
                            $desc = $transaction->description ?: '-';
                        @endphp
                        <tr class="transition-colors hover:bg-orange-50/40">
                            <td class="hidden sm:table-cell whitespace-nowrap px-4 sm:px-6 py-3.5 text-sm font-medium text-slate-500">
                                {{ optional($transaction->created_at)->format('d M Y') }}
                                <span class="block text-xs text-slate-400">{{ optional($transaction->created_at)->format('H:i') }}</span>
                            </td>
                            <td class="px-4 sm:px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-orange-500 to-red-600 text-sm font-black text-white shadow-md shadow-orange-200/60">
                                        {{ mb_strtoupper(mb_substr((string) $studentName, 0, 1)) }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-extrabold text-slate-900">{{ $studentName }}</p>
                                        <p class="text-xs text-slate-400 sm:hidden">{{ optional($transaction->created_at)->format('d M Y H:i') }}</p>
                                        <p class="truncate text-xs text-slate-400 md:hidden">{{ $desc }}</p>
                                        <p class="hidden text-xs text-slate-400 sm:block">Saldo akhir: Rp {{ number_format($transaction->balance_after, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="hidden lg:table-cell px-6 py-3.5">
                                <span class="rounded-lg bg-slate-50 px-2.5 py-1 font-mono text-sm font-bold text-slate-700 ring-1 ring-slate-100">{{ $transaction->card->card_number ?? '-' }}</span>
                            </td>
                            <td class="hidden md:table-cell px-6 py-3.5 text-sm text-slate-600">
                                <p class="max-w-[16rem] truncate" title="{{ $desc }}">{{ $desc }}</p>
                            </td>
                            <td class="hidden xl:table-cell px-6 py-3.5 text-sm text-slate-600">{{ $transaction->performedBy->name ?? 'Sistem/Siswa' }}</td>
                            <td class="hidden xl:table-cell px-6 py-3.5 text-xs font-bold text-slate-500">{{ $transaction->reference_number ?: '-' }}</td>
                            <td class="whitespace-nowrap px-4 sm:px-6 py-3.5 text-right text-sm font-black text-rose-600">
                                &minus; Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-14 text-center">
                                <span class="material-symbols-outlined text-5xl text-slate-300">receipt_long</span>
                                <p class="mt-2 font-bold text-slate-600">{{ $hasFilter ? 'Tidak ada pembayaran yang cocok' : 'Belum ada riwayat pembayaran' }}</p>
                                <p class="text-xs text-slate-400">{{ $hasFilter ? 'Ubah nama siswa atau tanggal, lalu coba lagi.' : 'Pembayaran yang diproses pada kartu siswa akan muncul di sini.' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($transactions, 'hasPages') && $transactions->hasPages())
            <div class="border-t border-slate-100 p-4 sm:p-6">
                {{ $transactions->links() }}
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