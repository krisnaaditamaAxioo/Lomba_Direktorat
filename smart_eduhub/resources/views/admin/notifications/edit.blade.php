@extends('layouts.app')

@section('title', 'Edit Notifikasi')

@section('content')
<!-- Header Halaman -->
<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6 md:mb-8">
    <div>
        <h2 class="font-display-xl text-display-xl text-on-surface">Edit Notifikasi</h2>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Ubah judul dan pesan notifikasi yang sudah dikirim.</p>
    </div>
    <a href="{{ route('admin.notifications.index') }}" class="flex-1 sm:flex-none justify-center bg-white border border-slate-200 text-slate-700 px-4 sm:px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-slate-50 active:scale-95 transition-all flex items-center gap-2">
        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        Kembali
    </a>
</div>

<!-- Form Edit Notifikasi -->
<div class="max-w-3xl bg-white rounded-2xl border border-slate-100 p-6 md:p-8">
    <form method="POST" action="{{ route('admin.notifications.update', $notification) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <!-- Info Siswa -->
            <div class="p-4 bg-slate-50/50 rounded-xl border border-slate-100">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Ditujukan Kepada</p>
                <p class="text-sm font-semibold text-slate-700">{{ $notification->student?->nama_lengkap ?? 'Siswa tidak ditemukan' }} <span class="text-slate-400">({{ $notification->student?->nis_siswa ?? '' }})</span></p>
            </div>

            <!-- Judul -->
            <div>
                <label for="title" class="block text-sm font-bold text-slate-700 mb-2">Judul Notifikasi</label>
                <input type="text" id="title" name="title" value="{{ old('title', $notification->title) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all" required>
            </div>

            <!-- Pesan -->
            <div>
                <label for="message" class="block text-sm font-bold text-slate-700 mb-2">Pesan Notifikasi</label>
                <textarea id="message" name="message" rows="5" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all" required>{{ old('message', $notification->message) }}</textarea>
            </div>

            <!-- Lampiran PDF -->
            <div>
                <label for="attachment" class="block text-sm font-bold text-slate-700 mb-2">Lampiran PDF (opsional)</label>
                <input id="attachment" name="attachment" type="file" accept="application/pdf" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100" />
                @if(isset($notification->data['attachment']))
                    <p class="text-xs text-slate-500 mt-2">Lampiran saat ini: <a href="{{ \Illuminate\Support\Facades\Storage::url($notification->data['attachment']) }}" target="_blank" class="text-orange-600 hover:underline">Lihat PDF</a></p>
                @endif
                @error('attachment')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.notifications.index') }}" class="px-6 py-2.5 rounded-xl font-bold text-sm text-slate-500 hover:text-slate-700 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-xl font-bold text-sm shadow-lg shadow-orange-100 hover:bg-orange-700 active:scale-95 transition-all">Simpan Perubahan</button>
            </div>
        </div>
    </form>
</div>
@endsection
