@extends('layouts.app')

@section('title', 'Edit Raport - ' . ($raport->student?->nama_lengkap ?? 'Siswa'))

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('raport.index') }}" class="p-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-500 transition-all">
        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
    </a>
    <div>
        <p class="text-xs font-bold text-orange-600 uppercase tracking-wider">Raport Industri</p>
        <h2 class="text-xl sm:text-2xl font-black text-slate-900">Edit Raport — {{ $raport->student?->nama_lengkap }}</h2>
    </div>
</div>

<form method="POST" action="{{ route('raport.update', $raport) }}" id="raportForm">
@csrf @method('PUT')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- LEFT: Identitas --}}
    <div class="lg:col-span-1 flex flex-col gap-5">

        {{-- Siswa (readonly) --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <p class="text-xs font-black uppercase tracking-wider text-slate-400 mb-4">Identitas Siswa</p>
            <div class="flex flex-col gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Nama Siswa</label>
                    <input type="text" readonly value="{{ $raport->student?->nama_lengkap }}"
                        class="w-full rounded-xl border-slate-200 bg-slate-100 text-sm py-2.5 px-3 text-slate-600 cursor-not-allowed">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-1">NIS</label>
                        <input type="text" readonly value="{{ $raport->student?->nis_siswa ?? $raport->student?->id_siswa }}"
                            class="w-full rounded-xl border-slate-200 bg-slate-100 text-xs py-2 px-3 text-slate-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-1">NISN</label>
                        <input type="text" readonly value="{{ $raport->student?->nisn }}"
                            class="w-full rounded-xl border-slate-200 bg-slate-100 text-xs py-2 px-3 text-slate-500 cursor-not-allowed">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Angkatan</label>
                    <input type="text" readonly value="{{ $raport->angkatan?->name }}"
                        class="w-full rounded-xl border-slate-200 bg-slate-100 text-sm py-2.5 px-3 text-slate-500 cursor-not-allowed">
                </div>
            </div>
        </div>

        {{-- Info Akademik --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <p class="text-xs font-black uppercase tracking-wider text-slate-400 mb-4">Info Akademik</p>
            <div class="flex flex-col gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tahun Pelajaran <span class="text-rose-500">*</span></label>
                    <input type="text" name="tahun_pelajaran" required value="{{ old('tahun_pelajaran', $raport->tahun_pelajaran) }}"
                        placeholder="2025/2026_Genap"
                        class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm py-2.5 px-3 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tingkat</label>
                    <input type="text" name="tingkat" value="{{ old('tingkat', $raport->tingkat) }}" placeholder="X (Sepuluh)"
                        class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm py-2.5 px-3 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Program Keahlian</label>
                    <input type="text" name="program_keahlian" value="{{ old('program_keahlian', $raport->program_keahlian) }}" placeholder="Rekayasa Perangkat Lunak"
                        class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm py-2.5 px-3 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400 transition-all">
                </div>
            </div>
        </div>

        {{-- Trainer & Wali Kelas --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <p class="text-xs font-black uppercase tracking-wider text-slate-400 mb-4">Trainer & Wali Kelas</p>
            <div class="flex flex-col gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Trainer</label>
                    <input type="text" name="trainer_name" value="{{ old('trainer_name', $raport->trainer_name) }}"
                        class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm py-2.5 px-3 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">No. Sertifikasi Trainer</label>
                    <input type="text" name="trainer_no_serti" value="{{ old('trainer_no_serti', $raport->trainer_no_serti) }}"
                        class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm py-2.5 px-3 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">NBM Trainer</label>
                    <input type="text" name="trainer_nbm" value="{{ old('trainer_nbm', $raport->trainer_nbm) }}"
                        class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm py-2.5 px-3 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Wali Kelas (dari Angkatan)</label>
                    <input type="text" readonly value="{{ $raport->waliKelas?->name ?? '-' }}"
                        class="w-full rounded-xl border-slate-200 bg-slate-100 text-sm py-2.5 px-3 text-slate-500 cursor-not-allowed">
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Penilaian --}}
    <div class="lg:col-span-2 flex flex-col gap-5">

        {{-- A: Capaian Pembelajaran --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-orange-600 flex items-center justify-center">
                        <span class="text-sm font-black text-white">A</span>
                    </div>
                    <div>
                        <p class="font-black text-slate-900 text-sm">Capaian Pembelajaran</p>
                        <p class="text-[10px] text-slate-400">Penilaian aspek pembelajaran (bisa diubah / ditambah)</p>
                    </div>
                </div>
                <button type="button" onclick="addAspekRow()" class="px-3 py-1.5 rounded-lg bg-orange-50 hover:bg-orange-100 text-orange-700 font-bold text-xs flex items-center gap-1 transition-all">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    <span>Tambah Aspek</span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[560px]">
                    <thead>
                        <tr class="bg-slate-50 rounded-xl">
                            <th class="px-3 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center w-8">No</th>
                            <th class="px-3 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-left">Aspek Penilaian</th>
                            <th class="px-3 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center w-24">Predikat</th>
                            <th class="px-3 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-left w-48">Keterangan</th>
                            <th class="px-2 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center w-10">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="aspekContainer" class="divide-y divide-slate-50">
                        @php
                            $aspekItems = $raport->capaianPembelajaran->count() > 0
                                ? $raport->capaianPembelajaran
                                : collect(array_map(fn($val, $key) => (object)['aspek_penilaian' => $val, 'predikat' => '', 'keterangan' => ''], $defaultAspek, array_keys($defaultAspek)));
                        @endphp
                        @foreach($aspekItems as $idx => $aspek)
                        <tr class="aspek-row">
                            <td class="px-3 py-3 text-center">
                                <span class="aspek-no text-xs font-black text-slate-400">{{ $idx + 1 }}</span>
                            </td>
                            <td class="px-3 py-3">
                                <input type="text" name="aspek[{{ $idx }}][aspek_penilaian]" value="{{ old("aspek.{$idx}.aspek_penilaian", $aspek->aspek_penilaian) }}" required
                                    class="w-full rounded-lg border-slate-200 bg-slate-50 text-xs py-1.5 px-2.5 focus:ring-2 focus:ring-orange-500/20 font-semibold text-slate-800">
                            </td>
                            <td class="px-3 py-3">
                                <select name="aspek[{{ $idx }}][predikat]" required onchange="updateKeterangan(this)"
                                    class="aspek-predikat w-full rounded-lg border-slate-200 bg-slate-50 text-xs py-1.5 px-2 text-center focus:ring-2 focus:ring-orange-500/20 font-bold">
                                    <option value="">-</option>
                                    @foreach(['A','B','C','D'] as $p)
                                        <option value="{{ $p }}" @selected(old("aspek.{$idx}.predikat", $aspek->predikat) === $p)>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-3 py-3">
                                <input type="text" name="aspek[{{ $idx }}][keterangan]"
                                    value="{{ old("aspek.{$idx}.keterangan", $aspek->keterangan) }}"
                                    class="aspek-keterangan w-full rounded-lg border-slate-200 bg-slate-50 text-xs py-1.5 px-2 focus:ring-2 focus:ring-orange-500/20"
                                    placeholder="Auto dari predikat">
                            </td>
                            <td class="px-2 py-3 text-center">
                                <button type="button" onclick="removeAspekRow(this)" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-all">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3 p-3 bg-slate-50 rounded-xl">
                <p class="text-[10px] font-bold text-slate-500">Keterangan Predikat: <span class="font-black text-orange-600">A</span> = Sangat Baik &nbsp; <span class="font-black text-emerald-600">B</span> = Baik &nbsp; <span class="font-black text-amber-600">C</span> = Cukup &nbsp; <span class="font-black text-rose-600">D</span> = Kurang</p>
            </div>
        </div>

        {{-- B: Uji Sertifikasi --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center">
                        <span class="text-sm font-black text-white">B</span>
                    </div>
                    <div>
                        <p class="font-black text-slate-900 text-sm">Uji Sertifikasi</p>
                        <p class="text-[10px] text-slate-400">Data sertifikasi ACP yang ditempuh siswa</p>
                    </div>
                </div>
                <button type="button" onclick="addUjianRow()" class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center gap-1 transition-all">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    <span>Tambah Uji Sertifikasi</span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[520px]">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-3 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center w-8">No</th>
                            <th class="px-3 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-left">Nama Ujian</th>
                            <th class="px-3 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-left w-40">No. Sertifikasi</th>
                            <th class="px-3 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center w-32">Status</th>
                            <th class="px-2 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center w-10">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="ujianContainer" class="divide-y divide-slate-50">
                        @php
                            $ujianItems = $raport->ujiSertifikasi->count() > 0
                                ? $raport->ujiSertifikasi
                                : collect(array_map(fn($val) => (object)['nama_ujian' => $val, 'no_serti' => '', 'status' => 'LULUS'], $defaultUjian));
                        @endphp
                        @foreach($ujianItems as $idx => $ujian)
                        <tr class="ujian-row">
                            <td class="px-3 py-3 text-center">
                                <span class="ujian-no text-xs font-black text-slate-400">{{ $idx + 1 }}</span>
                            </td>
                            <td class="px-3 py-3">
                                <input type="text" name="ujian[{{ $idx }}][nama_ujian]" value="{{ old("ujian.{$idx}.nama_ujian", $ujian->nama_ujian) }}" required
                                    class="w-full rounded-lg border-slate-200 bg-slate-50 text-xs py-1.5 px-2.5 focus:ring-2 focus:ring-emerald-500/20 font-semibold text-slate-800" placeholder="e.g. UJI SERTIFIKASI ACP 1">
                            </td>
                            <td class="px-3 py-3">
                                <input type="text" name="ujian[{{ $idx }}][no_serti]"
                                    value="{{ old("ujian.{$idx}.no_serti", $ujian->no_serti) }}"
                                    class="w-full rounded-lg border-slate-200 bg-slate-50 text-xs py-1.5 px-2 focus:ring-2 focus:ring-emerald-500/20 tracking-widest uppercase"
                                    placeholder="No. Sertifikat">
                            </td>
                            <td class="px-3 py-3">
                                <select name="ujian[{{ $idx }}][status]"
                                    class="w-full rounded-lg border-slate-200 bg-slate-50 text-xs py-1.5 px-2 focus:ring-2 focus:ring-emerald-500/20 font-bold">
                                    <option value="LULUS" @selected(old("ujian.{$idx}.status", $ujian->status) === 'LULUS')>LULUS</option>
                                    <option value="TIDAK LULUS" @selected(old("ujian.{$idx}.status", $ujian->status) === 'TIDAK LULUS')>TIDAK LULUS</option>
                                </select>
                            </td>
                            <td class="px-2 py-3 text-center">
                                <button type="button" onclick="removeUjianRow(this)" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-all">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Catatan --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <p class="text-xs font-black uppercase tracking-wider text-slate-400 mb-3">Catatan</p>
            <textarea name="catatan" rows="4"
                class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm py-3 px-4 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400 transition-all resize-none"
                placeholder="Catatan untuk siswa...">{{ old('catatan', $raport->catatan) }}</textarea>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('raport.print', $raport) }}" target="_blank"
               class="px-5 py-3 rounded-xl border border-emerald-200 text-emerald-700 hover:bg-emerald-50 font-bold text-sm transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">print</span> Preview Cetak
            </a>
            <div class="flex gap-3">
                <a href="{{ route('raport.index') }}" class="px-6 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-sm transition-all">Batal</a>
                <button type="submit" class="px-8 py-3 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-md shadow-orange-200 transition-all active:scale-95 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>
</form>
@endsection

@section('scripts')
<script>
const predikatMap = { 'A': 'Sangat Baik', 'B': 'Baik', 'C': 'Cukup', 'D': 'Kurang' };

function updateKeterangan(select) {
    const val = select.value;
    const row = select.closest('tr');
    const ketInput = row ? row.querySelector('.aspek-keterangan') : null;
    if (ketInput && !ketInput.dataset.manual) {
        ketInput.value = predikatMap[val] || '';
    }
}

document.addEventListener('input', function(e) {
    if (e.target.classList.contains('aspek-keterangan')) {
        e.target.dataset.manual = '1';
    }
});

function reindexAspek() {
    const rows = document.querySelectorAll('#aspekContainer .aspek-row');
    rows.forEach((row, idx) => {
        row.querySelector('.aspek-no').textContent = idx + 1;
        const nameInput = row.querySelector('input[name*="[aspek_penilaian]"]');
        const predSelect = row.querySelector('select[name*="[predikat]"]');
        const ketInput = row.querySelector('input[name*="[keterangan]"]');

        if (nameInput) nameInput.name = `aspek[${idx}][aspek_penilaian]`;
        if (predSelect) predSelect.name = `aspek[${idx}][predikat]`;
        if (ketInput) ketInput.name = `aspek[${idx}][keterangan]`;
    });
}

function addAspekRow() {
    const container = document.getElementById('aspekContainer');
    const idx = container.querySelectorAll('.aspek-row').length;
    const tr = document.createElement('tr');
    tr.className = 'aspek-row';
    tr.innerHTML = `
        <td class="px-3 py-3 text-center">
            <span class="aspek-no text-xs font-black text-slate-400">${idx + 1}</span>
        </td>
        <td class="px-3 py-3">
            <input type="text" name="aspek[${idx}][aspek_penilaian]" required
                class="w-full rounded-lg border-slate-200 bg-slate-50 text-xs py-1.5 px-2.5 focus:ring-2 focus:ring-orange-500/20 font-semibold text-slate-800" placeholder="Nama Aspek Penilaian">
        </td>
        <td class="px-3 py-3">
            <select name="aspek[${idx}][predikat]" required onchange="updateKeterangan(this)"
                class="aspek-predikat w-full rounded-lg border-slate-200 bg-slate-50 text-xs py-1.5 px-2 text-center focus:ring-2 focus:ring-orange-500/20 font-bold">
                <option value="">-</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>
        </td>
        <td class="px-3 py-3">
            <input type="text" name="aspek[${idx}][keterangan]"
                class="aspek-keterangan w-full rounded-lg border-slate-200 bg-slate-50 text-xs py-1.5 px-2 focus:ring-2 focus:ring-orange-500/20"
                placeholder="Auto dari predikat">
        </td>
        <td class="px-2 py-3 text-center">
            <button type="button" onclick="removeAspekRow(this)" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-all">
                <span class="material-symbols-outlined text-[18px]">delete</span>
            </button>
        </td>
    `;
    container.appendChild(tr);
    reindexAspek();
}

function removeAspekRow(btn) {
    const container = document.getElementById('aspekContainer');
    if (container.querySelectorAll('.aspek-row').length <= 1) {
        alert('Minimal harus ada 1 aspek penilaian.');
        return;
    }
    btn.closest('tr').remove();
    reindexAspek();
}

function reindexUjian() {
    const rows = document.querySelectorAll('#ujianContainer .ujian-row');
    rows.forEach((row, idx) => {
        row.querySelector('.ujian-no').textContent = idx + 1;
        const nameInput = row.querySelector('input[name*="[nama_ujian]"]');
        const noSertiInput = row.querySelector('input[name*="[no_serti]"]');
        const statusSelect = row.querySelector('select[name*="[status]"]');

        if (nameInput) nameInput.name = `ujian[${idx}][nama_ujian]`;
        if (noSertiInput) noSertiInput.name = `ujian[${idx}][no_serti]`;
        if (statusSelect) statusSelect.name = `ujian[${idx}][status]`;
    });
}

function addUjianRow() {
    const container = document.getElementById('ujianContainer');
    const idx = container.querySelectorAll('.ujian-row').length;
    const tr = document.createElement('tr');
    tr.className = 'ujian-row';
    tr.innerHTML = `
        <td class="px-3 py-3 text-center">
            <span class="ujian-no text-xs font-black text-slate-400">${idx + 1}</span>
        </td>
        <td class="px-3 py-3">
            <input type="text" name="ujian[${idx}][nama_ujian]" required
                class="w-full rounded-lg border-slate-200 bg-slate-50 text-xs py-1.5 px-2.5 focus:ring-2 focus:ring-emerald-500/20 font-semibold text-slate-800" placeholder="e.g. UJI SERTIFIKASI ACP ${idx + 1}">
        </td>
        <td class="px-3 py-3">
            <input type="text" name="ujian[${idx}][no_serti]"
                class="w-full rounded-lg border-slate-200 bg-slate-50 text-xs py-1.5 px-2 focus:ring-2 focus:ring-emerald-500/20 tracking-widest uppercase"
                placeholder="No. Sertifikat">
        </td>
        <td class="px-3 py-3">
            <select name="ujian[${idx}][status]"
                class="w-full rounded-lg border-slate-200 bg-slate-50 text-xs py-1.5 px-2 focus:ring-2 focus:ring-emerald-500/20 font-bold">
                <option value="LULUS">LULUS</option>
                <option value="TIDAK LULUS">TIDAK LULUS</option>
            </select>
        </td>
        <td class="px-2 py-3 text-center">
            <button type="button" onclick="removeUjianRow(this)" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-all">
                <span class="material-symbols-outlined text-[18px]">delete</span>
            </button>
        </td>
    `;
    container.appendChild(tr);
    reindexUjian();
}

function removeUjianRow(btn) {
    btn.closest('tr').remove();
    reindexUjian();
}
</script>
@endsection
