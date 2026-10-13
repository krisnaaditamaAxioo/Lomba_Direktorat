@extends('layouts.app')

@section('title', 'Catat Poin Perilaku')

@section('content')
<div class="mb-6 md:mb-8">
    <nav class="flex items-center space-x-2 text-outline mb-2">
        <a href="{{ route('points') }}" class="font-label-sm text-label-sm hover:text-primary transition-colors">Penghargaan & Disiplin</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="font-label-sm text-label-sm text-primary">Catat Poin</span>
    </nav>
    <h2 class="font-display-xl text-display-xl text-on-surface">Tambah Catatan Poin</h2>
    <p class="font-body-md text-body-md text-on-surface-variant mt-1">Catat penghargaan atau pelanggaran seorang siswa.</p>
</div>

<div class="max-w-3xl">
    <form action="{{ route('points.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 md:space-y-6">
        @csrf
        
        <div class="bg-white p-5 md:p-8 rounded-2xl md:rounded-3xl custom-shadow border border-slate-100 space-y-5 md:space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">
                <!-- Pilih Siswa -->
                <div class="space-y-2 md:col-span-2">
                    <label for="student_id" class="block text-sm font-bold text-slate-700">Siswa</label>
                    <select name="student_id" id="student_id" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('student_id') border-error @enderror" required>
                        <option value="">Pilih Siswa</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->nama_lengkap }} ({{ $student->id_siswa }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Jenis Poin -->
                <div class="space-y-2">
                    <label for="type" class="block text-sm font-bold text-slate-700">Jenis Poin</label>
                    <select name="type" id="type" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('type') border-error @enderror" required>
                        <option value="achievement" {{ old('type') == 'achievement' ? 'selected' : '' }}>Penghargaan</option>
                        <option value="violation" {{ old('type') == 'violation' ? 'selected' : '' }}>Pelanggaran</option>
                    </select>
                    @error('type') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Poin -->
                <div class="space-y-2">
                    <label for="score" class="block text-sm font-bold text-slate-700">Poin</label>
                    <input type="number" name="score" id="score" value="{{ old('score', 10) }}" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('score') border-error @enderror" required>
                    @error('score') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Tanggal -->
                <div class="space-y-2">
                    <label for="date" class="block text-sm font-bold text-slate-700">Tanggal</label>
                    <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('date') border-error @enderror" required>
                    @error('date') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Foto Bukti -->
                <div class="space-y-2">
                    <label for="proof_photo" class="block text-sm font-bold text-slate-700">Foto Bukti (Opsional)</label>
                    <input type="file" name="proof_photo" id="proof_photo" accept="image/*" class="w-full bg-slate-50 border-slate-200 rounded-xl py-2 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('proof_photo') border-error @enderror">
                    <p class="text-[10px] text-slate-500 italic mt-1">Gambar akan dikompres otomatis untuk menghemat ruang.</p>
                    @error('proof_photo') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Alasan -->
                <div class="space-y-2 md:col-span-2">
                    <label for="reason" class="block text-sm font-bold text-slate-700">Alasan / Keterangan</label>
                    <textarea name="reason" id="reason" rows="3" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('reason') border-error @enderror" required>{{ old('reason') }}</textarea>
                    @error('reason') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
            <a href="{{ route('points') }}" class="w-full sm:w-auto text-center px-6 py-3 rounded-xl font-label-md text-label-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 active:scale-[0.98] transition-all">Batal</a>
            <button type="submit" class="w-full sm:w-auto px-6 py-3 rounded-xl font-label-md text-label-md text-white bg-primary hover:bg-orange-700 active:scale-[0.98] custom-shadow transition-all">Simpan Catatan</button>
        </div>
    </form>
</div>
@endsection