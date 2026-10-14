@extends('layouts.app')

@php
    $statusLabels = ['late' => 'Terlambat', 'absent' => 'Alpa', 'present' => 'Hadir', 'excused' => 'Izin'];
@endphp

@section('content')
<div class="space-y-5 md:space-y-6">

    {{-- Header: menempel ke tepi atas & samping di mobile (membatalkan padding layout), normal di desktop --}}
    <div class="-mx-4 -mt-4 sm:-mx-6 sm:-mt-6 md:mx-0 md:mt-0 md:rounded-3xl overflow-hidden">
        @include('admin.partials.page-header', [
            'title' => 'Peraturan Poin',
            'eyebrow' => 'Kedisiplinan',
            'subtitle' => 'Kelola peraturan poin untuk kedisiplinan siswa.',
            'icon' => 'rule',
        ])
    </div>

    {{-- Aksi --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-sm text-slate-500">
            Total <span class="font-bold text-slate-800">{{ count($rules) }}</span> peraturan
        </p>
        <a href="{{ route('point-rules.create') }}"
           class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold flex items-center justify-center gap-2 shadow-lg shadow-orange-500/20 hover:shadow-orange-500/40 hover:-translate-y-0.5 active:scale-95 transition-all">
            <span class="material-symbols-outlined">add</span>
            Tambah Peraturan
        </a>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- ========== MOBILE: tampilan kartu ========== --}}
    <div class="grid gap-3 md:hidden">
        @forelse($rules as $rule)
            @php $isAchievement = $rule->point_type == 'achievement'; @endphp
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[11px] font-black uppercase tracking-widest text-slate-400">#{{ $loop->iteration }}</p>
                        <h3 class="font-bold text-slate-900 leading-snug break-words">{{ $rule->name }}</h3>
                    </div>
                    <span class="shrink-0 px-3 py-1 rounded-full text-xs font-bold {{ $rule->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                    <div class="rounded-xl bg-slate-50 px-2 py-2">
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Status</p>
                        <p class="text-sm font-semibold text-slate-700">{{ $statusLabels[$rule->attendance_status] ?? $rule->attendance_status }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 px-2 py-2">
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Batas</p>
                        <p class="text-sm font-semibold text-slate-700">{{ $rule->threshold }}x</p>
                    </div>
                    <div class="rounded-xl px-2 py-2 {{ $isAchievement ? 'bg-emerald-50' : 'bg-rose-50' }}">
                        <p class="text-[10px] font-black uppercase tracking-wider {{ $isAchievement ? 'text-emerald-500' : 'text-rose-400' }}">Poin</p>
                        <p class="text-sm font-bold {{ $isAchievement ? 'text-emerald-700' : 'text-rose-700' }}">{{ $isAchievement ? '+' : '-' }}{{ $rule->point_score }}</p>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-between">
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $isAchievement ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                        {{ $isAchievement ? 'Prestasi' : 'Pelanggaran' }}
                    </span>
                    <div class="flex gap-2">
                        <a href="{{ route('point-rules.edit', $rule->id) }}" class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 active:scale-95 transition" title="Edit">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </a>
                        <form action="{{ route('point-rules.destroy', $rule->id) }}" method="POST" onsubmit="return confirm('Hapus peraturan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 active:scale-95 transition" title="Hapus">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-dashed border-slate-200 px-6 py-10 text-center text-slate-500">
                <span class="material-symbols-outlined text-4xl text-slate-300">rule</span>
                <p class="mt-2">Belum ada peraturan poin.</p>
            </div>
        @endforelse
    </div>

    {{-- ========== DESKTOP/TABLET: tabel ========== --}}
    <div class="hidden md:block bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-slate-400">No</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-slate-400">Nama Peraturan</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-slate-400">Status Kehadiran</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-slate-400">Batas</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-slate-400">Tipe Poin</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-slate-400">Jumlah Poin</th>
                        <th class="px-6 py-4 text-center text-xs font-black uppercase tracking-widest text-slate-400">Aktif</th>
                        <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-widest text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rules as $rule)
                    @php $isAchievement = $rule->point_type == 'achievement'; @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-4 text-slate-900">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-slate-900 font-bold">{{ $rule->name }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $statusLabels[$rule->attendance_status] ?? $rule->attendance_status }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $rule->threshold }}x</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $isAchievement ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                {{ $isAchievement ? 'Prestasi' : 'Pelanggaran' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold {{ $isAchievement ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $isAchievement ? '+' : '-' }}{{ $rule->point_score }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $rule->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('point-rules.edit', $rule->id) }}" class="p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition" title="Edit">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                                <form action="{{ route('point-rules.destroy', $rule->id) }}" method="POST" onsubmit="return confirm('Hapus peraturan ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition" title="Hapus">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-slate-500">Belum ada peraturan poin.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection