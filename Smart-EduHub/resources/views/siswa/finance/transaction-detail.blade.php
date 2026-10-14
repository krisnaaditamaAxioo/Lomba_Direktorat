@extends('layouts.siswa')

@section('title', 'Detail Transaksi')
@section('page_title', 'Detail Transaksi')

@section('content')
@php
    $isCredit = in_array($transaction->type, ['topup', 'transfer_receive']);
    switch ($transaction->type) {
        case 'topup':            $txLabel = 'Top Up Saldo'; $txFlowLabel = 'Dana Masuk'; $badgeColor = 'emerald'; $txIcon = 'add_card'; break;
        case 'payment':          $txLabel = 'Pembayaran / Belanja'; $txFlowLabel = 'Dana Keluar'; $badgeColor = 'rose'; $txIcon = 'shopping_cart'; break;
        case 'transfer':         $txLabel = 'Transfer Keluar'; $txFlowLabel = 'Dana Keluar'; $badgeColor = 'orange'; $txIcon = 'send'; break;
        case 'transfer_receive': $txLabel = 'Transfer Masuk'; $txFlowLabel = 'Dana Masuk'; $badgeColor = 'cyan'; $txIcon = 'call_received'; break;
        case 'withdraw':         $txLabel = 'Tarik Tunai'; $txFlowLabel = 'Dana Keluar'; $badgeColor = 'amber'; $txIcon = 'point_of_sale'; break;
        default:                 $txLabel = ucfirst($transaction->type); $txFlowLabel = $isCredit ? 'Dana Masuk' : 'Dana Keluar'; $badgeColor = 'slate'; $txIcon = 'receipt';
    }
    $dotColor = match($badgeColor) {
        'emerald' => 'bg-emerald-500',
        'rose'    => 'bg-rose-500',
        'orange'  => 'bg-orange-500',
        'cyan'    => 'bg-cyan-500',
        'amber'   => 'bg-amber-500',
        default   => 'bg-slate-500',
    };
    $amountColor = $isCredit ? 'text-emerald-600' : ($transaction->type === 'withdraw' ? 'text-amber-600' : 'text-slate-800');
    $student = $transaction->card->student ?? null;
@endphp

{{--
    CATATAN LAYOUT:
    Header memakai teknik full-bleed (w-screen + margin negatif) agar menempel ke atas,
    kiri, dan kanan layar. Sesuaikan -mt-* dengan padding atas <main> di layouts.siswa.
--}}
<div class="relative left-1/2 right-1/2 -mx-[50vw] w-screen -mt-4 sm:-mt-6 lg:-mt-8 pb-28 sm:pb-12">

    {{-- ===== HEADER GRADIENT ===== --}}
    <header class="relative overflow-hidden bg-gradient-to-br from-orange-500 via-red-500 to-red-800 text-white rounded-b-[2rem] sm:rounded-b-[2.5rem] shadow-lg shadow-orange-500/20 pb-24 sm:pb-28">
        <div class="pointer-events-none absolute -top-16 -right-12 w-56 h-56 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute top-28 -left-16 w-44 h-44 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-10 right-1/4 w-32 h-32 rounded-full bg-black/10"></div>

        <div class="relative max-w-2xl mx-auto px-5 sm:px-6 pt-[max(1.25rem,env(safe-area-inset-top))]">
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('siswa.finance.history') }}"
                   class="group inline-flex items-center gap-2 pl-2 pr-4 py-1.5 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur text-xs font-bold uppercase tracking-wider transition-colors">
                    <span class="material-symbols-outlined text-base transition-transform group-hover:-translate-x-0.5">arrow_back</span>
                    <span>Kembali</span>
                </a>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="shareReceipt()" aria-label="Bagikan link"
                            class="inline-flex items-center justify-center gap-1.5 h-9 w-9 sm:w-auto sm:px-4 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur text-xs font-bold uppercase tracking-wider transition-all active:scale-95">
                        <span class="material-symbols-outlined text-base">ios_share</span>
                        <span class="hidden sm:inline">Bagikan</span>
                    </button>
                    <a href="{{ route('siswa.finance.transactions.receipt', $transaction) }}" target="_blank" aria-label="Cetak struk"
                       class="inline-flex items-center justify-center gap-1.5 h-9 px-3.5 sm:px-4 rounded-full bg-white text-orange-600 hover:bg-orange-50 text-xs font-extrabold uppercase tracking-wider shadow-sm transition-all active:scale-95">
                        <span class="material-symbols-outlined text-base">print</span>
                        <span>Cetak</span>
                    </a>
                </div>
            </div>

            <div class="text-center mt-6 sm:mt-8">
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-[0.2em] text-orange-100/80">Informasi Transaksi</p>
                <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold tracking-tight">Detail Transaksi</h1>
                <p class="mt-1.5 text-xs sm:text-sm text-orange-50/80">Rincian dan validasi saldo Anda.</p>
            </div>
        </div>
    </header>

    {{-- ===== KARTU NOMINAL (overlap header) ===== --}}
    <div class="max-w-2xl mx-auto px-4 sm:px-6 -mt-16 sm:-mt-20 relative space-y-4">

        <section class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/60 p-6 sm:p-8 text-center">
            <div class="mx-auto w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-500 to-red-600 text-white flex items-center justify-center shadow-lg shadow-orange-500/30">
                <span class="material-symbols-outlined text-2xl">{{ $txIcon }}</span>
            </div>

            <span class="mt-4 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-50 text-slate-600 border border-slate-100">
                <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
                {{ $txFlowLabel }}
            </span>

            <p class="mt-4 text-[10px] uppercase tracking-widest text-slate-400 font-bold">Nominal Transaksi</p>
            <p class="mt-1 text-3xl sm:text-4xl font-extrabold tracking-tight break-words {{ $amountColor }}">
                {{ $isCredit ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
            </p>
            <p class="mt-1 text-xs font-semibold text-slate-500">{{ $txLabel }}</p>

            {{-- Alur saldo --}}
            <div class="mt-6 grid grid-cols-[1fr_auto_1fr] items-center gap-3 rounded-2xl bg-slate-50 border border-slate-100 p-4 text-left">
                <div class="min-w-0">
                    <p class="text-[9px] uppercase tracking-wider text-slate-400 font-bold">Saldo Sebelum</p>
                    <p class="text-sm font-bold text-slate-500 truncate">Rp {{ number_format($transaction->balance_before, 0, ',', '.') }}</p>
                </div>
                <span class="material-symbols-outlined text-orange-500">arrow_right_alt</span>
                <div class="min-w-0 text-right">
                    <p class="text-[9px] uppercase tracking-wider text-slate-400 font-bold">Saldo Sesudah</p>
                    <p class="text-sm font-extrabold text-slate-800 truncate">Rp {{ number_format($transaction->balance_after, 0, ',', '.') }}</p>
                </div>
            </div>
        </section>

        {{-- ===== RINCIAN TRANSAKSI ===== --}}
        <section class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5 sm:p-6">
            <h2 class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-slate-400">
                <span class="w-1 h-3.5 rounded-full bg-gradient-to-b from-orange-500 to-red-600"></span>
                Rincian Transaksi
            </h2>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="p-4 bg-slate-50/70 border border-slate-100 rounded-2xl">
                    <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Jenis Layanan</span>
                    <span class="mt-1 block text-sm font-extrabold text-slate-700">{{ $txLabel }}</span>
                </div>

                <div class="p-4 bg-slate-50/70 border border-slate-100 rounded-2xl">
                    <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Waktu Transaksi</span>
                    <span class="mt-1 block text-sm font-bold text-slate-700">{{ $transaction->created_at->format('d M Y, H:i') }} WIB</span>
                </div>

                <div class="p-4 bg-slate-50/70 border border-slate-100 rounded-2xl sm:col-span-2">
                    <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Nomor Referensi</span>
                    <div class="mt-1 flex items-center justify-between gap-2">
                        <span class="text-sm font-mono font-bold text-slate-700 break-all" id="refNumberDetail">{{ $transaction->reference_number ?? '-' }}</span>
                        <button type="button" onclick="copyRefCodeDetail()" title="Salin kode" aria-label="Salin nomor referensi"
                                class="shrink-0 w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-orange-600 hover:border-orange-200 active:scale-90 transition-all">
                            <span class="material-symbols-outlined text-base">content_copy</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== AKUN & KARTU ===== --}}
        <section class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5 sm:p-6">
            <h2 class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-slate-400">
                <span class="w-1 h-3.5 rounded-full bg-gradient-to-b from-orange-500 to-red-600"></span>
                Akun &amp; Kartu
            </h2>

            <dl class="mt-2 divide-y divide-slate-100 text-xs">
                <div class="flex justify-between items-center gap-4 py-3.5">
                    <dt class="text-slate-400 font-semibold uppercase tracking-wider text-[9px]">Pemilik Rekening</dt>
                    <dd class="font-bold text-slate-700 text-right">{{ $student->nama_lengkap ?? $student->name ?? '-' }}</dd>
                </div>

                @if(isset($student->nis_siswa) || isset($student->nis))
                <div class="flex justify-between items-center gap-4 py-3.5">
                    <dt class="text-slate-400 font-semibold uppercase tracking-wider text-[9px]">NIS Siswa</dt>
                    <dd class="font-mono font-bold text-slate-700">{{ $student->nis_siswa ?? $student->nis ?? '-' }}</dd>
                </div>
                @endif

                <div class="flex justify-between items-center gap-4 py-3.5">
                    <dt class="text-slate-400 font-semibold uppercase tracking-wider text-[9px]">ID Kartu / RFID</dt>
                    <dd class="font-mono font-bold text-slate-700 break-all text-right">{{ $transaction->card->card_number }}</dd>
                </div>
            </dl>
        </section>

        {{-- ===== KETERANGAN & PELAKSANA ===== --}}
        <section class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5 sm:p-6">
            <h2 class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-slate-400">
                <span class="w-1 h-3.5 rounded-full bg-gradient-to-b from-orange-500 to-red-600"></span>
                Keterangan &amp; Pelaksana
            </h2>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="p-4 bg-slate-50/70 border border-slate-100 rounded-2xl">
                    <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Petugas Pelaksana</span>
                    <span class="mt-1 block text-sm font-bold text-slate-700">{{ $transaction->performer_name }}</span>
                </div>

                <div class="p-4 bg-slate-50/70 border border-slate-100 rounded-2xl">
                    <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Deskripsi</span>
                    <span class="mt-1 block text-sm font-bold text-slate-700 break-words">{{ $transaction->description ?: '-' }}</span>
                </div>

                @if($transaction->relatedCard)
                <div class="p-4 bg-slate-50/70 border border-slate-100 rounded-2xl sm:col-span-2">
                    <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Hubungan Kartu Terkait</span>
                    <span class="mt-1 block text-sm font-bold text-slate-700 break-words">{{ $transaction->relatedCard->student->name }} - {{ $transaction->relatedCard->card_number }}</span>
                </div>
                @endif
            </div>
        </section>
    </div>
