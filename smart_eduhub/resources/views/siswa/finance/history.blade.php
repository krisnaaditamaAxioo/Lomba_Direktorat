@extends('layouts.siswa')

@section('title', 'Riwayat Transaksi')
@section('page_title', 'Riwayat Transaksi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 px-4 sm:px-0">
    <div class="flex flex-col gap-3">
        <div>
            <a href="{{ route('siswa.finance') }}" class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-500 hover:text-slate-800 transition-colors group mb-3">
                <span class="material-symbols-outlined text-sm font-bold transition-transform group-hover:-translate-x-0.5">arrow_back</span>
                <span>Kembali</span>
            </a>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Semua Riwayat Transaksi</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-0.5">Halaman khusus untuk melihat detail riwayat transaksi dan struk.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 p-4 sm:p-6 shadow-sm">
        <div class="space-y-2.5">
            @forelse($transactions as $transaction)
            @php
                $isCredit = in_array($transaction->type, ['topup', 'transfer_receive']);
                $txIcon = $transaction->type === 'topup' ? 'add_card' :
                    ($transaction->type === 'payment' ? 'shopping_cart' :
                    ($transaction->type === 'transfer' ? 'send' :
                    ($transaction->type === 'transfer_receive' ? 'call_received' :
                    ($transaction->type === 'withdraw' ? 'point_of_sale' : 'receipt'))));
                $txColorClass = $transaction->type === 'topup' ? 'bg-emerald-50/70 text-emerald-600' :
                    ($transaction->type === 'payment' ? 'bg-rose-50/70 text-rose-600' :
                    ($transaction->type === 'transfer' ? 'bg-brand-50/70 text-brand-600' :
                    ($transaction->type === 'transfer_receive' ? 'bg-cyan-50/70 text-cyan-600' :
                    ($transaction->type === 'withdraw' ? 'bg-amber-50/70 text-amber-600' : 'bg-brand-50/70 text-brand-600'))));
                $txLabel = $transaction->type === 'topup' ? 'Top Up Saldo' :
                    ($transaction->type === 'payment' ? 'Pembelian / Belanja' :
                    ($transaction->type === 'transfer' ? 'Transfer Keluar' :
                    ($transaction->type === 'transfer_receive' ? 'Transfer Masuk' :
                    ($transaction->type === 'withdraw' ? 'Tarik Tunai' : ucfirst($transaction->type))));
            @endphp
            <a href="{{ route('siswa.finance.transactions.show', $transaction) }}" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3.5 sm:p-4 rounded-xl border border-slate-100 hover:border-slate-200 hover:shadow-xs transition-all duration-200 bg-white group">
                
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $txColorClass }}">
                        <span class="material-symbols-outlined text-lg">
                            {{ $txIcon }}
                        </span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800 group-hover:text-brand-600 transition-colors truncate">
                            {{ $txLabel }}
                        </p>
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[11px] text-slate-400 mt-0.5">
                            <span>{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                            @if($transaction->reference_number)
                            <span class="text-slate-200">•</span>
                            <span class="font-mono truncate max-w-[120px] sm:max-w-none">{{ $transaction->reference_number }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100 sm:hidden">

                <div class="flex flex-row sm:flex-col justify-between sm:items-end gap-1.5 pt-1 sm:pt-0">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $isCredit ? 'bg-emerald-50 text-emerald-700' : ($transaction->type === 'withdraw' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                            {{ $isCredit ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="text-right">
                        @if($transaction->description)
                        <p class="text-[11px] text-slate-500 line-clamp-1 max-w-[180px] sm:max-w-none">{{ $transaction->description }}</p>
                        @endif
                        <p class="text-[10px] text-slate-400 mt-0.5">Saldo: Rp {{ number_format($transaction->balance_after, 0, ',', '.') }}</p>
                    </div>
                </div>
            </a>
            @empty
            <div class="py-16 text-center">
                <div class="w-12 h-12 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center mx-auto mb-3 border border-slate-100">
                    <span class="material-symbols-outlined text-xl">receipt_long</span>
                </div>
                <p class="text-sm font-semibold text-slate-700">Belum Ada Transaksi</p>
                <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">Riwayat pembayaran atau pengisian saldo Anda akan muncul di sini.</p>
            </div>
            @endforelse
        </div>

        @if(method_exists($transactions, 'links'))
        <div class="mt-5 pt-4 border-t border-slate-50">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection