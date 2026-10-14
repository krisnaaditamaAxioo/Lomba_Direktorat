@extends('layouts.siswa')

@section('title', 'Konfirmasi PIN')
@section('page_title', 'Konfirmasi PIN')

@section('content')
<div class="max-w-md mx-auto space-y-6 mt-10">
    <div class="px-1 text-center">
        <a href="{{ route('siswa.finance.transfer') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 border border-slate-100 rounded-full text-xs font-black uppercase tracking-wider text-slate-500 hover:text-brand-600 transition-all active:scale-95 shadow-sm mb-6">
            <span class="material-symbols-outlined text-sm font-bold">arrow_back</span>
            <span>Batal & Kembali</span>
        </a>
        
        <h1 class="text-2xl font-black text-slate-900 tracking-tight mb-2">Konfirmasi PIN</h1>
        <p class="text-sm text-slate-500">Masukkan PIN Anda untuk mengkonfirmasi transfer ini.</p>
    </div>

    @if(session('error'))
    <div class="mx-1 bg-rose-50 border border-rose-100 text-rose-800 p-4 rounded-2xl flex items-center justify-center gap-3 shadow-sm">
        <span class="material-symbols-outlined text-rose-600">error</span>
        <span class="text-xs font-black tracking-tight">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-[2rem] border border-slate-100 p-6 shadow-xl shadow-brand-100/50 space-y-6">
        
        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Tujuan</span>
                <span class="text-sm text-slate-900 font-black">{{ $transferData['target_name'] }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">No. Kartu</span>
                <span class="text-sm text-slate-900 font-black">{{ $transferData['target_card_number'] }}</span>
            </div>
            <hr class="border-slate-200">
            <div class="flex justify-between items-center">
                <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Nominal</span>
                <span class="text-lg text-brand-600 font-black">Rp {{ number_format($transferData['amount'], 0, ',', '.') }}</span>
            </div>
        </div>

        <form action="{{ route('siswa.finance.transfer.process') }}" method="POST" class="space-y-5 text-center">
            @csrf
            
            <div class="space-y-3">
                <input type="password" name="pin" required maxlength="6" autofocus
                    class="w-full text-center tracking-[1em] text-3xl px-4 py-4 bg-slate-50 border border-slate-200 rounded-xl font-black focus:outline-none focus:ring-4 focus:ring-brand-500/20 focus:border-[#F6863B] transition-all" 
                    placeholder="••••••">
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Masukkan 6 Digit PIN</p>
                @error('pin')
                    <p class="text-xs text-rose-500 font-bold">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-[#F6863B] hover:bg-[#EB4325] text-white font-black py-4 rounded-xl shadow-lg shadow-brand-200/50 transition-all active:scale-95 uppercase tracking-widest text-sm mt-4">
                Konfirmasi & Kirim
            </button>
        </form>
    </div>
</div>
@endsection
