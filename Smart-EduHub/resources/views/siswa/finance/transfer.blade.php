@extends('layouts.siswa')

@section('title', 'Transfer Saldo')
@section('page_title', 'Transfer Saldo')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="px-1">
        <a href="{{ route('siswa.finance') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 border border-slate-100 rounded-full text-xs font-black uppercase tracking-wider text-slate-500 hover:text-brand-600 transition-all active:scale-95 shadow-sm mb-4">
            <span class="material-symbols-outlined text-sm font-bold">arrow_back</span>
            <span>Kembali</span>
        </a>
        
        <h1 class="text-2xl font-black text-slate-900 tracking-tight mb-2">Transfer Saldo</h1>
        <p class="text-sm text-slate-500">Kirim saldo dompet digital ke sesama siswa dengan mudah dan cepat.</p>
    </div>

    @if(session('error'))
    <div class="mx-1 bg-rose-50 border border-rose-100 text-rose-800 p-4 rounded-2xl flex items-center gap-3 shadow-sm">
        <span class="material-symbols-outlined text-rose-600">error</span>
        <span class="text-xs font-black tracking-tight">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-[2rem] border border-slate-100 p-6 lg:p-8 shadow-sm space-y-8">
        <div class="bg-brand-50 border border-brand-100 p-5 rounded-2xl">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-brand-600 text-2xl">info</span>
                <div>
                    <h4 class="text-xs font-black text-brand-900 tracking-tight mb-1">Informasi Transfer</h4>
                    <ul class="text-[11px] text-brand-800 font-medium space-y-1">
                        <li>• Minimum transfer Rp 1.000</li>
                        <li>• Pastikan nomor kartu tujuan sudah benar</li>
                        <li>• Transfer tidak dapat dibatalkan setelah diproses</li>
                    </ul>
                </div>
            </div>
        </div>

        <form action="{{ route('siswa.finance.transfer.confirm') }}" method="POST" class="space-y-5">
            @csrf
            
            <div class="space-y-2">
                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Pilih Kartu Sumber</label>
                <select name="source_card_id" required class="w-full px-4 py-4 bg-slate-50 border border-slate-100 rounded-xl text-sm font-bold focus:outline-none focus:ring-4 focus:ring-brand-500/20 focus:border-[#F6863B] transition-all">
                    @foreach($cards as $card)
                        <option value="{{ $card->id }}" {{ old('source_card_id') == $card->id ? 'selected' : '' }}>
                            {{ $card->card_number }} - Saldo: Rp {{ number_format($card->balance, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
                @error('source_card_id')
                    <p class="text-xs text-rose-500 font-bold ml-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Nomor Kartu Tujuan</label>
                <input type="text" name="target_card_number" value="{{ old('target_card_number') }}" required class="w-full px-4 py-4 bg-slate-50 border border-slate-100 rounded-xl text-sm font-bold focus:outline-none focus:ring-4 focus:ring-brand-500/20 focus:border-[#F6863B] transition-all" placeholder="Masukkan 10 digit nomor kartu tujuan">
                @error('target_card_number')
                    <p class="text-xs text-rose-500 font-bold ml-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Nominal Transfer (Rp)</label>
                <input type="number" name="amount" min="1000" value="{{ old('amount') }}" required class="w-full px-4 py-4 bg-slate-50 border border-slate-100 rounded-xl text-sm font-bold focus:outline-none focus:ring-4 focus:ring-brand-500/20 focus:border-[#F6863B] transition-all" placeholder="Contoh: 50000">
                @error('amount')
                    <p class="text-xs text-rose-500 font-bold ml-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Pesan / Keterangan (Opsional)</label>
                <input type="text" name="description" value="{{ old('description') }}" class="w-full px-4 py-4 bg-slate-50 border border-slate-100 rounded-xl text-sm font-bold focus:outline-none focus:ring-4 focus:ring-brand-500/20 focus:border-[#F6863B] transition-all" placeholder="Contoh: Bayar kas kelas">
                @error('description')
                    <p class="text-xs text-rose-500 font-bold ml-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-[#F6863B] hover:bg-[#EB4325] text-white font-black py-4 rounded-xl shadow-lg shadow-brand-200/50 transition-all active:scale-95 uppercase tracking-widest text-sm mt-4">
                Lanjut ke PIN
            </button>
        </form>
    </div>
</div>
@endsection
