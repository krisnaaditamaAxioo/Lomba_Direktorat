@extends('layouts.app')

@section('title', 'Rekap Laporan Keuangan')

@section('content')
<style>
    /* Mobile: sembunyikan navbar putih bawaan layout & rapatkan header ke tepi atas */
    @media (max-width: 767px) {
        body > header, #topbar, #navbar, .topbar, [data-topbar] { display: none !important; }
        main, .main-content, [data-main] { padding-top: 0 !important; }
    }
</style>
@php
    $exportParams = ['from' => $from, 'to' => $to, 'student_id' => $studentId];

    // Satu sumber data untuk ringkasan dan baris total tabel
    $stats = [
        ['label' => 'Top up',          'icon' => 'add_card',     'text' => 'text-emerald-600', 'box' => 'bg-emerald-50 text-emerald-600', 'amount' => $grandTotal['topup'] ?? 0,            'count' => optional($summary->get('topup'))->count ?? 0],
        ['label' => 'Pembayaran',      'icon' => 'shopping_bag', 'text' => 'text-rose-600',    'box' => 'bg-rose-50 text-rose-600',       'amount' => $grandTotal['payment'] ?? 0,          'count' => optional($summary->get('payment'))->count ?? 0],
        ['label' => 'Transfer keluar', 'icon' => 'north_east',   'text' => 'text-orange-600',  'box' => 'bg-orange-50 text-orange-600',   'amount' => $grandTotal['transfer'] ?? 0,         'count' => optional($summary->get('transfer'))->count ?? 0],
        ['label' => 'Transfer masuk',  'icon' => 'south_west',   'text' => 'text-cyan-600',    'box' => 'bg-cyan-50 text-cyan-600',       'amount' => $grandTotal['transfer_receive'] ?? 0, 'count' => optional($summary->get('transfer_receive'))->count ?? 0],
        ['label' => 'Tarik tunai',     'icon' => 'payments',     'text' => 'text-amber-600',   'box' => 'bg-amber-50 text-amber-600',     'amount' => $grandTotal['withdraw'] ?? 0,         'count' => optional($summary->get('withdraw'))->count ?? 0],
    ];

    // Preset periode
    $today = now()->toDateString();
    $presets = [
        'Hari ini'        => [$today, $today],
        '7 hari terakhir' => [now()->subDays(6)->toDateString(), $today],
        'Bulan ini'       => [now()->startOfMonth()->toDateString(), $today],
    ];

    $periode = ($from || $to)
        ? 'Periode ' . ($from ? \Carbon\Carbon::parse($from)->format('d M Y') : 'awal') . ' sampai ' . ($to ? \Carbon\Carbon::parse($to)->format('d M Y') : 'sekarang') . '.'
        : 'Ringkasan transaksi siswa pada periode yang dipilih.';

    $totalTrx = $stats[0]['count'] + $stats[1]['count'] + $stats[2]['count'] + $stats[3]['count'] + $stats[4]['count'];

    $inputClass = 'w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 placeholder:text-slate-400 focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-500/10';
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
                <span class="text-sm font-black">{{ number_format($totalTrx, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="relative mt-4 text-center md:text-left md:-mt-6">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-orange-100">Smart EduHub Wallet</p>
            <h2 class="mt-1 text-3xl sm:text-4xl font-black tracking-tight">Rekap Laporan Keuangan</h2>
            <p class="mt-1.5 text-xs sm:text-sm text-orange-50/90">{{ $periode }}</p>
        </div>

        <div class="relative mt-5 grid grid-cols-2 gap-2.5 sm:flex">
            <a href="{{ route('admin.finance.rekap.export.excel', $exportParams) }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-extrabold text-orange-600 shadow-lg hover:bg-orange-50 active:scale-[.98] transition">
                <span class="material-symbols-outlined text-[20px]">table_view</span>
                Excel
            </a>
            <a href="{{ route('admin.finance.rekap.export.pdf', $exportParams) }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white/15 px-5 py-3 text-sm font-bold text-white ring-1 ring-white/30 backdrop-blur hover:bg-white/25 active:scale-[.98] transition">
                <span class="material-symbols-outlined text-[20px]">picture_as_pdf</span>
                PDF
            </a>
        </div>
    </header>

    {{-- ================= FILTER ================= --}}
    <section class="rounded-3xl border border-slate-100 bg-white p-4 sm:p-6 shadow-sm space-y-4">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-orange-600">filter_alt</span>
            <h3 class="text-base font-extrabold text-slate-900">Filter Laporan</h3>
        </div>

        <form method="GET" id="filter-form" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-end">
            <div class="sm:col-span-2 lg:col-span-1">
                <label for="student_id" class="mb-1.5 block text-xs font-bold text-slate-500">Siswa</label>
                <select id="student_id" name="student_id" class="{{ $inputClass }}">
                    <option value="">Semua siswa</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ $studentId == $student->id ? 'selected' : '' }}>
                            {{ $student->nama_lengkap }} ({{ $student->nis_siswa ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="from" class="mb-1.5 block text-xs font-bold text-slate-500">Dari tanggal</label>
                <input id="from" type="date" name="from" value="{{ $from }}" class="{{ $inputClass }}">
            </div>
            <div>
                <label for="to" class="mb-1.5 block text-xs font-bold text-slate-500">Sampai tanggal</label>
                <input id="to" type="date" name="to" value="{{ $to }}" class="{{ $inputClass }}">
            </div>
            <button type="submit" class="sm:col-span-2 lg:col-span-1 inline-flex items-center justify-center gap-2 rounded-2xl bg-orange-600 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-orange-200 transition hover:bg-orange-700 active:scale-[.98]">
                <span class="material-symbols-outlined text-[20px]">filter_alt</span>
                Terapkan filter
            </button>
        </form>

        {{-- Pilih cepat: geser di mobile --}}
        <div class="-mx-4 flex items-center gap-2 overflow-x-auto border-t border-slate-100 px-4 pt-4 sm:mx-0 sm:flex-wrap sm:px-0">
            <span class="shrink-0 text-xs font-bold text-slate-400">Pilih cepat</span>
            @foreach($presets as $label => [$pFrom, $pTo])
                @php $active = $from === $pFrom && $to === $pTo; @endphp
                <a href="{{ url()->current() }}?{{ http_build_query(['from' => $pFrom, 'to' => $pTo, 'student_id' => $studentId]) }}"
                   class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-xs font-bold transition {{ $active ? 'bg-orange-600 text-white shadow-md shadow-orange-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $label }}
                </a>
            @endforeach
            @if($from || $to || $studentId)
                <a href="{{ url()->current() }}" class="inline-flex shrink-0 items-center gap-1 whitespace-nowrap rounded-full bg-rose-50 px-3.5 py-2 text-xs font-bold text-rose-600 transition hover:bg-rose-100 sm:ml-auto">
                    <span class="material-symbols-outlined text-sm">close</span>
                    Reset
                </a>
            @endif
        </div>
    </section>

    {{-- ================= RINGKASAN ================= --}}
    <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4 xl:grid-cols-5">
        @foreach($stats as $stat)
            <div class="flex min-w-0 items-center gap-3 rounded-3xl border border-slate-100 bg-white p-4 sm:p-5 shadow-sm {{ $loop->last ? 'sm:col-span-2 xl:col-span-1' : '' }}">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl {{ $stat['box'] }}">
                    <span class="material-symbols-outlined">{{ $stat['icon'] }}</span>
                </span>
                <div class="min-w-0">
                    <p class="truncate text-[11px] font-bold text-slate-400">{{ $stat['label'] }}</p>
                    <p class="break-words text-lg sm:text-xl font-black leading-tight {{ $stat['text'] }}">Rp {{ number_format($stat['amount'], 0, ',', '.') }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">{{ number_format($stat['count'], 0, ',', '.') }} transaksi</p>
                </div>
            </div>
        @endforeach
    </section>

    <div class="grid grid-cols-1 gap-5 lg:gap-6 xl:grid-cols-3">

        {{-- ================= REKAP HARIAN ================= --}}
        <section class="min-w-0 overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm xl:col-span-2">
            <div class="flex items-center gap-2 px-4 pt-5 pb-1 sm:px-6">
                <span class="material-symbols-outlined text-orange-600">calendar_month</span>
                <h3 class="text-base font-extrabold text-slate-900">Rekap harian</h3>
            </div>
            <p class="px-4 pb-4 text-xs sm:px-6 sm:text-sm text-slate-500">Total nominal per jenis transaksi untuk setiap tanggal.</p>

            {{-- Tabel: tablet ke atas --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full min-w-[720px] text-left">
                    <thead>
                        <tr class="border-y border-slate-100 bg-slate-50/80 text-xs font-bold text-slate-400">
                            <th scope="col" class="sticky left-0 bg-slate-50 px-6 py-3.5">Tanggal</th>
                            <th scope="col" class="px-4 py-3.5 text-right">Top up</th>
                            <th scope="col" class="px-4 py-3.5 text-right">Pembayaran</th>
                            <th scope="col" class="px-4 py-3.5 text-right">Trf keluar</th>
                            <th scope="col" class="px-4 py-3.5 text-right">Trf masuk</th>
                            <th scope="col" class="px-4 py-3.5 text-right">Tarik tunai</th>
                            <th scope="col" class="px-6 py-3.5 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($dailySummary as $item)
                            <tr class="group transition-colors hover:bg-orange-50/40">
                                <td class="sticky left-0 whitespace-nowrap bg-white px-6 py-3.5 text-sm font-extrabold text-slate-900 group-hover:bg-orange-50">{{ \Carbon\Carbon::parse($item->transaction_date)->format('d M Y') }}</td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-right text-sm font-semibold tabular-nums {{ ($item->total_topup ?? 0) > 0 ? 'text-emerald-600' : 'text-slate-300' }}">Rp {{ number_format($item->total_topup ?? 0, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-right text-sm font-semibold tabular-nums {{ ($item->total_payment ?? 0) > 0 ? 'text-rose-600' : 'text-slate-300' }}">Rp {{ number_format($item->total_payment ?? 0, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-right text-sm font-semibold tabular-nums {{ ($item->total_transfer ?? 0) > 0 ? 'text-orange-600' : 'text-slate-300' }}">Rp {{ number_format($item->total_transfer ?? 0, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-right text-sm font-semibold tabular-nums {{ ($item->total_transfer_receive ?? 0) > 0 ? 'text-cyan-600' : 'text-slate-300' }}">Rp {{ number_format($item->total_transfer_receive ?? 0, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-right text-sm font-semibold tabular-nums {{ ($item->total_withdraw ?? 0) > 0 ? 'text-amber-600' : 'text-slate-300' }}">Rp {{ number_format($item->total_withdraw ?? 0, 0, ',', '.') }}</td>
                                <td class="px-6 py-3.5 text-right text-sm font-black tabular-nums text-slate-900">{{ $item->total_transactions }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-14 text-center">
                                    <span class="material-symbols-outlined text-5xl text-slate-300">event_busy</span>
                                    <p class="mt-2 font-bold text-slate-600">Belum ada transaksi pada periode ini</p>
                                    <p class="text-xs text-slate-400">Ubah tanggal atau pilih siswa lain untuk melihat data.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($dailySummary->count() > 0)
                        <tfoot>
                            <tr class="border-t border-slate-200 bg-slate-50 text-sm font-black">
                                <td class="sticky left-0 bg-slate-50 px-6 py-4 text-slate-900">Total</td>
                                <td class="whitespace-nowrap px-4 py-4 text-right tabular-nums text-emerald-600">Rp {{ number_format($stats[0]['amount'], 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-right tabular-nums text-rose-600">Rp {{ number_format($stats[1]['amount'], 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-right tabular-nums text-orange-600">Rp {{ number_format($stats[2]['amount'], 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-right tabular-nums text-cyan-600">Rp {{ number_format($stats[3]['amount'], 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-right tabular-nums text-amber-600">Rp {{ number_format($stats[4]['amount'], 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right tabular-nums text-slate-900">{{ $dailySummary->sum('total_transactions') }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            {{-- Kartu: ponsel --}}
            <div class="md:hidden space-y-3 border-t border-slate-100 p-3">
                @forelse($dailySummary as $item)
                    <article class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-black text-slate-900">{{ \Carbon\Carbon::parse($item->transaction_date)->format('d M Y') }}</p>
                            <span class="rounded-full bg-orange-50 px-2.5 py-1 text-xs font-black text-orange-600">{{ $item->total_transactions }} transaksi</span>
                        </div>
                        <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-3 rounded-2xl bg-slate-50 p-3 text-sm">
                            <div class="min-w-0">
                                <dt class="text-xs font-bold text-slate-400">Top up</dt>
                                <dd class="break-words font-bold tabular-nums text-emerald-600">Rp {{ number_format($item->total_topup ?? 0, 0, ',', '.') }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="text-xs font-bold text-slate-400">Pembayaran</dt>
                                <dd class="break-words font-bold tabular-nums text-rose-600">Rp {{ number_format($item->total_payment ?? 0, 0, ',', '.') }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="text-xs font-bold text-slate-400">Transfer keluar</dt>
                                <dd class="break-words font-bold tabular-nums text-orange-600">Rp {{ number_format($item->total_transfer ?? 0, 0, ',', '.') }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="text-xs font-bold text-slate-400">Transfer masuk</dt>
                                <dd class="break-words font-bold tabular-nums text-cyan-600">Rp {{ number_format($item->total_transfer_receive ?? 0, 0, ',', '.') }}</dd>
                            </div>
                            <div class="col-span-2 min-w-0">
                                <dt class="text-xs font-bold text-slate-400">Tarik tunai</dt>
                                <dd class="break-words font-bold tabular-nums text-amber-600">Rp {{ number_format($item->total_withdraw ?? 0, 0, ',', '.') }}</dd>
                            </div>
                        </dl>
                    </article>
                @empty
                    <div class="px-5 py-12 text-center">
                        <span class="material-symbols-outlined text-5xl text-slate-300">event_busy</span>
                        <p class="mt-2 font-bold text-slate-600">Belum ada transaksi pada periode ini</p>
                        <p class="text-xs text-slate-400">Ubah tanggal atau pilih siswa lain untuk melihat data.</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- ================= PEMBAYARAN TERAKHIR ================= --}}
        <section class="flex min-w-0 flex-col overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm">
            <div class="flex items-center gap-2 px-4 pt-5 pb-1 sm:px-6">
                <span class="material-symbols-outlined text-orange-600">receipt_long</span>
                <h3 class="text-base font-extrabold text-slate-900">Pembayaran terakhir</h3>
            </div>
            <p class="px-4 pb-4 text-xs sm:px-6 sm:text-sm text-slate-500">Pembayaran terbaru pada periode ini.</p>

            <div class="flex-1 space-y-3 border-t border-slate-100 p-3 sm:p-4 xl:max-h-[40rem] xl:overflow-y-auto">
                @forelse($paymentTransactions as $transaction)
                    @php $sName = $transaction->card->student->nama_lengkap ?? '-'; @endphp
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-3.5">
                        <div class="flex items-start gap-3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-orange-500 to-red-600 text-sm font-black text-white shadow-md shadow-orange-200/60">
                                {{ mb_strtoupper(mb_substr((string) $sName, 0, 1)) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <p class="break-words text-sm font-extrabold text-slate-900">{{ $sName }}</p>
                                    <p class="shrink-0 text-sm font-black text-rose-600">&minus; Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                                </div>
                                <p class="mt-0.5 text-xs text-slate-500">{{ optional($transaction->created_at)->format('d M Y H:i') }}</p>
                                <p class="mt-2 break-words text-sm text-slate-600">{{ $transaction->description ?: '-' }}</p>
                                <div class="mt-1.5 flex flex-wrap items-center justify-between gap-x-3 gap-y-0.5 text-xs text-slate-400">
                                    <span>Petugas: {{ $transaction->performedBy->name ?? 'Sistem/Siswa' }}</span>
                                    <span class="break-all text-[11px]">{{ $transaction->reference_number ?: '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center">
                        <span class="material-symbols-outlined text-5xl text-slate-300">receipt_long</span>
                        <p class="mt-2 font-bold text-slate-600">Belum ada pembayaran</p>
                        <p class="text-xs text-slate-400">Belum ada pembayaran pada periode ini.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
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