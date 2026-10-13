@extends('layouts.app')

@section('title', 'Raport Industri')

@section('content')
<div class="pb-6">
    <p class="text-xs font-bold text-orange-600 uppercase tracking-wider mb-1">Operasional</p>
    <h2 class="text-2xl sm:text-3xl font-black text-slate-900">Raport Industri</h2>
    <p class="text-xs sm:text-sm text-slate-500 mt-1">Capaian Hasil Belajar Kelas Industri Axioo Class Program.</p>
</div>
<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
    
    @if(auth()->user()->isAdmin() || auth()->user()->isWaliKelas())
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('settings') }}" class="bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl flex items-center gap-2 border border-amber-200 transition-all active:scale-95">
            <span class="material-symbols-outlined text-[18px]">badge</span>
            <span>Pengaturan Trainer</span>
        </a>
        <button onclick="document.getElementById('printBulkModal').classList.remove('hidden')" class="bg-orange-50 hover:bg-orange-100 text-orange-700 font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl flex items-center gap-2 border border-orange-200 transition-all active:scale-95">
            <span class="material-symbols-outlined text-[18px]">print</span>
            <span>Cetak Per Angkatan</span>
        </button>
        <a href="{{ route('raport.download-template') }}" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl flex items-center gap-2 border border-emerald-200 transition-all active:scale-95">
            <span class="material-symbols-outlined text-[18px]">download</span>
            <span>Download Template</span>
        </a>
        <button onclick="document.getElementById('importModal').classList.remove('hidden')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl flex items-center gap-2 border border-slate-200 transition-all active:scale-95">
            <span class="material-symbols-outlined text-[18px]">upload_file</span>
            <span>Import Excel</span>
        </button>
        <a href="{{ route('raport.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs sm:text-sm px-5 py-2.5 rounded-xl flex items-center gap-2 shadow-md shadow-orange-200 transition-all active:scale-95">
            <span class="material-symbols-outlined text-[20px]">note_add</span>
            <span>Tambah Raport</span>
        </a>
    </div>
    @endif
</div>

