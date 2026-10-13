@extends('layouts.app')

@section('content')
<div class="space-y-8">
    @include('admin.partials.page-header', [
        'title' => 'Manajemen Nilai Ujian',
        'eyebrow' => 'Hasil Seleksi',
        'subtitle' => 'Lihat dan ekspor hasil ujian seleksi pendaftar.',
        'icon' => 'grading',
    ])

    <div class="flex flex-col sm:flex-row gap-2 justify-center">
        <a href="{{ route('admin.exams.results.export.pdf') }}" class="px-5 py-3 bg-white border border-slate-200 text-slate-700 rounded-2xl font-bold text-sm flex items-center justify-center gap-2 hover:bg-slate-50 transition-all" title="Download PDF">
            <span class="material-symbols-outlined">picture_as_pdf</span>
            PDF
        </a>
        <a href="{{ route('admin.exams.results.export.excel') }}" class="px-5 py-3 bg-emerald-600 text-white rounded-2xl font-bold text-sm shadow-lg shadow-emerald-100 flex items-center justify-center gap-2 hover:bg-emerald-700 transition-all" title="Download Excel">
            <span class="material-symbols-outlined">table</span>
            Excel
        </a>
        <a href="{{ route('admin.exams.export') }}" class="px-5 py-3 bg-slate-900 text-white rounded-2xl font-bold text-sm shadow-lg shadow-slate-200 flex items-center justify-center gap-2 hover:bg-slate-800 transition-all" title="Download CSV">
            <span class="material-symbols-outlined">download</span>
            CSV
        </a>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">Live Report Nilai Tertinggi</h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Update otomatis setiap 5 detik.</p>
            </div>
            <div class="text-xs font-bold text-slate-500">
                Terakhir update: <span id="topUpdatedAt" class="text-slate-700">-</span>
            </div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">No</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">ID Daftar</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Angkatan</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Benar / Total</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Skor</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Tanggal</th>
                    </tr>
                </thead>
                <tbody id="topResultsBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-400 font-medium">Memuat...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">No</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Siswa</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">ID Daftar</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Angkatan</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Benar / Total</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Skor</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Tanggal Ujian</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($results as $index => $res)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-slate-400">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-xs">
                                    {{ substr($res->registration->name, 0, 1) }}
                                </div>
                                <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $res->registration->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs font-bold text-slate-500">{{ $res->registration->application_id }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-full text-[10px] font-black uppercase">
                                Angkatan {{ $res->registration->angkatan ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-slate-600 dark:text-slate-400">
                            {{ $res->correct_answers }} / {{ $res->total_questions }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-lg font-black text-orange-600">{{ $res->score }}</span>
                        </td>
                        <td class="px-6 py-4 text-right text-xs font-medium text-slate-400">
                            {{ $res->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('admin.exams.results.delete', $res->id) }}" method="POST" onsubmit="return confirm('Hapus nilai ujian ini? Siswa harus mengerjakan ulang jika ingin mendapatkan nilai kembali.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Nilai">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <span class="material-symbols-outlined text-4xl text-slate-200 mb-2">analytics</span>
                                <p class="text-slate-400 font-medium">Belum ada hasil ujian masuk.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function refreshTopResults() {
        try {
            const response = await fetch("{{ route('admin.exams.results.top') }}?limit=5", { headers: { 'Accept': 'application/json' } });
            const payload = await response.json();

            document.getElementById('topUpdatedAt').textContent = payload.generated_at || '-';

            const tbody = document.getElementById('topResultsBody');
            const rows = (payload.data || []).map((row, idx) => {
                return `
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-4 py-3 text-sm font-bold text-slate-400">${idx + 1}</td>
                        <td class="px-4 py-3 text-sm font-bold text-slate-900 dark:text-white">${row.name ?? '-'}</td>
                        <td class="px-4 py-3 text-xs font-bold text-slate-500">${row.application_id ?? '-'}</td>
                        <td class="px-4 py-3"><span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-full text-[10px] font-black uppercase">Angkatan ${row.angkatan ?? '-'}</span></td>
                        <td class="px-4 py-3 text-sm font-bold text-slate-600 dark:text-slate-400">${row.correct_answers} / ${row.total_questions}</td>
                        <td class="px-4 py-3 text-center"><span class="text-lg font-black text-orange-600">${row.score}</span></td>
                        <td class="px-4 py-3 text-right text-xs font-medium text-slate-400">${row.created_at ?? '-'}</td>
                    </tr>
                `;
            }).join('');

            tbody.innerHTML = rows || `<tr><td colspan="7" class="px-4 py-8 text-center text-sm text-slate-400 font-medium">Belum ada data.</td></tr>`;
        } catch (e) {
            const tbody = document.getElementById('topResultsBody');
            tbody.innerHTML = `<tr><td colspan="7" class="px-4 py-8 text-center text-sm text-slate-400 font-medium">Gagal memuat live report.</td></tr>`;
        }
    }

    refreshTopResults();
    setInterval(refreshTopResults, 5000);
</script>
@endpush