</div>

<!-- Toast -->
<div id="toastDetail" class="hidden fixed bottom-24 sm:bottom-6 left-1/2 -translate-x-1/2 max-w-[90vw] bg-slate-900/95 backdrop-blur text-white text-xs font-semibold px-4 py-3 rounded-2xl shadow-xl flex items-center gap-2 z-50">
    <span class="material-symbols-outlined text-sm text-emerald-400" id="toastIcon">check_circle</span>
    <span id="toastText">Teks berhasil disalin</span>
</div>

<script>
    let toastTimer;
    function showToast(message, isError = false) {
        const toast = document.getElementById('toastDetail');
        const toastText = document.getElementById('toastText');
        const toastIcon = document.getElementById('toastIcon');

        toastText.textContent = message;
        toastIcon.textContent = isError ? 'warning' : 'check_circle';
        toastIcon.className = 'material-symbols-outlined text-sm ' + (isError ? 'text-rose-400' : 'text-emerald-400');

        toast.classList.remove('hidden');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.add('hidden'), 2500);
    }

    async function copyText(text) {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
            return;
        }
        const tempInput = document.createElement('input');
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
    }

    async function copyRefCodeDetail() {
        try {
            await copyText(document.getElementById('refNumberDetail').textContent.trim());
            showToast('Nomor Referensi berhasil disalin.');
        } catch (e) {
            showToast('Gagal menyalin nomor referensi.', true);
        }
    }

    async function shareReceipt() {
        const url = @json(route('siswa.finance.transactions.receipt', $transaction));

        if (navigator.share) {
            try {
                await navigator.share({
                    title: 'Detail Transaksi',
                    text: 'Berikut adalah tautan bukti transaksi digital saya.',
                    url: url,
                });
                return;
            } catch (error) {
                if (error && error.name === 'AbortError') return;
            }
        }

        try {
            await copyText(url);
            showToast('Tautan struk disalin ke papan klip.');
        } catch (error) {
            showToast('Gagal menyalin tautan secara otomatis.', true);
        }
    }
</script>
@endsection