@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black text-slate-900">Tambah Peraturan Poin</h2>
            <p class="text-slate-500 mt-1">Tambahkan peraturan poin baru.</p>
        </div>
        <a href="{{ route('point-rules.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600">Kembali</a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
        <form method="POST" action="{{ route('point-rules.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-1">Nama Peraturan</label>
                    <input type="text" name="name" required placeholder="Misal: Terlambat 5x" class="w-full rounded-xl border border-slate-200 p-3">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Status Kehadiran</label>
                    <select name="attendance_status" class="w-full rounded-xl border border-slate-200 p-3" required>
                        <option value="late">Terlambat</option>
                        <option value="absent">Alpa</option>
                        <option value="present">Hadir</option>
                        <option value="excused">Izin</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Batas (Jumlah)</label>
                    <input type="number" name="threshold" required min="1" placeholder="Misal: 5" class="w-full rounded-xl border border-slate-200 p-3">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Tipe Poin</label>
                    <select name="point_type" class="w-full rounded-xl border border-slate-200 p-3" required>
                        <option value="achievement">Prestasi (+)</option>
                        <option value="violation">Pelanggaran (-)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Jumlah Poin</label>
                    <input type="number" name="point_score" required min="1" placeholder="Misal: 5" class="w-full rounded-xl border border-slate-200 p-3">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Urutan Sortir</label>
                    <input type="number" name="sort_order" value="0" class="w-full rounded-xl border border-slate-200 p-3">
                </div>

                <div class="col-span-2 flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="createIsActive" checked class="w-5 h-5 rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                    <label for="createIsActive" class="text-sm font-medium text-slate-700">Aktifkan peraturan ini</label>
                </div>

                <div class="col-span-2 flex justify-end gap-3 pt-4">
                    <a href="{{ route('point-rules.index') }}" class="px-4 py-2 rounded-xl border border-slate-200">Batal</a>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-orange-600 text-white font-bold">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
