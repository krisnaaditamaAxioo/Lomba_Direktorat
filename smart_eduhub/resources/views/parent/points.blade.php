@extends('layouts.parent')

@section('title', 'Poin Kedisiplinan')
@section('page_title', 'Catatan Poin Kedisiplinan Siswa')

@section('content')
<div class="space-y-6">
    <!-- <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900">Riwayat Poin Kedisiplinan: {{ $student->nama_lengkap }}</h2>
            <p class="text-xs text-slate-400 mt-0.5">Catatan pelanggaran dan penghargaan kedisiplinan siswa.</p>
        </div>
    </div> -->

    <!-- Tabel Poin -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden p-6 space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 font-bold text-slate-500 uppercase">Tanggal</th>
                        <th class="px-6 py-4 font-bold text-slate-500 uppercase">Kategori</th>
                        <th class="px-6 py-4 font-bold text-slate-500 uppercase">Keterangan / Alasan</th>
                        <th class="px-6 py-4 font-bold text-slate-500 uppercase text-right">Poin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($points as $p)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 text-slate-600 font-bold">
                            {{ \Carbon\Carbon::parse($p->date)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($p->type === 'violation')
                                <span class="px-2.5 py-1 rounded-full font-bold bg-rose-100 text-rose-700 text-[10px]">Pelanggaran</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full font-bold bg-emerald-100 text-emerald-700 text-[10px]">Penghargaan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-800">
                            {{ $p->reason }}
                        </td>
                        <td class="px-6 py-4 text-right font-black text-sm {{ $p->type === 'violation' ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ $p->type === 'violation' ? '-' : '+' }}{{ $p->score }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-400 italic">
                            Tidak ada data catatan poin kedisiplinan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-2">
            {{ $points->links() }}
        </div>
    </div>
</div>
@endsection