{{-- Import Flash Messages & Error Logs --}}
@if(session('import_errors') && count(session('import_errors')) > 0)
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-6">
    <div class="flex items-center gap-2 text-amber-800 font-bold text-sm mb-2">
        <span class="material-symbols-outlined text-[20px]">warning</span>
        <span>Catatan / Error saat Import:</span>
    </div>
    <ul class="list-disc list-inside text-xs text-amber-700 space-y-1">
        @foreach(session('import_errors') as $msg)
            <li>{{ $msg }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Filters --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-5 mb-5">
    <form method="GET" action="{{ route('raport.index') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex flex-col gap-1">
            <label class="text-[10px] font-black uppercase tracking-wider text-slate-400">Angkatan</label>
            <select name="angkatan_id" class="rounded-xl border-slate-200 bg-slate-50 text-xs py-2 px-3 focus:ring-2 focus:ring-orange-500/20 min-w-[150px]">
                <option value="">Semua Angkatan</option>
                @foreach($angkatans as $a)
                    <option value="{{ $a->id }}" @selected(request('angkatan_id') == $a->id)>{{ $a->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-[10px] font-black uppercase tracking-wider text-slate-400">Tahun Pelajaran</label>
            <select name="tahun_pelajaran" class="rounded-xl border-slate-200 bg-slate-50 text-xs py-2 px-3 focus:ring-2 focus:ring-orange-500/20 min-w-[160px]">
                <option value="">Semua Tahun</option>
                @foreach($tahunList as $tp)
                    <option value="{{ $tp }}" @selected(request('tahun_pelajaran') === $tp)>{{ $tp }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col gap-1 flex-1">
            <label class="text-[10px] font-black uppercase tracking-wider text-slate-400">Cari Siswa</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NIS / NISN..." class="rounded-xl border-slate-200 bg-slate-50 text-xs py-2 px-3 focus:ring-2 focus:ring-orange-500/20 min-w-[160px]">
        </div>
        <div class="flex flex-wrap gap-2 pb-[1px]">
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition-all">Filter</button>
            <a href="{{ route('raport.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-all">Reset</a>
            @if(request('angkatan_id'))
            <a href="{{ route('raport.print-bulk', ['angkatan_id' => request('angkatan_id'), 'tahun_pelajaran' => request('tahun_pelajaran')]) }}" target="_blank" class="px-4 py-2 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 font-bold text-xs border border-orange-200 transition-all flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">print</span>
                <span>Cetak Angkatan Ini</span>
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[700px]">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-5 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Siswa</th>
                    <th class="px-5 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Angkatan</th>
                    <th class="px-5 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Tahun Pelajaran</th>
                    <th class="px-5 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Wali Kelas</th>
                    <th class="px-5 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Trainer</th>
                    <th class="px-5 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($rapports as $r)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-5 py-4">
                        <p class="font-bold text-slate-900 text-sm group-hover:text-orange-600 transition-colors">{{ $r->student?->nama_lengkap ?? '-' }}</p>
                        <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wide mt-0.5">NIS: {{ $r->student?->nis_siswa ?? $r->student?->id_siswa ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-4">
                        <span class="text-xs font-bold bg-orange-50 text-orange-700 px-2.5 py-1 rounded-full">{{ $r->angkatan?->name ?? '-' }}</span>
                    </td>
                    <td class="px-5 py-4 text-sm text-slate-600 font-semibold">{{ $r->tahun_pelajaran }}</td>
                    <td class="px-5 py-4">
                        <p class="text-sm text-slate-700 font-semibold">{{ $r->waliKelas?->name ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-4">
                        <p class="text-sm text-slate-700 font-semibold">{{ $r->trainer_name ?? '-' }}</p>
                        @if($r->trainer_no_serti)
                            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">No. Serti: {{ $r->trainer_no_serti }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('raport.print', $r) }}" target="_blank"
                               class="p-2 rounded-lg border border-emerald-200 text-emerald-600 hover:bg-emerald-50 transition-all" title="Cetak Raport Siswa Ini">
                                <span class="material-symbols-outlined text-[18px]">print</span>
                            </a>
                            @if(auth()->user()->isAdmin() || auth()->user()->isWaliKelas())
                            <a href="{{ route('raport.edit', $r) }}"
                               class="p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition-all" title="Edit">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </a>
                            <form action="{{ route('raport.destroy', $r) }}" method="POST" onsubmit="return confirm('Hapus raport siswa ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg border border-rose-200 text-rose-500 hover:bg-rose-50 transition-all" title="Hapus">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                        <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">assignment_late</span>
                        <p class="font-bold text-sm">Belum ada data raport industri</p>
                        <p class="text-xs text-slate-400 mt-1">Gunakan tombol 'Tambah Raport' atau 'Import Excel' untuk menambahkan data.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($rapports->hasPages())
    <div class="px-5 py-4 border-t border-slate-100">
        {{ $rapports->links() }}
    </div>
    @endif
</div>

{{-- Modal Cetak Raport Per Angkatan --}}
<div id="printBulkModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl relative border border-slate-100">
        <button onclick="document.getElementById('printBulkModal').classList.add('hidden')" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600">
            <span class="material-symbols-outlined">close</span>
        </button>

        <div class="flex items-center gap-3 mb-4">
            <div class="p-3 bg-orange-50 text-orange-600 rounded-2xl">
                <span class="material-symbols-outlined text-2xl">print</span>
            </div>
            <div>
                <h3 class="font-black text-lg text-slate-900">Cetak Raport Per Angkatan</h3>
                <p class="text-xs text-slate-500">Pilih angkatan untuk mencetak semua raport sekaligus.</p>
            </div>
        </div>

        <form action="{{ route('raport.print-bulk') }}" method="GET" target="_blank">
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Angkatan <span class="text-rose-500">*</span></label>
                <select name="angkatan_id" required class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm py-2.5 px-3 focus:ring-2 focus:ring-orange-500/20">
                    <option value="">-- Pilih Angkatan --</option>
                    @foreach($angkatans as $a)
                        <option value="{{ $a->id }}" @selected(request('angkatan_id') == $a->id)>{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tahun Pelajaran (Opsional)</label>
                <select name="tahun_pelajaran" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm py-2.5 px-3 focus:ring-2 focus:ring-orange-500/20">
                    <option value="">Semua Tahun Pelajaran</option>
                    @foreach($tahunList as $tp)
                        <option value="{{ $tp }}" @selected(request('tahun_pelajaran') === $tp)>{{ $tp }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('printBulkModal').classList.add('hidden')" class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">Batal</button>
                <button type="submit" onclick="setTimeout(() => document.getElementById('printBulkModal').classList.add('hidden'), 500)" class="px-5 py-2.5 text-xs font-bold text-white bg-orange-600 hover:bg-orange-700 rounded-xl shadow-md shadow-orange-200 transition flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    Buka Tampilan Cetak
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Import Modal --}}
<div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl relative border border-slate-100">
        <button onclick="document.getElementById('importModal').classList.add('hidden')" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600">
            <span class="material-symbols-outlined">close</span>
        </button>

        <div class="flex items-center gap-3 mb-4">
            <div class="p-3 bg-orange-50 text-orange-600 rounded-2xl">
                <span class="material-symbols-outlined text-2xl">upload_file</span>
            </div>
            <div>
                <h3 class="font-black text-lg text-slate-900">Import Raport Excel</h3>
                <p class="text-xs text-slate-500">Unggah berkas spreadsheet data raport.</p>
            </div>
        </div>

        <form action="{{ route('raport.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-5">
                <label class="block text-xs font-bold text-slate-700 mb-2">Berkas Excel (.xlsx / .xls / .csv)</label>
                <input type="file" name="file" required accept=".xlsx,.xls,.csv" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 border border-slate-200 rounded-xl cursor-pointer">
            </div>

            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 text-xs text-slate-600 mb-6 space-y-1">
                <p class="font-bold text-slate-800">Petunjuk:</p>
                <p>1. Gunakan format template resmi agar kolom dapat dikenali secara presisi.</p>
                <p>2. Kolom <span class="font-bold text-orange-600">NIS / ID Siswa</span> wajib terisi sesuai data master siswa.</p>
            </div>

            <div class="flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-orange-600 hover:bg-orange-700 rounded-xl shadow-md shadow-orange-200 transition">Unggah & Process</button>
            </div>
        </form>
    </div>
</div>
@endsection
