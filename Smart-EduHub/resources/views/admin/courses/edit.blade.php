@extends('layouts.app')

@section('content')
<div class="space-y-8">
    @include('admin.partials.page-header', [
        'title' => 'Edit Kursus',
        'eyebrow' => 'E-Learning',
        'subtitle' => 'Ubah detail kursus ' . $course->title,
        'icon' => 'edit',
    ])

    <div class="flex justify-center">
        <a href="{{ route('admin.courses.index') }}" class="p-3 bg-slate-100 text-slate-700 rounded-2xl hover:bg-slate-200 transition-all flex items-center gap-2 font-bold text-sm">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali
        </a>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3">
        <span class="material-symbols-outlined">check_circle</span>
        <span class="text-sm font-bold">{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-rose-50 border border-rose-100 text-rose-700 px-6 py-4 rounded-2xl space-y-1">
        <div class="flex items-center gap-3 mb-2">
            <span class="material-symbols-outlined">error</span>
            <span class="text-sm font-bold">Terjadi Kesalahan:</span>
        </div>
        <ul class="list-disc list-inside text-xs font-medium ml-8">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 p-8 max-w-2xl">
        <form action="{{ route('admin.courses.update', $course) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Judul Kursus</label>
                <input type="text" name="title" required value="{{ old('title', $course->title) }}" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Deskripsi</label>
                <textarea name="description" rows="4" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">{{ old('description', $course->description) }}</textarea>
            </div>

            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Angkatan</label>
                <select name="angkatan_id" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                    <option value="">Semua Angkatan</option>
                    @foreach($angkatans as $angkatan)
                        <option value="{{ $angkatan->id }}" {{ old('angkatan_id', $course->angkatan_id) == $angkatan->id ? 'selected' : '' }}>{{ $angkatan->name }}</option>
                    @endforeach
                </select>
            </div>

            @if($course->image)
            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Gambar Saat Ini</label>
                <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="w-40 h-40 object-cover rounded-2xl border border-slate-100">
            </div>
            @endif

            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Ganti Gambar Sampul (Opsional)</label>
                <input type="file" name="image" class="w-full text-xs text-slate-500 file:mr-4 file:py-3 file:px-5 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100 transition-all">
            </div>

            <div class="flex gap-4 pt-4">
                <a href="{{ route('admin.courses.index') }}" class="flex-1 py-4 bg-slate-100 text-slate-700 rounded-2xl font-black shadow-xl shadow-slate-100 hover:bg-slate-200 transition-all uppercase tracking-widest text-sm text-center">Batal</a>
                <button type="submit" class="flex-1 py-4 bg-orange-600 text-white rounded-2xl font-black shadow-xl shadow-orange-100 hover:bg-orange-700 transition-all uppercase tracking-widest text-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
