@extends('layouts.app')

@section('content')
@php
    // Konfigurasi tampilan jenis transaksi (satu tempat, dipakai tabel & kartu mobile)
    $typeMap = [
        'topup'            => ['label' => 'Topup',          'badge' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-400/20', 'icon' => 'arrow_downward'],
        'payment'          => ['label' => 'Pembayaran',     'badge' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-400/20',             'icon' => 'shopping_bag'],
        'withdraw'         => ['label' => 'Tarik Tunai',    'badge' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-400/20',     'icon' => 'payments'],
        'transfer_receive' => ['label' => 'Transfer Masuk', 'badge' => 'bg-cyan-50 text-cyan-700 ring-cyan-600/20 dark:bg-cyan-500/10 dark:text-cyan-400 dark:ring-cyan-400/20',             'icon' => 'call_received'],
        'transfer'         => ['label' => 'Transfer Keluar','badge' => 'bg-orange-50 text-orange-700 ring-orange-600/20 dark:bg-orange-500/10 dark:text-orange-400 dark:ring-orange-400/20', 'icon' => 'call_made'],
    ];
    $fallbackType = ['label' => 'Pembayaran', 'badge' => $typeMap['payment']['badge'], 'icon' => 'shopping_bag'];

    // Skala batang pada rekap harian
    $maxDaily = collect($dailyRecap)->map(fn ($d) => max($d->total_topup, $d->total_payment))->max() ?: 1;

    $card = 'bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/70 dark:border-slate-800 shadow-sm';
    $input = 'w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm font-medium text-slate-800 dark:text-slate-100 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 focus:outline-none transition';
@endphp

<div class="space-y-6 lg:space-y-8">

    {{-- Header hero: full-bleed & menempel ke ujung atas di mobile --}}
    <div id="pageHero" class="relative overflow-hidden -mx-4 lg:mx-0 rounded-b-[2.5rem] lg:rounded-[2.5rem] bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] pt-8 pb-12 px-6 lg:py-8 text-white shadow-2xl shadow-orange-950/10">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/5"></div>
        <div class="pointer-events-none absolute -left-12 top-24 h-40 w-40 rounded-full bg-white/5"></div>
        <div class="pointer-events-none absolute -bottom-8 right-24 h-28 w-28 rounded-full bg-white/5"></div>

        <div class="relative z-10 space-y-4 text-center">
            <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-white/15 backdrop-blur-sm">
                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">account_balance_wallet</span>
            </div>
            <div class="space-y-1">
                <p class="text-[10px] font-bold uppercase tracking-widest text-orange-200/80">Smart EduHub Wallet</p>
                <h1 class="text-2xl lg:text-3xl font-black tracking-tight">Dashboard Keuangan</h1>
                <p class="mx-auto max-w-xl text-xs font-medium text-orange-100/70 sm:text-sm">Ringkasan transaksi top up, pembayaran, dan aktivitas kartu siswa.</p>
            </div>
        </div>
    </div>

    {{-- Filter + aksi --}}
    <section class="{{ $card }} p-5 sm:p-6">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end xl:justify-end">
                <form method="GET" class="grid grid-cols-2 sm:flex sm:items-end gap-3">
                    <label class="block">
                        <span class="mb-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">Dari tanggal</span>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="{{ $input }}">
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-xs font-semibold text-slate-500 dark:text-slate-400">Sampai tanggal</span>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="{{ $input }}">
                    </label>
                    <button type="submit"
                        class="col-span-2 sm:col-span-1 inline-flex items-center justify-center gap-2 rounded-xl bg-orange-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-orange-600/20 transition hover:bg-orange-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2 active:scale-[.98]">
                        <span class="material-symbols-outlined text-lg">filter_alt</span>
                        Terapkan
                    </button>
                </form>

                @if(auth()->user()->canTopupFinance())
                <a href="{{ route('admin.finance.transaction-search') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-emerald-600/20 transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 active:scale-[.98] whitespace-nowrap">
                    <span class="material-symbols-outlined text-lg">add_card</span>
                    Topup / Pembayaran
                </a>
                @endif
            </div>
        </div>

        {{-- Pintasan periode --}}
        <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 dark:border-slate-800 pt-4">
            <span class="text-xs font-semibold text-slate-400 mr-1">Periode cepat:</span>
            @foreach([
                'Hari ini'  => [now()->toDateString(), now()->toDateString()],
                '7 hari'    => [now()->subDays(6)->toDateString(), now()->toDateString()],
                'Bulan ini' => [now()->startOfMonth()->toDateString(), now()->toDateString()],
            ] as $label => [$from, $to])
                @php $active = $startDate == $from && $endDate == $to; @endphp
                <a href="{{ request()->url() }}?start_date={{ $from }}&end_date={{ $to }}"
                   class="rounded-full px-3.5 py-1.5 text-xs font-bold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500
                          {{ $active ? 'bg-orange-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </section>

    {{-- Kartu statistik --}}
    <section class="grid grid-cols-2 lg:grid-cols-6 gap-3 sm:gap-4 lg:gap-5">
        @php
            $stats = [
                ['label' => 'Total Top Up',     'value' => 'Rp ' . number_format($totalTopup, 0, ',', '.'),    'icon' => 'trending_up',   'tone' => 'emerald', 'span' => 'col-span-2 lg:col-span-2'],
                ['label' => 'Total Pembayaran', 'value' => 'Rp ' . number_format($totalPayment, 0, ',', '.'),  'icon' => 'shopping_cart', 'tone' => 'rose',    'span' => 'col-span-2 lg:col-span-2'],
                ['label' => 'Total Tarik Tunai','value' => 'Rp ' . number_format($totalWithdraw, 0, ',', '.'), 'icon' => 'payments',      'tone' => 'amber',   'span' => 'col-span-2 lg:col-span-2'],
            ];
            $tones = [
                'emerald' => ['bg' => 'bg-emerald-50 dark:bg-emerald-500/10', 'text' => 'text-emerald-600 dark:text-emerald-400'],
                'rose'    => ['bg' => 'bg-rose-50 dark:bg-rose-500/10',       'text' => 'text-rose-600 dark:text-rose-400'],
                'amber'   => ['bg' => 'bg-amber-50 dark:bg-amber-500/10',     'text' => 'text-amber-600 dark:text-amber-400'],
            ];
        @endphp

        @foreach($stats as $s)
        <div class="{{ $card }} {{ $s['span'] }} p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ $s['label'] }}</p>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $tones[$s['tone']]['bg'] }} {{ $tones[$s['tone']]['text'] }}">
                    <span class="material-symbols-outlined text-xl">{{ $s['icon'] }}</span>
                </span>
            </div>
            <h3 class="mt-3 truncate text-2xl sm:text-3xl font-black tracking-tight {{ $tones[$s['tone']]['text'] }}" title="{{ $s['value'] }}">{{ $s['value'] }}</h3>
        </div>
        @endforeach

        <div class="{{ $card }} col-span-1 lg:col-span-3 p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Transaksi</p>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-xl">receipt_long</span>
                </span>
            </div>
            <h3 class="mt-3 text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white">{{ number_format($transactionCount) }}</h3>
        </div>

        <div class="{{ $card }} col-span-1 lg:col-span-3 p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Kartu aktif</p>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400">
                    <span class="material-symbols-outlined text-xl">credit_card</span>
                </span>
            </div>
            <h3 class="mt-3 text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white">{{ $activeCards }}</h3>
            <p class="mt-1.5 inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400">
                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                {{ $pendingRequests }} pengajuan pending
            </p>
        </div>
    </section>

    {{-- Konten utama --}}
    <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Transaksi terbaru --}}
        <div id="txCard" class="{{ $card }} xl:col-span-2 overflow-hidden">
            <div class="flex items-center justify-between gap-3 p-5 sm:p-6 pb-4">
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Transaksi Terbaru</h3>
                <a href="{{ route('admin.finance.rekap') }}"
                   class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-sm font-bold text-orange-600 transition hover:bg-orange-50 dark:hover:bg-orange-500/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                    Lihat rekap
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                </a>
            </div>

            {{-- Filter jumlah: default 5 terbaru, tombol "Semua" menampilkan seluruh riwayat --}}
            @php $txTotal = $recentTransactions->count(); @endphp
            @if($txTotal > 5)
            <div class="flex flex-wrap items-center gap-3 px-5 sm:px-6 pb-4">
                <div class="inline-flex rounded-xl bg-slate-100 dark:bg-slate-800 p-1" role="group" aria-label="Filter jumlah transaksi">
                    <button type="button" data-tx-filter="limited" aria-pressed="true"
                        class="rounded-lg px-3.5 py-1.5 text-xs font-bold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                        5 Terbaru
                    </button>
                    <button type="button" data-tx-filter="all" aria-pressed="false"
                        class="rounded-lg px-3.5 py-1.5 text-xs font-bold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                        Semua ({{ $txTotal }})
                    </button>
                </div>
                <p id="txInfo" class="text-xs text-slate-500 dark:text-slate-400" aria-live="polite">Menampilkan 5 dari {{ $txTotal }} transaksi</p>
            </div>
            @endif

            {{-- Tabel (tablet ke atas) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 dark:text-slate-400">Waktu</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 dark:text-slate-400">Siswa</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 dark:text-slate-400">Jenis</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 dark:text-slate-400">Petugas</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($recentTransactions as $transaction)
                            @php $t = $typeMap[$transaction->type] ?? $fallbackType; @endphp
                            <tr @if($loop->iteration > 5) data-tx-extra hidden @endif class="transition hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500 dark:text-slate-400">{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-slate-900 dark:text-white">{{ $transaction->card->student->nama_lengkap ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ $t['badge'] }}">
                                        <span class="material-symbols-outlined text-sm">{{ $t['icon'] }}</span>
                                        {{ $t['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">{{ $transaction->performedBy->name ?? 'Sistem/Siswa' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-black tabular-nums text-slate-900 dark:text-white">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-14 text-center">
                                    <span class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-600">receipt_long</span>
                                    <p class="mt-2 text-sm text-slate-400">Belum ada transaksi pada periode ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Daftar kartu (mobile) --}}
            <ul class="md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($recentTransactions as $transaction)
                    @php $t = $typeMap[$transaction->type] ?? $fallbackType; @endphp
                    <li @if($loop->iteration > 5) data-tx-extra hidden @endif class="px-5 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold text-slate-900 dark:text-white">{{ $transaction->card->student->nama_lengkap ?? '-' }}</p>
                                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <p class="shrink-0 text-sm font-black tabular-nums text-slate-900 dark:text-white">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="mt-2.5 flex items-center justify-between gap-3">
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ $t['badge'] }}">
                                <span class="material-symbols-outlined text-sm">{{ $t['icon'] }}</span>
                                {{ $t['label'] }}
                            </span>
                            <span class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $transaction->performedBy->name ?? 'Sistem/Siswa' }}</span>
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-12 text-center">
                        <span class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-600">receipt_long</span>
                        <p class="mt-2 text-sm text-slate-400">Belum ada transaksi pada periode ini.</p>
                    </li>
                @endforelse
            </ul>
        </div>

        {{-- Rekap harian --}}
        <div class="{{ $card }} p-5 sm:p-6">
            <div class="flex items-center justify-between gap-3 mb-5">
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Rekap Harian</h3>
                <div class="flex items-center gap-3 text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>Top up</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>Bayar</span>
                </div>
            </div>

            <div class="space-y-3 sm:max-h-[34rem] xl:max-h-none overflow-y-auto pr-1">
                @forelse($dailyRecap as $day)
                    @php
                        $topPct = min(100, round($day->total_topup / $maxDaily * 100));
                        $payPct = min(100, round($day->total_payment / $maxDaily * 100));
                    @endphp
                    <div class="rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 p-4">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-black text-slate-900 dark:text-white">{{ \Carbon\Carbon::parse($day->transaction_date)->format('d M Y') }}</p>
                            <span class="rounded-full bg-white dark:bg-slate-900 px-2.5 py-0.5 text-xs font-bold text-slate-600 dark:text-slate-300 ring-1 ring-inset ring-slate-200 dark:ring-slate-700">
                                {{ $day->total_transactions }} transaksi
                            </span>
                        </div>

                        <div class="mt-3 space-y-2.5">
                            <div>
                                <div class="flex items-baseline justify-between text-sm">
                                    <span class="text-slate-500 dark:text-slate-400">Top up</span>
                                    <span class="font-bold tabular-nums text-emerald-600 dark:text-emerald-400">Rp {{ number_format($day->total_topup, 0, ',', '.') }}</span>
                                </div>
                                <div class="mt-1 h-1.5 rounded-full bg-slate-200/70 dark:bg-slate-700">
                                    <div class="h-full rounded-full bg-emerald-500" style="width: {{ $topPct }}%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-baseline justify-between text-sm">
                                    <span class="text-slate-500 dark:text-slate-400">Pembayaran</span>
                                    <span class="font-bold tabular-nums text-rose-600 dark:text-rose-400">Rp {{ number_format($day->total_payment, 0, ',', '.') }}</span>
                                </div>
                                <div class="mt-1 h-1.5 rounded-full bg-slate-200/70 dark:bg-slate-700">
                                    <div class="h-full rounded-full bg-rose-500" style="width: {{ $payPct }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center">
                        <span class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-600">calendar_month</span>
                        <p class="mt-2 text-sm text-slate-400">Belum ada data rekap harian.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>

<script>
(function () {
    var card = document.getElementById('txCard');
    if (!card) return;

    var buttons = card.querySelectorAll('[data-tx-filter]');
    var extras  = card.querySelectorAll('[data-tx-extra]');
    var info    = document.getElementById('txInfo');
    var total   = {{ $recentTransactions->count() }};

    var ACTIVE   = ['bg-white', 'dark:bg-slate-700', 'text-slate-900', 'dark:text-white', 'shadow-sm'];
    var INACTIVE = ['text-slate-500', 'dark:text-slate-400', 'hover:text-slate-700', 'dark:hover:text-slate-200'];

    function apply(mode) {
        var showAll = mode === 'all';
        extras.forEach(function (el) { el.hidden = !showAll; });
        if (info) info.textContent = showAll
            ? 'Menampilkan semua ' + total + ' transaksi'
            : 'Menampilkan 5 dari ' + total + ' transaksi';

        buttons.forEach(function (btn) {
            var on = btn.dataset.txFilter === mode;
            btn.setAttribute('aria-pressed', on);
            ACTIVE.forEach(function (c) { btn.classList.toggle(c, on); });
            INACTIVE.forEach(function (c) { btn.classList.toggle(c, !on); });
        });
    }

    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () { apply(btn.dataset.txFilter); });
    });

    apply('limited');
})();
</script>

