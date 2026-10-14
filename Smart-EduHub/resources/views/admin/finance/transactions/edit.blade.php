@extends('layouts.app')

@section('content')
<div class="space-y-8">
    @include('admin.partials.page-header', [
        'title' => 'Edit Transaksi',
        'eyebrow' => 'Riwayat Transaksi',
        'subtitle' => 'Ubah deskripsi atau nilai transaksi terakhir.',
        'icon' => 'edit',
    ])

    <div class="flex justify-center">
        <a href="{{ route('admin.finance.transactions.index') }}" class="p-2 text-slate-500 hover:bg-slate-100 rounded-xl transition-all flex items-center gap-2 font-bold text-sm">
            <span class="material-symbols-outlined text-[24px]">arrow_back</span>
            Kembali
        </a>
    </div>

    @if(session('error'))
    <div class="bg-rose-50 border border-rose-100 text-rose-700 px-6 py-4 rounded-2xl flex items-center gap-3">
        <span class="material-symbols-outlined">error</span>
        <span class="text-sm font-bold">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 p-6 shadow-sm max-w-3xl">
        <form action="{{ route('admin.finance.transactions.update', $transaction) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Siswa</label>
                    <input type="text" value="{{ $transaction->card->student->nama_lengkap }}" disabled class="w-full mt-2 px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm text-slate-600" />
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tipe Transaksi</label>
                    <input type="text" value="{{ ucfirst($transaction->type) }}" disabled class="w-full mt-2 px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm text-slate-600" />
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nomor Kartu</label>
                    <input type="text" value="{{ $transaction->card->card_number }}" disabled class="w-full mt-2 px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm text-slate-600" />
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Petugas (Input Data)</label>
                    <input type="text" value="{{ $transaction->performedBy->name ?? 'Sistem/Siswa' }}" disabled class="w-full mt-2 px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm text-slate-600" />
                </div>
            </div>

            @if(in_array($transaction->type, ['topup', 'payment']))
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Jumlah</label>
                <input type="number" name="amount" value="{{ old('amount', $transaction->amount) }}" min="1000" required class="w-full mt-2 px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none" />
            </div>
            @endif

            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Deskripsi</label>
                <textarea name="description" rows="4" class="w-full mt-2 px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none">{{ old('description', $transaction->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Saldo Sebelum</label>
                    <input type="text" value="Rp {{ number_format($transaction->balance_before, 0, ',', '.') }}" disabled class="w-full mt-2 px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm text-slate-600" />
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Saldo Setelah</label>
                    <input type="text" value="Rp {{ number_format($transaction->balance_after, 0, ',', '.') }}" disabled class="w-full mt-2 px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm text-slate-600" />
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <button type="submit" class="w-full sm:w-auto py-3 px-6 bg-orange-600 text-white rounded-2xl font-black uppercase tracking-widest text-sm hover:bg-orange-700 transition-all">
                    <span class="inline-flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">save</span>
                        Simpan Perubahan
                    </span>
                </button>
                @if($transaction->id === optional($transaction->card->transactions()->latest('id')->first())->id)
                <form action="{{ route('admin.finance.transactions.destroy', $transaction) }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    @method('DELETE')
                    @php
                        $confirmMsg = $transaction->type === 'topup' 
                            ? "Hapus topup Rp " . number_format($transaction->amount, 0, ',', '.') . "? Saldo siswa akan berkurang " . number_format($transaction->amount, 0, ',', '.') . "."
                            : "Hapus pembayaran Rp " . number_format($transaction->amount, 0, ',', '.') . "? Saldo siswa akan bertambah " . number_format($transaction->amount, 0, ',', '.') . ".";
                    @endphp
                    <button type="submit" onclick="return confirm('{{ $confirmMsg }}')" class="w-full sm:w-auto py-3 px-6 {{ $transaction->type === 'topup' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700' }} text-white rounded-2xl font-black uppercase tracking-widest text-sm transition-all">
                        <span class="inline-flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">delete</span>
                            Hapus Transaksi
                        </span>
                    </button>
                </form>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
