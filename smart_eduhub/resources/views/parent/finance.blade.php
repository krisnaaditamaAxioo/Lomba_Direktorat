@extends('layouts.parent')

@section('title', 'Keuangan Siswa')
@section('page_title', 'Status & Transaksi Keuangan Siswa')

@php
    $creditTypes = ['topup', 'transfer_receive'];
    $typeLabels  = [
        'topup'            => 'Top up',
        'transfer_receive' => 'Transfer masuk',
        'transfer_send'    => 'Transfer keluar',
        'purchase'         => 'Pembelian',
        'payment'          => 'Pembayaran',
    ];
    $typeLabel = fn ($type) => $typeLabels[$type] ?? \Illuminate\Support\Str::headline($type);
    $totalBalance = $cards->sum('balance');

    // Tabel dibatasi 5 data terbaru; sisanya lewat tombol "Lihat semua" (?all=1)
    $limit    = 5;
    $showAll  = request()->boolean('all');
    $rows     = $showAll ? $transactions : $transactions->take($limit);
    $total    = method_exists($transactions, 'total') ? $transactions->total() : $transactions->count();
    $hasMore  = !$showAll && ($total > $limit);
    $urlAll   = request()->fullUrlWithQuery(['all' => 1, 'page' => null]) . '#riwayat';
    $urlLess  = request()->fullUrlWithoutQuery(['all', 'page']) . '#riwayat';
@endphp

@section('content')
<style>
    /* ===== Kartu 3D (gaya SmartBank, warna hijau) ===== */
    .perspective-container { perspective: 1000px; touch-action: pan-y; }

    .card-3d-inner {
        transform-style: preserve-3d;
        transform: rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg));
        transition: transform .35s cubic-bezier(.16, 1, .3, 1);
        will-change: transform;
        box-shadow:
            0 30px 50px -20px rgba(4, 120, 87, .6),
            0 12px 20px -12px rgba(2, 44, 34, .5);
    }
    .perspective-container.is-tilting .card-3d-inner { transition: transform .06s linear; }
    .card-3d-float { animation: cardFloat 7s ease-in-out infinite; }
    .perspective-container.is-tilting .card-3d-float { animation-play-state: paused; }

    .credit-card-gradient {
        background:
            radial-gradient(120% 90% at 0% 0%, #6ee7b7 0%, transparent 55%),
            linear-gradient(135deg, #10b981 0%, #047857 48%, #064e3b 100%);
    }
    .chip-gold { background: linear-gradient(135deg, #fde68a, #f59e0b 55%, #fcd34d); box-shadow: inset 0 0 0 1px rgba(0,0,0,.12); position: relative; }
    .chip-gold::before, .chip-gold::after { content: ""; position: absolute; background: rgba(0,0,0,.15); }
    .chip-gold::before { left: 0; right: 0; top: 50%; height: 1px; }
    .chip-gold::after  { top: 0; bottom: 0; left: 50%; width: 1px; }

    .card-shimmer {
        position: absolute; inset: 0;
        background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,.22) 45%, rgba(255,255,255,.06) 55%, transparent 70%);
        transform: translateX(-120%);
        animation: cardShimmer 5.5s ease-in-out infinite;
    }

    @keyframes cardShimmer { 0%, 55% { transform: translateX(-120%); } 100% { transform: translateX(120%); } }
    @keyframes cardFloat { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
    @media (prefers-reduced-motion: reduce) {
        .card-3d-float, .card-shimmer { animation: none; }
        .card-3d-inner, .perspective-container.is-tilting .card-3d-inner { transition: none; }
    }
</style>

<div class="mx-auto max-w-6xl space-y-6 sm:space-y-8">

    {{-- ================= Ringkasan ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-900 p-5 text-white shadow-lg shadow-emerald-900/15 sm:p-8">
        <div class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-12 left-1/4 h-32 w-32 rounded-full bg-white/5"></div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <p class="text-sm text-emerald-100/80">Keuangan siswa</p>
                <h2 class="mt-0.5 truncate text-xl font-extrabold tracking-tight sm:text-2xl">{{ $student->nama_lengkap }}</h2>
                <p class="mt-1 text-sm text-emerald-100/70">Saldo kartu dan mutasi transaksi e-card.</p>
            </div>

            <div class="flex items-center gap-3 rounded-2xl bg-white/10 px-4 py-3 ring-1 ring-inset ring-white/20 backdrop-blur sm:min-w-[14rem]">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/15">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">account_balance_wallet</span>
                </span>
                <div class="min-w-0">
                    <p class="text-xs text-emerald-100/80">{{ $cards->count() > 1 ? 'Total saldo semua kartu' : 'Total saldo' }}</p>
                    <p class="truncate text-xl font-extrabold tabular-nums">Rp {{ number_format($totalBalance, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= Kartu siswa (3D) ================= --}}
    <section aria-labelledby="h-kartu">
        <h3 id="h-kartu" class="mb-4 px-1 text-base font-bold text-slate-900">Kartu siswa</h3>

        <div class="grid gap-8 sm:gap-10 md:grid-cols-2">
            @forelse($cards as $card)
                @php
                    $active = $card->status === 'active';
                    $last4  = substr($card->card_number, -4);
                @endphp
                <div class="flex w-full flex-col items-center">
                    <div class="relative w-full max-w-[430px]">
                        {{-- Glow di belakang kartu --}}
                        <div class="pointer-events-none absolute -inset-6 rounded-[3rem] bg-gradient-to-tr from-emerald-500/25 via-teal-500/20 to-lime-400/20 blur-3xl"></div>

                        <div class="perspective-container relative w-full" data-tilt>
                            <div class="card-3d-float">
                                <div class="card-3d-inner credit-card-gradient relative aspect-[1.586/1] min-h-[13.5rem] w-full rounded-[1.25rem] p-4 text-white ring-1 ring-inset ring-white/20 min-[400px]:p-5 sm:rounded-[1.75rem] sm:p-8"
                                     aria-label="Kartu siswa {{ $student->nama_lengkap }}">

                                    {{-- Dekorasi latar (di-clip terpisah agar 3D tetap jalan) --}}
                                    <div class="pointer-events-none absolute inset-0 overflow-hidden rounded-[1.25rem] sm:rounded-[1.75rem]">
                                        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-lime-300/30 blur-3xl"></div>
                                        <div class="absolute -bottom-24 -left-16 h-64 w-64 rounded-full bg-teal-400/25 blur-3xl"></div>
                                        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(rgba(255,255,255,.7) 1px, transparent 1px); background-size: 18px 18px;"></div>
                                        <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full border border-white/10"></div>
                                        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full border border-white/10"></div>
                                        <div class="card-shimmer"></div>
                                        <div class="card-glare absolute inset-0" style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0), transparent 55%);"></div>
                                    </div>

                                    {{-- Konten berlapis --}}
                                    <div class="relative flex h-full flex-col" style="transform-style: preserve-3d;">
                                        <div class="mb-3 flex items-start justify-between sm:mb-6" style="transform: translateZ(35px);">
                                            <div>
                                                <p class="mb-1 text-[8px] font-bold uppercase tracking-[0.2em] text-white/60 sm:text-[10px]">Student Credit Card</p>
                                                <span class="text-base font-extrabold tracking-tight sm:text-lg">SMART<span class="text-yellow-300">AXIOO</span></span>
                                            </div>
                                            <span class="material-symbols-outlined rotate-90 text-xl text-white/70 sm:text-2xl">contactless</span>
                                        </div>

                                        <div class="mb-3 flex items-center gap-3 sm:mb-5 sm:gap-4" style="transform: translateZ(55px);">
                                            <div class="chip-gold h-7 w-10 rounded-lg sm:h-9 sm:w-12"></div>
                                            <div class="flex items-center gap-1.5 rounded-full border border-white/10 bg-black/20 px-2.5 py-1 sm:px-3">
                                                <span class="relative flex h-1.5 w-1.5">
                                                    @if($active)<span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-300 opacity-75"></span>@endif
                                                    <span class="relative inline-flex h-1.5 w-1.5 rounded-full {{ $active ? 'bg-emerald-300' : 'bg-rose-300' }}"></span>
                                                </span>
                                                <span class="ml-1 text-[8px] font-bold uppercase tracking-widest sm:text-[9px] {{ $active ? 'text-emerald-200' : 'text-rose-200' }}">
                                                    {{ $active ? 'Active' : \Illuminate\Support\Str::headline($card->status) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mb-3 sm:mb-5" style="transform: translateZ(75px);">
                                            <p class="font-mono text-base font-bold tracking-[0.12em] text-white drop-shadow-lg min-[400px]:text-lg sm:text-2xl sm:tracking-[0.18em]">**** **** **** {{ $last4 }}</p>
                                        </div>

                                        <div class="mt-auto flex items-end justify-between gap-2" style="transform: translateZ(45px);">
                                            <div class="min-w-0">
                                                <p class="text-[8px] uppercase tracking-[0.15em] text-white/50 sm:text-[9px]">Card Holder</p>
                                                <p class="max-w-[8rem] truncate text-xs font-bold uppercase tracking-wide sm:max-w-[10rem] sm:text-sm">{{ $student->nama_lengkap }}</p>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-[8px] uppercase tracking-[0.15em] text-white/50 sm:text-[9px]">Saldo</p>
                                                <p class="font-mono text-xs font-bold tabular-nums sm:text-sm">Rp {{ number_format($card->balance, 0, ',', '.') }}</p>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-[10px] font-black tracking-[0.2em] text-white/80 sm:text-xs sm:tracking-[0.25em]">AXIOO</span>
                                                <p class="text-[8px] uppercase tracking-widest text-white/40">e-card</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-emerald-200 bg-emerald-50/50 px-6 py-12 text-center">
                    <span class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                        <span class="material-symbols-outlined text-3xl">credit_card_off</span>
                    </span>
                    <p class="text-sm font-semibold text-slate-700">Siswa belum memiliki kartu aktif</p>
                    <p class="mt-1 text-xs text-slate-500">Kartu akan tampil di sini setelah diaktifkan oleh sekolah.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- ================= Riwayat transaksi ================= --}}
    <section id="riwayat" aria-labelledby="h-riwayat" class="scroll-mt-6">
        <div class="mb-4 flex items-center justify-between gap-3 px-1">
            <h3 id="h-riwayat" class="text-base font-bold text-slate-900">
                {{ $showAll ? 'Semua mutasi transaksi' : 'Transaksi terbaru' }}
            </h3>
            @if($hasMore)
                <a href="{{ $urlAll }}" class="inline-flex items-center gap-0.5 rounded-lg px-2 py-1 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50">
                    Lihat semua
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                </a>
            @endif
        </div>

        <div class="overflow-hidden rounded-3xl border border-emerald-100 bg-white shadow-sm">

            {{-- Tabel (tablet ke atas) --}}
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full border-collapse text-left text-sm">
                    <thead>
                        <tr class="border-b border-emerald-100 bg-emerald-50/60 text-slate-600">
                            <th scope="col" class="px-6 py-3.5 font-semibold">Waktu</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Jenis</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Keterangan</th>
                            <th scope="col" class="px-6 py-3.5 text-right font-semibold">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($rows as $t)
                            @php $in = in_array($t->type, $creditTypes); @endphp
                            <tr class="transition-colors hover:bg-emerald-50/40">
                                <td class="whitespace-nowrap px-6 py-4 tabular-nums text-slate-600">
                                    {{ $t->created_at->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold {{ $in ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        <span class="material-symbols-outlined text-[14px]">{{ $in ? 'south_west' : 'north_east' }}</span>
                                        {{ $typeLabel($t->type) }}
                                    </span>
                                </td>
                                <td class="max-w-xs px-6 py-4 text-slate-700">
                                    <span class="line-clamp-2">{{ $t->description ?: '-' }}</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right font-bold tabular-nums {{ $in ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $in ? '+' : '−' }} Rp {{ number_format($t->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-14 text-center">
                                    <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-400">
                                        <span class="material-symbols-outlined text-3xl">receipt_long</span>
                                    </span>
                                    <p class="text-sm font-semibold text-slate-600">Belum ada transaksi</p>
                                    <p class="mt-1 text-xs text-slate-400">Top up dan pembelian akan tercatat di sini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Daftar (ponsel) --}}
            <ul class="divide-y divide-slate-100 md:hidden">
                @forelse($rows as $t)
                    @php $in = in_array($t->type, $creditTypes); @endphp
                    <li class="flex items-center gap-3 px-4 py-3.5">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl {{ $in ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }}">
                            <span class="material-symbols-outlined text-[22px]">{{ $in ? 'south_west' : 'north_east' }}</span>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-800">{{ $t->description ?: $typeLabel($t->type) }}</p>
                            <p class="truncate text-xs text-slate-500">
                                {{ $typeLabel($t->type) }} • {{ $t->created_at->translatedFormat('d M Y, H:i') }}
                            </p>
                        </div>
                        <p class="shrink-0 text-sm font-bold tabular-nums {{ $in ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $in ? '+' : '−' }}Rp {{ number_format($t->amount, 0, ',', '.') }}
                        </p>
                    </li>
                @empty
                    <li class="px-6 py-12 text-center">
                        <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-400">
                            <span class="material-symbols-outlined text-3xl">receipt_long</span>
                        </span>
                        <p class="text-sm font-semibold text-slate-600">Belum ada transaksi</p>
                        <p class="mt-1 text-xs text-slate-400">Top up dan pembelian akan tercatat di sini.</p>
                    </li>
                @endforelse
            </ul>

            @if($showAll)
                <div class="flex flex-col gap-3 border-t border-emerald-100 bg-emerald-50/30 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <a href="{{ $urlLess }}" class="inline-flex items-center gap-1 self-start rounded-lg px-2 py-1 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100">
                        <span class="material-symbols-outlined text-base">chevron_left</span>
                        Tampilkan 5 terbaru
                    </a>
                    @if($transactions->hasPages())
                        <div class="overflow-x-auto">{{ $transactions->appends(['all' => 1])->links() }}</div>
                    @endif
                </div>
            @elseif($hasMore)
                <a href="{{ $urlAll }}" class="flex items-center justify-center gap-1.5 border-t border-emerald-100 bg-emerald-50/40 px-4 py-3.5 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100/60">
                    Lihat semua {{ $total }} transaksi
                    <span class="material-symbols-outlined text-base">arrow_forward</span>
                </a>
            @endif
        </div>
    </section>
</div>

<script>
(function () {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    document.querySelectorAll('[data-tilt]').forEach(function (scene) {
        var card  = scene.querySelector('.card-3d-inner');
        var glare = scene.querySelector('.card-glare');
        var MAX_Y = 18, MAX_X = 14;

        function move(e) {
            var r  = scene.getBoundingClientRect();
            var px = Math.min(Math.max((e.clientX - r.left) / r.width, 0), 1);
            var py = Math.min(Math.max((e.clientY - r.top) / r.height, 0), 1);
            card.style.setProperty('--ry', ((px - 0.5) * 2 * MAX_Y).toFixed(2) + 'deg');
            card.style.setProperty('--rx', ((0.5 - py) * 2 * MAX_X).toFixed(2) + 'deg');
            glare.style.background =
                'radial-gradient(circle at ' + (px * 100).toFixed(1) + '% ' + (py * 100).toFixed(1) +
                '%, rgba(255,255,255,.35), transparent 55%)';
        }
        function reset() {
            scene.classList.remove('is-tilting');
            card.style.setProperty('--rx', '0deg');
            card.style.setProperty('--ry', '0deg');
            glare.style.background = 'radial-gradient(circle at 50% 50%, rgba(255,255,255,0), transparent 55%)';
        }

        scene.addEventListener('pointerenter', function () { scene.classList.add('is-tilting'); });
        scene.addEventListener('pointermove', move);
        scene.addEventListener('pointerleave', reset);
        scene.addEventListener('pointercancel', reset);
        scene.addEventListener('pointerup', function (e) { if (e.pointerType !== 'mouse') reset(); });
    });
})();
</script>
@endsection