<script>
// Mobile: sembunyikan navbar atas (Search) & tempelkan header ke ujung atas layar
(function () {
    var hero = document.getElementById('pageHero');
    if (!hero) return;
    var mq = window.matchMedia('(max-width: 767px)');

    var searchBox = document.querySelector('input[type="search"], input[placeholder*="earch" i]');
    var topbar = null;
    if (searchBox) {
        var el = searchBox;
        while (el && el !== document.body) {
            if (el.contains(hero)) break;
            var pos = getComputedStyle(el).position;
            if (el.tagName === 'HEADER' || el.tagName === 'NAV' || pos === 'sticky' || pos === 'fixed') topbar = el;
            el = el.parentElement;
        }
    }

    function snapHero() {
        if (topbar) topbar.style.display = mq.matches ? 'none' : '';
        hero.style.marginTop = hero.style.marginLeft = hero.style.marginRight = '';
        if (!mq.matches) return;
        var r = hero.getBoundingClientRect();
        var cs = getComputedStyle(hero);
        var mt = parseFloat(cs.marginTop) || 0;
        var ml = parseFloat(cs.marginLeft) || 0;
        var mr = parseFloat(cs.marginRight) || 0;
        var top = r.top + window.scrollY;
        var right = document.documentElement.clientWidth - r.right;
        if (top > 0) hero.style.marginTop = (mt - top) + 'px';
        if (r.left !== 0) hero.style.marginLeft = (ml - r.left) + 'px';
        if (right !== 0) hero.style.marginRight = (mr - right) + 'px';
    }
    var run = function () { requestAnimationFrame(snapHero); };
    run();
    window.addEventListener('load', run);
    window.addEventListener('resize', run);
    if (mq.addEventListener) mq.addEventListener('change', run);
})();
</script>
@endsection