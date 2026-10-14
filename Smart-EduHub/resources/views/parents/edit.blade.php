@extends('layouts.app')

@section('title', 'Edit Akun Orang Tua')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <nav class="flex items-center space-x-2 text-outline mb-2">
            <a href="{{ route('parents.index') }}" class="font-label-sm text-label-sm hover:text-primary transition-colors">Data Orang Tua</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="font-label-sm text-label-sm text-primary">Edit Akun</span>
        </nav>
        <h2 class="font-display-xl text-display-xl text-on-surface">Edit Akun Orang Tua</h2>
    </div>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-3xl custom-shadow border border-slate-100 overflow-hidden">
        <form action="{{ route('parents.update', $parent) }}" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-bold text-slate-700">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $parent->name) }}" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('name') border-error @enderror" required>
                    @error('name') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-bold text-slate-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $parent->email) }}" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('email') border-error @enderror" required>
                        @error('email') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="phone" class="block text-sm font-bold text-slate-700">No. HP</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $parent->phone) }}" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('phone') border-error @enderror" required>
                        @error('phone') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="gender" class="block text-sm font-bold text-slate-700">Jenis Kelamin</label>
                        <select name="gender" id="gender" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('gender') border-error @enderror" required>
                            <option value="L" {{ old('gender', $parent->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender', $parent->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="student_id" class="block text-sm font-bold text-slate-700">Siswa</label>
                        <select name="student_id" id="student_id" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('student_id') border-error @enderror" required>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id', $parent->student_id) == $student->id ? 'selected' : '' }}>{{ $student->nama_lengkap }} ({{ $student->id_siswa ?? '-' }})</option>
                            @endforeach
                        </select>
                        @error('student_id') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-bold text-slate-700">Password Baru</label>
                        <input type="password" name="password" id="password" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('password') border-error @enderror" placeholder="Kosongkan jika tidak diubah">
                        @error('password') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="password_confirmation" class="block text-sm font-bold text-slate-700">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="Konfirmasi password baru">
                    </div>
                </div>
            </div>

            <div class="pt-6 flex items-center justify-end space-x-3 border-t border-slate-50">
                <a href="{{ route('parents.index') }}" class="px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">Batal</a>
                <button type="submit" class="bg-primary text-white font-bold text-sm px-10 py-3 rounded-xl custom-shadow hover:translate-y-[-1px] active:scale-95 transition-all shadow-orange-200/50">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
