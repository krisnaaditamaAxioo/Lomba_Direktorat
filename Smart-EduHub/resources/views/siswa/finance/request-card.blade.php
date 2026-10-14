@extends('layouts.siswa')

@section('title', 'Ajukan Kartu')
@section('page_title', 'Ajukan Kartu')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="px-1">
        <a href="{{ route('siswa.finance') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 border border-slate-100 rounded-full text-xs font-black uppercase tracking-wider text-slate-500 hover:text-brand-600 transition-all active:scale-95 shadow-sm mb-4">
            <span class="material-symbols-outlined text-sm font-bold">arrow_back</span>
            <span>Kembali</span>
        </a>
        
        <h1 class="text-2xl font-black text-slate-900 tracking-tight mb-2">Ajukan Kartu Baru</h1>
        <p class="text-sm text-slate-500">Lengkapi form berikut untuk mengajukan kartu dompet digital</p>
    </div>

    <div class="bg-white rounded-[2rem] border border-slate-100 p-6 lg:p-8 shadow-sm space-y-8">
        <div class="bg-amber-50 border border-amber-100 p-5 rounded-2xl">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-amber-600 text-2xl">info</span>
                <div>
                    <h4 class="text-xs font-black text-amber-900 tracking-tight mb-1">Persyaratan Pengajuan</h4>
                    <ul class="text-[11px] text-amber-800 font-medium space-y-1">
                        <li>• Surat pernyataan yang ditandatangani oleh orang tua/wali</li>
                        <li>• Scan KTP orang tua/wali (opsional)</li>
                    </ul>
                </div>
            </div>
        </div>

        <form action="{{ route('siswa.finance.request-card.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div class="space-y-2">
                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Orang Tua/Wali</label>
                <input type="text" name="parent_name" value="{{ old('parent_name') }}" required class="w-full px-4 py-4 bg-slate-50 border border-slate-100 rounded-xl text-sm font-bold focus:outline-none focus:ring-4 focus:ring-brand-500/20 focus:border-[#F6863B] transition-all" placeholder="Masukkan nama orang tua/wali">
            </div>

            <div class="space-y-2">
                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Nomor WhatsApp Orang Tua/Wali</label>
                <input type="text" name="parent_phone" value="{{ old('parent_phone', $student->parent_phone) }}" required class="w-full px-4 py-4 bg-slate-50 border border-slate-100 rounded-xl text-sm font-bold focus:outline-none focus:ring-4 focus:ring-brand-500/20 focus:border-[#F6863B] transition-all" placeholder="08xxxxxxxxxx">
            </div>

            <div class="space-y-2">
                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Surat Pernyataan</label>
                <div class="relative">
                    <input type="file" name="statement_file" id="statementFile" accept=".pdf,.jpg,.jpeg,.png" required class="hidden" onchange="document.getElementById('fileName').textContent = this.files[0]?.name || 'Klik untuk unggah surat pernyataan'">
                    <label for="statementFile" class="flex flex-col items-center justify-center p-8 bg-slate-50 border-2 border-dashed border-slate-200 hover:border-brand-300 rounded-xl cursor-pointer hover:bg-brand-50/50 transition-all group">
                        <span class="material-symbols-outlined text-4xl text-slate-400 group-hover:text-[#F6863B] mb-2">upload_file</span>
                        <p id="fileName" class="text-xs font-black text-slate-500 group-hover:text-[#F6863B] text-center">Klik untuk unggah surat pernyataan</p>
                        <p class="text-[10px] text-slate-400 mt-1">Max 5 MB (PDF, JPG, PNG)</p>
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full bg-[#F6863B] hover:bg-[#EB4325] text-white font-black py-4 rounded-xl shadow-lg shadow-brand-200/50 transition-all active:scale-95 uppercase tracking-widest text-sm">
                Kirim Pengajuan
            </button>
        </form>
    </div>
</div>
@endsection
