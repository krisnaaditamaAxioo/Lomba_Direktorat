@extends('layouts.app')

@section('title', 'Kirim Notifikasi Baru')

@section('content')
<!-- Header Halaman -->
<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6 md:mb-8">
    <div>
        <h2 class="font-display-xl text-display-xl text-on-surface">Kirim Notifikasi Baru</h2>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Kirim notifikasi khusus ke satu atau lebih siswa.</p>
    </div>
    <a href="{{ route('admin.notifications.index') }}" class="flex-1 sm:flex-none justify-center bg-white border border-slate-200 text-slate-700 px-4 sm:px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-slate-50 active:scale-95 transition-all flex items-center gap-2">
        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        Kembali
    </a>
</div>

<!-- Form Kirim Notifikasi -->
<div class="max-w-3xl bg-white rounded-2xl border border-slate-100 p-6 md:p-8">
    <form method="POST" action="{{ route('admin.notifications.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="space-y-6">
            <!-- Judul -->
            <div>
                <label for="title" class="block text-sm font-bold text-slate-700 mb-2">Judul Notifikasi</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all" required placeholder="Masukkan judul notifikasi...">
            </div>

            <!-- Pesan -->
            <div>
                <label for="message" class="block text-sm font-bold text-slate-700 mb-2">Pesan Notifikasi</label>
                <textarea id="message" name="message" rows="5" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all" required placeholder="Tulis pesan notifikasi...">{{ old('message') }}</textarea>
            </div>

            <!-- Lampiran PDF -->
            <div>
                <label for="attachment" class="block text-sm font-bold text-slate-700 mb-2">Lampiran PDF (opsional)</label>
                <input id="attachment" name="attachment" type="file" accept="application/pdf" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100" />
                @error('attachment')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pilih Siswa -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Pilih Siswa</label>
                <div class="flex gap-2 mb-3">
                    <button type="button" onclick="selectAllStudents()" class="px-4 py-2 bg-orange-100 text-orange-700 rounded-lg text-sm font-bold hover:bg-orange-200 transition-colors">Pilih Semua</button>
                    <button type="button" onclick="deselectAllStudents()" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-bold hover:bg-slate-200 transition-colors">Batal Pilih Semua</button>
                </div>
                <div class="max-h-64 overflow-y-auto border border-slate-200 rounded-xl p-4 space-y-2">
                    @foreach($students as $student)
                        <div class="flex items-center gap-3">
                            <input type="checkbox" id="student_{{ $student->id }}" name="student_ids[]" value="{{ $student->id }}" class="w-4 h-4 text-orange-600 border-slate-300 rounded focus:ring-orange-500 student-checkbox" {{ in_array($student->id, old('student_ids', [])) ? 'checked' : '' }}>
                            <label for="student_{{ $student->id }}" class="text-sm text-slate-700">{{ $student->nama_lengkap }} <span class="text-slate-400">({{ $student->nis_siswa }})</span></label>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Tombol -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.notifications.index') }}" class="px-6 py-2.5 rounded-xl font-bold text-sm text-slate-500 hover:text-slate-700 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-xl font-bold text-sm shadow-lg shadow-orange-100 hover:bg-orange-700 active:scale-95 transition-all">Kirim Notifikasi</button>
            </div>
        </div>
    </form>
</div>

<script>
function selectAllStudents() {
    document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = true);
}

function deselectAllStudents() {
    document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
}
</script>
@endsection
