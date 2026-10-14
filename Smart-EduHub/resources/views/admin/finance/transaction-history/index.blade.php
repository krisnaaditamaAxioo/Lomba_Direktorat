@extends('layouts.app')

@section('content')
@php
    $actionBadge = function($action) {
        return match($action) {
            'created' => 'bg-emerald-100 text-emerald-700',
            'updated' => 'bg-amber-100 text-amber-700',
            'deleted' => 'bg-rose-100 text-rose-700',
            default => 'bg-slate-100 text-slate-700',
        };
    };
    $actionIcon = function($action) {
        return match($action) {
            'created' => 'add_circle',
            'updated' => 'edit',
            'deleted' => 'delete',
            default => 'history',
        };
    };
    $typeLabel = function($type) {
        return match($type) {
            'topup' => 'Topup',
            'payment' => 'Pembayaran',
            'withdraw' => 'Tarik Tunai',
            'transfer' => 'Transfer Keluar',
            'transfer_receive' => 'Transfer Masuk',
            default => ucfirst($type),
        };
    };
@endphp
<div class="space-y-8">
    @include('admin.partials.page-header', [
        'title' => 'Riwayat Perubahan Transaksi',
        'eyebrow' => 'Audit Trail Keuangan',
        'subtitle' => 'Catatan lengkap setiap pembuatan, perubahan, dan penghapusan transaksi.',
        'icon' => 'history',
    ])

    {{-- Statistik Ringkas --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 p-6 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[28px]">add_circle</span>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Dibuat</p>
                <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $stats['created'] }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 p-6 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[28px]">edit</span>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Diubah</p>
                <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $stats['updated'] }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 p-6 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[28px]">delete</span>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Dihapus</p>
                <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $stats['deleted'] }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3">
        <span class="material-symbols-outlined">check_circle</span>
        <span class="text-sm font-bold">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Form Filter --}}
    <form action="{{ route('admin.finance.transaction-history.index') }}" method="GET" class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 p-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Aksi</label>
                <select name="action" class="w-full mt-2 px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none">
                    <option value="">Semua</option>
                    <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Dibuat</option>
                    <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Diubah</option>
                    <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Dihapus</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Dari Tanggal</label>
                <input type="date" name="from" value="{{ request('from') }}" class="w-full mt-2 px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none">
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sampai Tanggal</label>
                <input type="date" name="to" value="{{ request('to') }}" class="w-full mt-2 px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none">
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kata Kunci</label>
                <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Cari nama siswa, NIS, atau nomor kartu..." class="w-full mt-2 px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none">
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 mt-4">
            <a href="{{ route('admin.finance.transaction-history.index') }}" class="px-5 py-3 bg-slate-100 text-slate-700 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-slate-200 transition-all">Reset</a>
            <button type="submit" class="px-6 py-3 bg-orange-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-orange-700 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-base">filter_alt</span>
                Filter
            </button>
        </div>
    </form>

    {{-- Tabel Riwayat --}}
    <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Waktu</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Aksi</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tipe Trx</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Jumlah</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Siswa</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Kartu</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Pelaku</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($histories as $history)
                    @php
                        $snap = $history->snapshot ?? [];
                    @endphp
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300 whitespace-nowrap">{{ $history->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest {{ $actionBadge($history->action) }}">
                                <span class="material-symbols-outlined text-[14px]">{{ $actionIcon($history->action) }}</span>
                                {{ $history->action_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-slate-900 dark:text-white">{{ $typeLabel($snap['type'] ?? '-') }}</td>
                        <td class="px-6 py-4 font-black text-slate-900 dark:text-white whitespace-nowrap">Rp {{ number_format($snap['amount'] ?? 0, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            <p class="font-bold text-slate-700 dark:text-slate-200">{{ $snap['student_name'] ?? '-' }}</p>
                            <p class="text-[10px] text-slate-400 uppercase tracking-widest">NIS: {{ $snap['student_nis'] ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            <p class="font-bold text-slate-700 dark:text-slate-200">{{ $snap['card_number'] ?? '-' }}</p>
                            <p class="text-[10px] text-slate-400 uppercase tracking-widest">ID Trx: #{{ $snap['id'] ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            @if($history->performedBy)
                                <p class="font-bold text-slate-700 dark:text-slate-200">{{ $history->performedBy->name }}</p>
                                <p class="text-[10px] text-slate-400 uppercase tracking-widest">{{ $history->performedBy->role_label }}</p>
                            @else
                                <span class="text-slate-400">Sistem/Siswa</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.finance.transaction-history.show', $history) }}" class="inline-flex items-center gap-1 px-3 py-2 bg-orange-100 text-orange-700 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-orange-200 transition-all">
                                <span class="material-symbols-outlined text-xs">visibility</span>
                                Lihat
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl mb-2 block">inbox</span>
                            Belum ada riwayat perubahan transaksi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="px-2">
        {{ $histories->links() }}
    </div>
</div>
@endsection
