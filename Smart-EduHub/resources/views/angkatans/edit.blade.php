@extends('layouts.app')

@section('title', 'Edit Angkatan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <h2 class="text-3xl font-black text-slate-900">Edit Angkatan</h2>
        <p class="text-slate-500 mt-1">Perbarui data master angkatan.</p>
    </div>

    <form action="{{ route('angkatans.update', $angkatan) }}" method="POST" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Kode Angkatan</label>
                <input type="text" name="code" value="{{ old('code', $angkatan->code) }}" class="w-full rounded-xl border-slate-200 bg-slate-50" required>
                @error('code') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Nama Angkatan</label>
                <input type="text" name="name" value="{{ old('name', $angkatan->name) }}" class="w-full rounded-xl border-slate-200 bg-slate-50" required>
                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Tahun Masuk</label>
                <input type="number" name="tahun_masuk" value="{{ old('tahun_masuk', $angkatan->tahun_masuk) }}" min="1900" max="2100" class="w-full rounded-xl border-slate-200 bg-slate-50">
                @error('tahun_masuk') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Urutan</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $angkatan->sort_order) }}" min="1" class="w-full rounded-xl border-slate-200 bg-slate-50" required>
                @error('sort_order') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Wali Kelas</label>
                <select name="wali_kelas_id" class="w-full rounded-xl border-slate-200 bg-slate-50">
                    <option value="">Pilih Wali Kelas</option>
                    @foreach($waliKelasList as $waliKelas)
                        <option value="{{ $waliKelas->id }}" {{ old('wali_kelas_id', $angkatan->wali_kelas_id) == $waliKelas->id ? 'selected' : '' }}>{{ $waliKelas->name }}</option>
                    @endforeach
                </select>
                @error('wali_kelas_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-center gap-3 pt-8">
                <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $angkatan->is_active)) class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                <label for="is_active" class="text-sm font-bold text-slate-700">Aktif</label>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('angkatans.index') }}" class="px-6 py-3 rounded-xl border border-slate-200 text-slate-600 font-bold">Batal</a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-orange-600 text-white font-bold">Perbarui Angkatan</button>
        </div>
    </form>
</div>
@endsection
