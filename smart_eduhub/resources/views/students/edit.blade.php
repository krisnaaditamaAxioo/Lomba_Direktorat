@extends('layouts.app')

@section('title', 'Edit Siswa')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div>
        <h2 class="text-3xl font-black text-slate-900">Edit Siswa</h2>
        <p class="text-slate-500 mt-1">Perbarui data siswa dan pilih angkatan dari master data.</p>
    </div>

    <form action="{{ route('students.update', $student) }}" method="POST" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 space-y-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">ID Siswa</label>
                <input type="text" name="id_siswa" value="{{ old('id_siswa', $student->id_siswa) }}" class="w-full rounded-xl border-slate-200 bg-slate-50" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Username</label>
                <input type="text" name="username" value="{{ old('username', $student->username) }}" class="w-full rounded-xl border-slate-200 bg-slate-50" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">PIN Baru (6 Digit)</label>
                <input type="password" name="pin" maxlength="6" placeholder="Kosongkan jika tidak diubah" class="w-full rounded-xl border-slate-200 bg-slate-50">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $student->nama_lengkap) }}" class="w-full rounded-xl border-slate-200 bg-slate-50" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $student->email) }}" class="w-full rounded-xl border-slate-200 bg-slate-50" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Status</label>
                <select name="status" class="w-full rounded-xl border-slate-200 bg-slate-50" required>
                    <option value="0" @selected(old('status', $student->status) === 0 || old('status', $student->status) === '0')>Aktif</option>
                    <option value="1" @selected(old('status', $student->status) === 1 || old('status', $student->status) === '1')>Tidak Aktif</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Angkatan</label>
                <select name="angkatan_id" class="w-full rounded-xl border-slate-200 bg-slate-50" required>
                    <option value="">Pilih Angkatan</option>
                    @foreach($angkatans as $angkatan)
                        <option value="{{ $angkatan->id }}" @selected(old('angkatan_id', $student->angkatan_id) == $angkatan->id)>{{ $angkatan->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Jenis Kelamin</label>
                <select name="gender" class="w-full rounded-xl border-slate-200 bg-slate-50">
                    <option value="">Pilih</option>
                    <option value="0" @selected(old('gender', $student->gender) === 0 || old('gender', $student->gender) === '0')>Laki-laki</option>
                    <option value="1" @selected(old('gender', $student->gender) === 1 || old('gender', $student->gender) === '1')>Perempuan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">NISN</label>
                <input type="text" name="nisn" value="{{ old('nisn', $student->nisn) }}" class="w-full rounded-xl border-slate-200 bg-slate-50">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">NIS Siswa</label>
                <input type="text" name="nis_siswa" value="{{ old('nis_siswa', $student->nis_siswa) }}" class="w-full rounded-xl border-slate-200 bg-slate-50">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">NIK</label>
                <input type="text" name="no_nik" value="{{ old('no_nik', $student->no_nik) }}" class="w-full rounded-xl border-slate-200 bg-slate-50">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">No. HP Siswa</label>
                <input type="text" name="phone" value="{{ old('phone', $student->phone) }}" class="w-full rounded-xl border-slate-200 bg-slate-50">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">No. HP Orang Tua</label>
                <input type="text" name="parent_phone" value="{{ old('parent_phone', $student->parent_phone) }}" class="w-full rounded-xl border-slate-200 bg-slate-50">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Tempat Lahir</label>
                <input type="text" name="place_of_birth" value="{{ old('place_of_birth', $student->place_of_birth) }}" class="w-full rounded-xl border-slate-200 bg-slate-50">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Lahir</label>
                <input type="date" name="dateofbirth" value="{{ old('dateofbirth', $student->dateofbirth) }}" class="w-full rounded-xl border-slate-200 bg-slate-50">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Agama</label>
                <input type="text" name="religion" value="{{ old('religion', $student->religion) }}" class="w-full rounded-xl border-slate-200 bg-slate-50">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Nama Ibu</label>
                <input type="text" name="mother_name" value="{{ old('mother_name', $student->mother_name) }}" class="w-full rounded-xl border-slate-200 bg-slate-50">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Tinggi</label>
                <input type="number" name="height" value="{{ old('height', $student->height) }}" class="w-full rounded-xl border-slate-200 bg-slate-50">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Berat</label>
                <input type="number" name="weight" value="{{ old('weight', $student->weight) }}" class="w-full rounded-xl border-slate-200 bg-slate-50">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Alamat</label>
            <textarea name="address" rows="4" class="w-full rounded-xl border-slate-200 bg-slate-50">{{ old('address', $student->address) }}</textarea>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('students') }}" class="px-6 py-3 rounded-xl border border-slate-200 text-slate-600 font-bold">Batal</a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-orange-600 text-white font-bold">Perbarui</button>
        </div>
    </form>
</div>
@endsection
