@extends('layouts.siswa')

@section('title', 'Detail Transaksi')
@section('page_title', 'Detail Transaksi')

@section('content')
@php
    $isCredit = in_array($transaction->type, ['topup', 'transfer_receive']);
    switch ($transaction->type) {
        case 'topup':            $txLabel = 'Top Up Saldo'; $txFlowLabel = 'Dana Masuk'; $badgeColor = 'emerald'; break;
        case 'payment':          $txLabel = 'Pembayaran / Belanja'; $txFlowLabel = 'Dana Keluar'; $badgeColor = 'rose'; break;
        case 'transfer':         $txLabel = 'Transfer Keluar'; $txFlowLabel = 'Dana Keluar'; $badgeColor = 'indigo'; break;
        case 'transfer_receive': $txLabel = 'Transfer Masuk'; $txFlowLabel = 'Dana Masuk'; $badgeColor = 'cyan'; break;
        case 'withdraw':         $txLabel = 'Tarik Tunai'; $txFlowLabel = 'Dana Keluar'; $badgeColor = 'amber'; break;
        default:                 $txLabel = ucfirst($transaction->type); $txFlowLabel = $isCredit ? 'Dana Masuk' : 'Dana Keluar'; $badgeColor = 'slate';
    }
    $badgeClass = match($badgeColor) {
        'emerald' => 'bg-emerald-50 text-emerald-600 border border-emerald-100',
        'rose'    => 'bg-rose-50 text-rose-600 border border-rose-100',
        'indigo'  => 'bg-brand-50 text-brand-600 border border-brand-100',
        'cyan'    => 'bg-cyan-50 text-cyan-600 border border-cyan-100',
        'amber'   => 'bg-amber-50 text-amber-600 border border-amber-100',
        default   => 'bg-slate-50 text-slate-600 border border-slate-100',
    };
    $dotColor = match($badgeColor) {
        'emerald' => 'bg-emerald-500',
        'rose'    => 'bg-rose-500',
        'indigo'  => 'bg-brand-500',
        'cyan'    => 'bg-cyan-500',
        'amber'   => 'bg-amber-500',
        default   => 'bg-slate-500',
    };
    $amountColor = $isCredit ? 'text-emerald-600' : ($transaction->type === 'withdraw' ? 'text-amber-700' : 'text-slate-800');
@endphp

