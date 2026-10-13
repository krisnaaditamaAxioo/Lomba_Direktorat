@extends('layouts.parent')

@section('title', 'Dashboard Wali Murid')
@section('page_title', 'Ringkasan Wali Murid')

@section('content')
<div class="max-w-md mx-auto lg:max-w-none space-y-6">
    
    <!-- Emerald Header Block -->
    <div class="relative bg-gradient-to-br from-[#047857] via-[#065f46] to-[#022c22] -mx-4 -mt-4 pt-8 pb-14 px-6 text-white overflow-hidden shadow-xl rounded-b-[2.5rem] lg:mx-0 lg:mt-0 lg:rounded-[2.5rem] lg:pt-6 lg:pb-8">
        <div class="relative z-10 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-300">family_restroom</span>
                    <span class="text-xs font-black tracking-widest uppercase text-emerald-200">Portal Resmi Orang Tua</span>
                </div>
                <div class="bg-white/10 backdrop-blur-md px-3 py-1 rounded-full text-xs font-extrabold flex items-center gap-1">
                    <span class="material-symbols-outlined text-amber-400 text-sm">stars</span>
                    <span>{{ $netPoints }} Poin Siswa</span>
                </div>
            </div>

            <div class="text-center py-2 space-y-1">
                <p class="text-[10px] text-emerald-200/80 font-bold uppercase tracking-widest">Selamat Datang, {{ $parent->name }}</p>
                <h2 class="text-2xl lg:text-3xl font-black tracking-tight">Anak: {{ $student?->nama_lengkap ?? 'Belum terhubung' }}</h2>
                <p class="text-xs text-emerald-100/70 font-medium">NISN: {{ $student?->nisn ?? '-' }} | Angkatan: {{ $student?->angkatanModel?->code ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Menu Icons -->
    <div class="relative z-20 -mt-10 px-2">
        <div class="bg-white rounded-[2rem] p-5 shadow-lg shadow-slate-200/60 border border-slate-50 flex justify-around items-center">
            <a href="{{ route('parent.attendance') }}" class="flex flex-col items-center gap-2 group">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-105">
                    <span class="material-symbols-outlined text-xl">event_available</span>
                </div>
                <span class="text-[11px] font-black text-slate-700 tracking-tight text-center">Absensi</span>
            </a>

            <a href="{{ route('parent.finance') }}" class="flex flex-col items-center gap-2 group">
                <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-105">
                    <span class="material-symbols-outlined text-xl">account_balance_wallet</span>
                </div>
                <span class="text-[11px] font-black text-slate-700 tracking-tight text-center">Keuangan</span>
            </a>

            <a href="{{ route('parent.points') }}" class="flex flex-col items-center gap-2 group">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-105">
                    <span class="material-symbols-outlined text-xl">military_tech</span>
                </div>
                <span class="text-[11px] font-black text-slate-700 tracking-tight text-center">Poin</span>
            </a>

            <a href="{{ route('parent.permit') }}" class="flex flex-col items-center gap-2 group">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-105">
                    <span class="material-symbols-outlined text-xl">assignment_late</span>
                </div>
                <span class="text-[11px] font-black text-slate-700 tracking-tight text-center">Izin Sakit</span>
            </a>
        </div>
    </div>

    @if($violationAlert)
    <div class="mx-1 p-4 rounded-2xl border bg-rose-50 border-rose-100 text-rose-950">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-rose-500 text-white flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-base">warning</span>
            </div>
            <div class="space-y-0.5">
                <h3 class="text-xs font-black tracking-tight">{{ $violationAlert['title'] }}</h3>
                <p class="text-[11px] font-medium opacity-90 leading-relaxed">{{ $violationAlert['message'] }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Presensi & Poin Terakhir -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Card Presensi Terakhir -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600">event_available</span>
                    Presensi Terakhir Siswa
                </h3>
                <a href="{{ route('parent.attendance') }}" class="text-xs text-emerald-600 font-bold hover:underline">Lihat Semua</a>
            </div>

            <div class="space-y-2">
                @forelse($recentAttendances as $att)
                <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                    <div>
                        <p class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('l, d F Y') }}</p>
                        <p class="text-[10px] text-slate-400">Jam Masuk: {{ $att->check_in ?: '-' }}</p>
                    </div>
                    <div>
                        @if($att->status === 'present')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Ontime</span>
                        @elseif($att->status === 'late')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Telat</span>
                        @elseif($att->status === 'excused')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-orange-100 text-orange-700">Izin/Sakit</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">Alpa</span>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-4 italic">Belum ada riwayat presensi.</p>
                @endforelse
            </div>
        </div>

        <!-- Card Poin Kedisiplinan Terakhir -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500">military_tech</span>
                    Catatan Poin Kedisiplinan
                </h3>
                <a href="{{ route('parent.points') }}" class="text-xs text-emerald-600 font-bold hover:underline">Lihat Semua</a>
            </div>

            <div class="space-y-2">
                @forelse($recentPoints as $p)
                <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                    <div>
                        <p class="font-bold text-slate-800">{{ $p->reason }}</p>
                        <p class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($p->date)->translatedFormat('d M Y') }}</p>
                    </div>
                    <div>
                        @if($p->type === 'violation')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">-{{ $p->score }} Poin</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">+{{ $p->score }} Poin</span>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-4 italic">Tidak ada catatan pelanggaran.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
