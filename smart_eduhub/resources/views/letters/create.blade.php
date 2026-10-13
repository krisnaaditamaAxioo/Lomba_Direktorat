@extends('layouts.app')

@section('title', 'Buat Surat Resmi')

@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    <div>
        <a href="{{ route('letters.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-orange-600 mb-4">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Kembali ke Surat Resmi
        </a>
        <h2 class="text-2xl font-black text-slate-900">Buat Surat Resmi</h2>
        <p class="text-sm text-slate-500 mt-1">Surat yang dibuat akan dikirim sebagai notifikasi ke akun wali siswa.</p>
    </div>

    @if($errors->any())
    <div class="bg-rose-50 border border-rose-100 text-rose-700 px-6 py-4 rounded-2xl">
        <ul class="list-disc list-inside text-sm space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    <form action="{{ route('letters.store') }}" method="POST" class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6 md:p-8 space-y-6">
        @csrf
        <div class="space-y-2">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Siswa</label>
            <select name="student_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold">
                <option value="">Pilih siswa</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->nama_lengkap }} - {{ $student->nis_siswa ?: ($student->nisn ?: 'Tanpa NIS') }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Jenis Surat</label>
                <select name="type" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold">
                    <option value="binaan">Surat Binaan</option><option value="sp1">Surat Peringatan 1</option><option value="sp2">Surat Peringatan 2</option><option value="sp3">Surat Peringatan 3</option><option value="lainnya">Surat Resmi Lainnya</option>
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal Surat</label>
                <input type="date" name="issued_at" value="{{ old('issued_at', date('Y-m-d')) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="space-y-2"><label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nomor Referensi</label><input type="text" name="reference_number" value="{{ old('reference_number') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold"></div>
            <div class="space-y-2"><label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Poin</label><input type="number" name="points" min="0" value="{{ old('points', 0) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold"></div>
        </div>
        <div class="space-y-2"><label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Catatan</label><textarea name="result_note" rows="4" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-medium" placeholder="Isi catatan atau keterangan surat...">{{ old('result_note') }}</textarea></div>
        <div class="space-y-2"><label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</label><select name="status" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold"><option value="sent">Kirim ke Wali</option><option value="draft">Simpan sebagai Draft</option></select></div>
        <div class="flex justify-end gap-3"><a href="{{ route('letters.index') }}" class="px-5 py-3 bg-slate-100 text-slate-600 rounded-2xl text-sm font-bold">Batal</a><button class="px-5 py-3 bg-orange-600 text-white rounded-2xl text-sm font-bold">Simpan Surat</button></div>
    </form>
</div>
@endsection