<div class="max-w-2xl mx-auto space-y-6 pb-16 px-4 sm:px-0 bg-slate-50/30">
    
    <!-- Top Action & Navigation Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-2 border-b border-slate-100 pb-4">
        <!-- Left Side: Back Button & Title -->
        <div class="flex items-center gap-3">
            <a href="{{ route('siswa.finance.history') }}" class="inline-flex items-center justify-center w-10 h-10 bg-white border border-slate-200/80 rounded-full text-slate-500 hover:text-brand-600 transition-all active:scale-90 shadow-sm hover:shadow">
                <span class="material-symbols-outlined text-xl">arrow_back</span>
            </a>
            <div>
                <h1 class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em]">Informasi Transaksi</h1>
                <p class="text-xs font-medium text-slate-500 mt-0.5">Detail & Validasi Saldo</p>
            </div>
        </div>

        <!-- Right Side: Top Action Buttons (Cetak & Bagi Link diletakkan di paling atas) -->
        <div class="flex items-center gap-2">
            <button type="button" onclick="shareReceipt()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-2xl text-xs font-bold uppercase tracking-wider text-slate-600 transition-all active:scale-95 shadow-sm">
                <span class="material-symbols-outlined text-base text-brand-600">ios_share</span>
                <span>Bagikan Link</span>
            </button>
            <a href="{{ route('siswa.finance.transactions.receipt', $transaction) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-2xl text-xs font-bold uppercase tracking-wider transition-all active:scale-95 shadow-md shadow-brand-100">
                <span class="material-symbols-outlined text-base">print</span>
                <span>Cetak Struk</span>
            </a>
        </div>
    </div>

    <!-- Main Info Container -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden p-6 sm:p-8 space-y-8">
        
        <!-- Status & Big Amount Header -->
        <div class="flex flex-col items-center text-center space-y-3 pb-6 border-b border-slate-100">
            <!-- Dynamic Status Badge -->
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badgeClass }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
                {{ $txFlowLabel }}
            </span>
            
            <!-- Big Amount Display -->
            <div class="space-y-1">
                <span class="text-[10px] uppercase tracking-widest text-slate-400 font-bold block">Nominal Transaksi</span>
                <p class="text-3xl sm:text-4xl font-extrabold tracking-tight {{ $amountColor }}">
                    {{ $isCredit ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Information Section: Core Transaction Details -->
        <div class="space-y-4">
            <h4 class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Rincian Transaksi</h4>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 bg-slate-50/50 border border-slate-100 rounded-2xl space-y-1">
                    <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Jenis Layanan</span>
                    <span class="text-xs font-extrabold text-slate-700">{{ $txLabel }}</span>
                </div>

                <div class="p-4 bg-slate-50/50 border border-slate-100 rounded-2xl space-y-1">
                    <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Waktu Transaksi</span>
                    <span class="text-xs font-bold text-slate-700">{{ $transaction->created_at->format('d M Y, H:i') }} WIB</span>
                </div>

                <div class="p-4 bg-slate-50/50 border border-slate-100 rounded-2xl space-y-1 sm:col-span-2">
                    <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Nomor Referensi</span>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono font-bold text-slate-700" id="refNumberDetail">{{ $transaction->reference_number ?? '-' }}</span>
                        <button onclick="copyRefCodeDetail()" class="p-1 text-slate-400 hover:text-brand-600 active:scale-90 transition-all rounded" title="Salin kode">
                            <span class="material-symbols-outlined text-base">content_copy</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Section: Account Metadata -->
        <div class="space-y-4 pt-2">
            <h4 class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Akun & Kartu</h4>
            
            <div class="divide-y divide-slate-100 text-xs">
                <div class="flex justify-between items-center py-3">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider text-[9px]">Pemilik Rekening</span>
                    <span class="font-bold text-slate-700">{{ $transaction->card->student->nama_lengkap ?? $transaction->card->student->name }}</span>
                </div>

                @if(isset($transaction->card->student->nis_siswa) || isset($transaction->card->student->nis))
                <div class="flex justify-between items-center py-3">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider text-[9px]">NIS Siswa</span>
                    <span class="font-mono font-bold text-slate-700">{{ $transaction->card->student->nis_siswa ?? $transaction->card->student->nis ?? '-' }}</span>
                </div>
                @endif

                <div class="flex justify-between items-center py-3">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider text-[9px]">ID Kartu / RFID</span>
                    <span class="font-mono font-bold text-slate-700">{{ $transaction->card->card_number }}</span>
                </div>

                <div class="flex justify-between items-center py-3">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider text-[9px]">Saldo Sebelum</span>
                    <span class="font-bold text-slate-500">Rp {{ number_format($transaction->balance_before, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between items-center py-3">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider text-[9px]">Saldo Sesudah</span>
                    <span class="font-extrabold text-slate-800 text-sm">Rp {{ number_format($transaction->balance_after, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Information Section: Note & Operator -->
        <div class="space-y-4 pt-2">
            <h4 class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Keterangan & Pelaksana</h4>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 bg-slate-50/50 border border-slate-100 rounded-2xl space-y-1">
                    <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Petugas Pelaksana</span>
                    <span class="text-xs font-bold text-slate-700">{{ $transaction->performer_name }}</span>
                </div>

                <div class="p-4 bg-slate-50/50 border border-slate-100 rounded-2xl space-y-1">
                    <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Deskripsi</span>
                    <span class="text-xs font-bold text-slate-700 block truncate" title="{{ $transaction->description }}">{{ $transaction->description ?: '-' }}</span>
                </div>

                @if($transaction->relatedCard)
                <div class="p-4 bg-slate-50/50 border border-slate-100 rounded-2xl space-y-1 sm:col-span-2">
                    <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Hubungan Kartu Terkait</span>
                    <span class="text-xs font-bold text-slate-700">{{ $transaction->relatedCard->student->name }} - {{ $transaction->relatedCard->card_number }}</span>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

<!-- Soft Floating Toast Notification -->
<div id="toastDetail" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-xs font-semibold px-4 py-3 rounded-2xl shadow-xl flex items-center gap-2 z-50 transition-all">
    <span class="material-symbols-outlined text-sm text-emerald-400" id="toastIcon">check_circle</span>
    <span id="toastText">Teks berhasil disalin</span>
</div>

<script>
    // Custom Soft Toast Helper
    function showToast(message, isError = false) {
        const toast = document.getElementById('toastDetail');
        const toastText = document.getElementById('toastText');
        const toastIcon = document.getElementById('toastIcon');

        toastText.textContent = message;
        if (isError) {
            toastIcon.textContent = 'warning';
            toastIcon.className = 'material-symbols-outlined text-sm text-rose-400';
        } else {
            toastIcon.textContent = 'check_circle';
            toastIcon.className = 'material-symbols-outlined text-sm text-emerald-400';
        }

        toast.classList.remove('hidden');
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 2500);
    }

    // Copy Reference Code
    function copyRefCodeDetail() {
        const refText = document.getElementById('refNumberDetail').textContent;
        
        const tempInput = document.createElement('input');
        tempInput.value = refText;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);

        showToast('Nomor Referensi berhasil disalin.');
    }

    // Share Receipt Link
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
                if (error && error.name === 'AbortError') {
                    return;
                }
            }
        }

        // Fallback copying
        try {
            const tempInput = document.createElement('input');
            tempInput.value = url;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            
            showToast('Tautan struk disalin ke papan klip.');
        } catch (error) {
            showToast('Gagal menyalin tautan secara otomatis.', true);
        }
    }
</script>
@endsection