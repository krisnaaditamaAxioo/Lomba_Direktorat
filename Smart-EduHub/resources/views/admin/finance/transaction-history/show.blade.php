@extends('layouts.app')

@section('content')
@php
    $snap = $history->snapshot ?? [];
    $typeLabel = match($snap['type'] ?? '') {
        'topup' => 'Topup',
        'payment' => 'Pembayaran',
        'withdraw' => 'Tarik Tunai',
        'transfer' => 'Transfer Keluar',
        'transfer_receive' => 'Transfer Masuk',
        default => ucfirst($snap['type'] ?? '-'),
    };
    $actionBadge = match($history->action) {
        'created' => 'bg-emerald-100 text-emerald-700',
        'updated' => 'bg-amber-100 text-amber-700',
        'deleted' => 'bg-rose-100 text-rose-700',
        default => 'bg-slate-100 text-slate-700',
    };
    $actionIcon = match($history->action) {
        'created' => 'add_circle',
        'updated' => 'edit',
        'deleted' => 'delete',
        default => 'history',
    };
@endphp
<div class="space-y-8">
    @include('admin.partials.page-header', [
        'title' => 'Detail Riwayat Transaksi',
        'eyebrow' => 'Audit Trail Keuangan',
        'subtitle' => 'Detail lengkap snapshot transaksi pada saat aksi dilakukan.',
        'icon' => 'history',
    ])

    <div class="flex justify-center">
        <a href="{{ route('admin.finance.transaction-history.index') }}" class="px-5 py-3 bg-slate-100 text-slate-700 rounded-2xl font-bold text-sm hover:bg-slate-200 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali ke Daftar Riwayat
        </a>
    </div>

    {{-- Info Aksi --}}
    <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl {{ $actionBadge }} flex items-center justify-center">
                    <span class="material-symbols-outlined text-[28px]">{{ $actionIcon }}</span>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest {{ $actionBadge }}">
                        {{ $history->action_label }}
                    </span>
                    <p class="text-sm text-slate-500 mt-1">{{ $history->created_at->format('d F Y H:i:s') }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ID Riwayat</p>
                <p class="text-lg font-black text-slate-900 dark:text-white">#{{ $history->id }}</p>
            </div>
        </div>
    </div>

    {{-- Perubahan (jika aksi = updated) --}}
    @if($history->action === 'updated' && !empty($history->changes))
    <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-amber-100 dark:border-amber-900/30 p-6 shadow-sm">
        <h3 class="text-lg font-black text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-500">swap_horiz</span>
            Perubahan Data
        </h3>
        <div class="space-y-3">
            @foreach($history->changes as $field => $change)
            <div class="grid grid-cols-1 md:grid-cols-[140px_1fr_1fr] gap-3 items-center bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $field }}</div>
                <div>
                    <p class="text-[10px] font-black text-rose-400 uppercase tracking-widest">Sebelumnya</p>
                    <p class="text-sm font-bold text-rose-600 dark:text-rose-400 break-words">{{ $change['old'] ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest">Sesudahnya</p>
                    <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400 break-words">{{ $change['new'] ?? '-' }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Snapshot Transaksi --}}
    <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 p-6 shadow-sm">
        <h3 class="text-lg font-black text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-orange-500">snapshot</span>
            Snapshot Data Transaksi
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ID Transaksi</p>
                <p class="text-sm font-bold text-slate-900 dark:text-white mt-1">#{{ $snap['id'] ?? '-' }}</p>
            </div>
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tipe Transaksi</p>
                <p class="text-sm font-bold text-slate-900 dark:text-white mt-1">{{ $typeLabel }}</p>
            </div>
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ID Kartu</p>
                <p class="text-sm font-bold text-slate-900 dark:text-white mt-1">#{{ $snap['card_id'] ?? '-' }}</p>
            </div>
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nomor Referensi</p>
                <p class="text-sm font-bold text-slate-900 dark:text-white mt-1">{{ $snap['reference_number'] ?? '-' }}</p>
            </div>
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Jumlah</p>
                <p class="text-sm font-black text-orange-600 mt-1">Rp {{ number_format($snap['amount'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ID Kartu Terkait</p>
                <p class="text-sm font-bold text-slate-900 dark:text-white mt-1">{{ $snap['related_card_id'] ?? '-' }}</p>
            </div>
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Saldo Sebelum</p>
                <p class="text-sm font-bold text-slate-900 dark:text-white mt-1">Rp {{ number_format($snap['balance_before'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Saldo Sesudah</p>
                <p class="text-sm font-bold text-slate-900 dark:text-white mt-1">Rp {{ number_format($snap['balance_after'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="md:col-span-2 bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Deskripsi</p>
                <p class="text-sm font-bold text-slate-900 dark:text-white mt-1 break-words">{{ $snap['description'] ?? '-' }}</p>
            </div>
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Dilakukan Oleh (User ID)</p>
                <p class="text-sm font-bold text-slate-900 dark:text-white mt-1">{{ $snap['performed_by'] ?? '-' }}</p>
            </div>
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Waktu Transaksi</p>
                <p class="text-sm font-bold text-slate-900 dark:text-white mt-1">{{ $snap['created_at'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Pelaku Aksi --}}
    <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 p-6 shadow-sm">
        <h3 class="text-lg font-black text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-orange-500">person</span>
            Pelaku Aksi
        </h3>
        @if($history->performedBy)
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[28px]">person</span>
            </div>
            <div>
                <p class="text-base font-black text-slate-900 dark:text-white">{{ $history->performedBy->name }}</p>
                <p class="text-xs text-slate-500">{{ $history->performedBy->role_label }} &middot; {{ $history->performedBy->email ?? '-' }}</p>
            </div>
        </div>
        @else
        <p class="text-sm text-slate-400">Aksi dilakukan oleh sistem atau siswa (tidak tercatat user admin).</p>
        @endif
    </div>
</div>
@endsection
