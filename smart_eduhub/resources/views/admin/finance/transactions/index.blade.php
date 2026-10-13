@extends('layouts.app')

@section('content')
@php
    $showAll = request()->boolean('all');
    $isPaginator = $transactions instanceof \Illuminate\Contracts\Pagination\Paginator;
    $rows = $isPaginator ? collect($transactions->items()) : collect($transactions);
    if (!$showAll) {
        $rows = $rows->take(5);
    }
@endphp
<div class="space-y-8">
    @include('admin.partials.page-header', [
        'title' => 'Manajemen Transaksi',
        'eyebrow' => 'Riwayat Transaksi',
        'subtitle' => 'Lihat, edit, dan hapus transaksi kartu siswa.',
        'icon' => 'receipt_long',
    ])

    <div class="flex justify-center">
        <a href="{{ route('admin.finance.cards') }}" class="px-5 py-3 bg-slate-100 text-slate-700 rounded-2xl font-bold text-sm hover:bg-slate-200 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali ke Kartu
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-4">
        <form action="{{ route('admin.finance.transactions.index') }}" method="GET" class="space-y-4 xl:col-span-1 bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 p-6 shadow-sm">
            @if($showAll)
                <input type="hidden" name="all" value="1">
            @endif
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Siswa</label>
                <input type="text" name="student_name" value="{{ request('student_name') }}" class="w-full mt-2 px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none" placeholder="Cari nama siswa...">
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nomor Kartu</label>
                <input type="text" name="card_number" value="{{ request('card_number') }}" class="w-full mt-2 px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none" placeholder="Cari nomor kartu...">
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Jenis Transaksi</label>
                <select name="type" class="w-full mt-2 px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none">
                    <option value="">Semua</option>
                    <option value="topup" {{ request('type') === 'topup' ? 'selected' : '' }}>Topup</option>
                    <option value="payment" {{ request('type') === 'payment' ? 'selected' : '' }}>Payment</option>
                    <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>Transfer Keluar</option>
                    <option value="transfer_receive" {{ request('type') === 'transfer_receive' ? 'selected' : '' }}>Transfer Masuk</option>
                    <option value="withdraw" {{ request('type') === 'withdraw' ? 'selected' : '' }}>Tarik Tunai</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full mt-2 px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none">
            </div>
            <button type="submit" class="w-full py-3 bg-orange-600 text-white rounded-2xl font-black uppercase tracking-widest text-sm hover:bg-orange-700 transition-all">Filter</button>
        </form>

        <div class="xl:col-span-3 space-y-4">
            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3">
                <span class="material-symbols-outlined">check_circle</span>
                <span class="text-sm font-bold">{{ session('success') }}</span>
            </div>
            @endif
            @if(session('error'))
            <div class="bg-rose-50 border border-rose-100 text-rose-700 px-6 py-4 rounded-2xl flex items-center gap-3">
                <span class="material-symbols-outlined">error</span>
                <span class="text-sm font-bold">{{ session('error') }}</span>
            </div>
            @endif

            {{-- Toggle 5 terbaru / semua --}}
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                    {{ $showAll ? 'Menampilkan semua riwayat' : 'Menampilkan 5 riwayat terbaru' }}
                </p>
                @if($showAll)
                    <a href="{{ route('admin.finance.transactions.index', request()->except(['all', 'page'])) }}"
                       class="px-4 py-2 bg-slate-100 text-slate-700 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-slate-200 transition-all">
                        Tampilkan 5 Terbaru
                    </a>
                @else
                    <a href="{{ route('admin.finance.transactions.index', array_merge(request()->except('page'), ['all' => 1])) }}"
                       class="px-4 py-2 bg-orange-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-orange-700 transition-all">
                        All
                    </a>
                @endif
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Waktu</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Siswa</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tipe</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Jumlah</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Saldo Setelah</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Petugas</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($rows as $transaction)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $transaction->card->student->nama_lengkap }}</p>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-widest">{{ $transaction->card->card_number }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm uppercase tracking-wider {{ $transaction->type === 'topup' ? 'text-emerald-600' : ($transaction->type === 'payment' ? 'text-rose-600' : ($transaction->type === 'withdraw' ? 'text-amber-600' : ($transaction->type === 'transfer_receive' ? 'text-cyan-600' : 'text-orange-600'))) }}">
                                    @if($transaction->type === 'transfer_receive')
                                        Transfer Masuk
                                    @elseif($transaction->type === 'transfer')
                                        Transfer Keluar
                                    @elseif($transaction->type === 'withdraw')
                                        Tarik Tunai
                                    @elseif($transaction->type === 'topup')
                                        Topup
                                    @else
                                        {{ $transaction->type }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-black text-slate-900">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">Rp {{ number_format($transaction->balance_after, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $transaction->performer_name }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.finance.transactions.receipt', $transaction) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-2 bg-orange-100 text-orange-700 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-orange-200 transition-all">
                                        <span class="material-symbols-outlined text-xs">receipt</span>
                                        Struk
                                    </a>
                                    <a href="{{ route('admin.finance.transactions.edit', $transaction) }}" class="inline-flex items-center gap-1 px-3 py-2 bg-slate-100 text-slate-700 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-slate-200 transition-all">
                                        <span class="material-symbols-outlined text-xs">edit</span>
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.finance.transactions.destroy', $transaction) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        @php
                                            if ($transaction->type === 'topup') {
                                                $confirmMsg = "Hapus topup Rp " . number_format($transaction->amount, 0, ',', '.') . "? Saldo akan berkurang Rp " . number_format($transaction->amount, 0, ',', '.') . ".";
                                            } elseif ($transaction->type === 'withdraw') {
                                                $confirmMsg = "Hapus tarik tunai Rp " . number_format($transaction->amount, 0, ',', '.') . "? Saldo akan bertambah Rp " . number_format($transaction->amount, 0, ',', '.') . ".";
                                            } elseif ($transaction->type === 'transfer') {
                                                $confirmMsg = "Hapus transfer keluar Rp " . number_format($transaction->amount, 0, ',', '.') . "? Saldo kartu ini akan bertambah dan transaksi pasangan di kartu tujuan juga akan dihapus.";
                                            } elseif ($transaction->type === 'transfer_receive') {
                                                $confirmMsg = "Hapus transfer masuk Rp " . number_format($transaction->amount, 0, ',', '.') . "? Saldo kartu ini akan berkurang dan transaksi pasangan di kartu sumber juga akan dihapus.";
                                            } else {
                                                $confirmMsg = "Hapus pembayaran Rp " . number_format($transaction->amount, 0, ',', '.') . "? Saldo akan bertambah Rp " . number_format($transaction->amount, 0, ',', '.') . ".";
                                            }
                                            $btnDeleteClass = $transaction->type === 'topup' ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 
                                                ($transaction->type === 'withdraw' ? 'bg-amber-100 text-amber-700 hover:bg-amber-200' : 
                                                ($transaction->type === 'transfer_receive' ? 'bg-cyan-100 text-cyan-700 hover:bg-cyan-200' : 
                                                ($transaction->type === 'transfer' ? 'bg-orange-100 text-orange-700 hover:bg-orange-200' : 'bg-rose-100 text-rose-700 hover:bg-rose-200')));
                                        @endphp
                                        <button type="submit" onclick="return confirm('{{ $confirmMsg }}')" class="inline-flex items-center gap-1 px-3 py-2 {{ $btnDeleteClass }} rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                                            <span class="material-symbols-outlined text-xs">delete</span>
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">Belum ada transaksi untuk ditampilkan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($showAll && $isPaginator)
            <div class="px-6">
                {{ $transactions->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection