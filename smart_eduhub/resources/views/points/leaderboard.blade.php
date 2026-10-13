@extends('layouts.app')

@section('title', 'Leaderboard - Discipline & Awards')

@section('content')
<div class="mb-8">
    <nav class="flex items-center space-x-2 text-outline mb-2">
        <a href="{{ route('points') }}" class="font-label-sm text-label-sm hover:text-primary transition-colors">Merit & Discipline</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="font-label-sm text-label-sm text-primary">Leaderboard</span>
    </nav>
    <h2 class="font-display-xl text-display-xl text-on-surface">{{ __('Leaderboard') }}</h2>
    <p class="font-body-md text-body-md text-on-surface-variant mt-1">Siswa terbaik dan pemantauan pelanggaran.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Top Achievements (Penghargaan) -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 bg-emerald-50 border-b border-emerald-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center text-white">
                    <span class="material-symbols-outlined">military_tech</span>
                </div>
                <div>
                    <h3 class="font-bold text-emerald-900">{{ __('Top Achievements') }}</h3>
                    <p class="text-xs text-emerald-700">Penghargaan Tertinggi</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($topAchievements as $index => $item)
                <div class="flex items-center justify-between p-4 rounded-2xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-full {{ $index == 0 ? 'bg-amber-400' : ($index == 1 ? 'bg-slate-300' : ($index == 2 ? 'bg-amber-600' : 'bg-slate-100')) }} flex items-center justify-center text-white font-black text-xs">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">{{ $item->student->nama_lengkap ?? 'Unknown' }}</p>
                            <p class="text-xs text-slate-500">Angkatan {{ $item->student->angkatan ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-black text-emerald-600">+{{ $item->total_score }}</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('Points') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-center text-slate-400 py-8">{{ __('No data available') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Top Violations (Pelanggaran) -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 bg-rose-50 border-b border-rose-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-500 flex items-center justify-center text-white">
                    <span class="material-symbols-outlined">warning</span>
                </div>
                <div>
                    <h3 class="font-bold text-rose-900">{{ __('Top Violations') }}</h3>
                    <p class="text-xs text-rose-700">Pelanggaran Terbanyak</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($topViolations as $index => $item)
                <div class="flex items-center justify-between p-4 rounded-2xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-black text-xs">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">{{ $item->student->nama_lengkap ?? 'Unknown' }}</p>
                            <p class="text-xs text-slate-500">Angkatan {{ $item->student->angkatan ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-black text-rose-600">-{{ $item->total_score }}</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('Points') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-center text-slate-400 py-8">{{ __('No data available') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
