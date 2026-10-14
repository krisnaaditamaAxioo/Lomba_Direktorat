@extends('layouts.parent')

@section('title', 'Surat Resmi Axioo')
@section('page_title', 'Surat Resmi & Pemberitahuan Sekolah')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
        <h1 class="text-xl font-black text-slate-900">Surat Resmi Sekolah</h1>
        <p class="text-xs text-slate-400 mt-1">Daftar surat binaan, surat peringatan, dan pemberitahuan resmi untuk: <strong>{{ $student->nama_lengkap }}</strong>.</p>
    </div>

    <div class="space-y-4">
        @forelse($letters as $letter)
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden p-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-2xl">description</span>
                        </div>
                        <div>
                            @php
                                $typeLabel = match($letter->type) {
                                    'binaan' => 'Surat Binaan Kedisiplinan',
                                    'sp1' => 'Surat Peringatan 1 (SP1)',
                                    'sp2' => 'Surat Peringatan 2 (SP2)',
                                    'sp3' => 'Surat Peringatan 3 (SP3)',
                                    default => 'Surat Resmi'
                                };
                            @endphp
                            <h3 class="text-base font-black text-slate-900 leading-tight">{{ $typeLabel }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[10px] font-mono text-slate-400">{{ $letter->reference_number }}</span>
                                <span class="text-slate-300">•</span>
                                <span class="text-[10px] font-bold text-slate-500">{{ \Carbon\Carbon::parse($letter->issued_at)->translatedFormat('d M Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold">
                            {{ $letter->status_label ?? 'Diterbitkan' }}
                        </span>
                        <a href="{{ route('letters.print', ['student' => $student->id, 'type' => $letter->type]) }}" target="_blank" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-3.5 py-2 rounded-xl flex items-center gap-1.5 transition-all">
                            <span class="material-symbols-outlined text-sm">print</span>
                            Cetak Surat
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl border border-slate-100 p-8 text-center text-slate-400 text-xs italic">
                Belum ada surat resmi yang diterbitkan untuk siswa ini.
            </div>
        @endforelse
    </div>
</div>
@endsection
