@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @include('admin.partials.page-header', [
        'title' => 'Peraturan Poin',
        'eyebrow' => 'Kedisiplinan',
        'subtitle' => 'Kelola peraturan poin untuk kedisiplinan siswa.',
        'icon' => 'rule',
    ])

    <div class="flex justify-center">
        <a href="{{ route('point-rules.create') }}" class="px-5 py-3 rounded-xl bg-orange-600 text-white font-bold flex items-center gap-2 hover:bg-orange-700 transition-all">
            <span class="material-symbols-outlined">add</span>
            Tambah Peraturan
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
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
                    <tr>
                        <td class="px-6 py-4 text-slate-900"> {{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-slate-900 font-bold">{{ $rule->name }}</td>
                        <td class="px-6 py-4 text-slate-600">
                            @php
                                $statusLabels = ['late' => 'Terlambat', 'absent' => 'Alpa', 'present' => 'Hadir', 'excused' => 'Izin'];
                            @endphp
                            {{ $statusLabels[$rule->attendance_status] ?? $rule->attendance_status }}
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $rule->threshold }}x</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full {{ $rule->point_type == 'achievement' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} text-xs font-bold">
                                {{ $rule->point_type == 'achievement' ? '+' : '-' }}{{ $rule->point_score }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $rule->point_score }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full {{ $rule->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }} text-xs font-bold">
                                {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('point-rules.edit', $rule->id) }}" class="p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50" title="Edit">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                                <form action="{{ route('point-rules.destroy', $rule->id) }}" method="POST" onsubmit="return confirm('Hapus peraturan ini?')" style="display: inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100" title="Hapus">
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
