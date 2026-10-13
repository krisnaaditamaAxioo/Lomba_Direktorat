@extends('layouts.siswa')

@section('title', 'Dashboard Siswa')
@section('page_title', 'Ringkasan Akademik')

@section('content')
<div class="max-w-md mx-auto lg:max-w-none space-y-6">
    
    <!-- Blue Curved Header Block (Fintech Style) - Now Full Width on Mobile -->
    <div class="relative bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] -mx-4 -mt-4 pt-10 pb-16 px-6 text-white overflow-hidden shadow-2xl shadow-brand-950/10 rounded-b-[2.5rem] lg:mx-0 lg:mt-0 lg:rounded-[2.5rem] lg:pt-6 lg:pb-8">
        <!-- Abstract Circular Glow Overlays from homepage.jpg -->
        <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/5 rounded-full pointer-events-none"></div>
        <div class="absolute top-24 -left-12 w-40 h-40 bg-white/5 rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-8 right-24 w-28 h-28 bg-white/5 rounded-full pointer-events-none"></div>

        <div class="relative z-10 space-y-6">
            <!-- App Logo Branding & Points Pill -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center backdrop-blur-sm">
                        <span class="material-symbols-outlined text-white text-lg font-bold" style="font-variation-settings: 'FILL' 1;">school</span>
                    </div>
                    <span class="text-base font-black tracking-tight">SEH</span>
                </div>
                
                <!-- Points Display as Yellow Gold Pill -->
                <div class="bg-white text-slate-800 px-3 py-1.5 rounded-full text-xs font-extrabold flex items-center gap-1 shadow-sm">
                    <span class="material-symbols-outlined text-amber-500 text-sm" style="font-variation-settings: 'FILL' 1;">stars</span>
                    <span>{{ $netPoints }} Points</span>
                </div>
            </div>

            <!-- Student Information Display -->
            <div class="text-center py-2 space-y-1">
                <p class="text-xs text-brand-200/80 font-bold uppercase tracking-widest text-[10px]">Profil Siswa</p>
                <div class="flex flex-col items-center justify-center gap-1">
                    <span class="text-2xl lg:text-3xl font-black tracking-tight transition-all duration-200">{{ $student->nama_lengkap ?? 'Siswa' }}</span>
                    <p class="text-xs text-brand-200/70">{{ $student->email ?? '' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Quick Actions Card (Overlaps the header card slightly) -->
    <div class="relative z-20 -mt-12 px-2">
        <div class="bg-white rounded-[2rem] p-5 shadow-lg shadow-slate-200/60 border border-slate-50 flex justify-around items-center">
            
            <a href="{{ route('siswa.courses') }}" class="flex flex-col items-center gap-2 group">
                <div class="w-12 h-12 bg-brand-50 text-brand-600 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-105">
                    <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">auto_stories</span>
                </div>
                <span class="text-[11px] font-black text-slate-700 tracking-tight text-center">Kursus</span>
            </a>

            <a href="{{ route('siswa.attendance') }}" class="flex flex-col items-center gap-2 group">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-105">
                    <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">event_available</span>
                </div>
                <span class="text-[11px] font-black text-slate-700 tracking-tight text-center">Presensi</span>
            </a>

            <a href="{{ route('siswa.permit') }}" class="flex flex-col items-center gap-2 group">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-105">
                    <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">assignment_late</span>
                </div>
                <span class="text-[11px] font-black text-slate-700 tracking-tight text-center">Izin</span>
            </a>

            <!-- More Trigger: opens mobile slide-up sheet -->
            <button onclick="toggleMobileMenu()" class="flex flex-col items-center gap-2 group focus:outline-none">
                <div class="w-12 h-12 bg-brand-50 text-brand-600 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-105">
                    <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">more_horiz</span>
                </div>
                <span class="text-[11px] font-black text-slate-700 tracking-tight text-center">Lainnya</span>
            </button>

        </div>
    </div>

    <!-- Alert Banner inside content area -->
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

    <!-- "Send Again" Equivalent: Teacher & Class Consultation Shortcuts -->
    <div class="space-y-3 px-1">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-black text-slate-900 tracking-tight">Konsultasi Guru</h3>
            <a href="#" class="text-[11px] font-black text-[#F6863B] flex items-center gap-0.5 hover:underline">
                Lihat Semua <span class="material-symbols-outlined text-[10px] font-bold">arrow_forward</span>
            </a>
        </div>
        
        <!-- Horizontal Scroll List -->
        <div class="flex items-center gap-4 overflow-x-auto pb-2 -mx-4 px-4 scrollbar-none custom-scrollbar">
            <!-- Add New Action Box -->
            <a href="{{ route('siswa.permit') }}" class="flex flex-col items-center gap-2 shrink-0">
                <div class="w-14 h-14 rounded-full border-2 border-dashed border-[#F6863B] flex items-center justify-center text-[#F6863B] hover:bg-brand-50/50 transition-colors">
                    <span class="material-symbols-outlined text-xl">add</span>
                </div>
                <span class="text-[10px] font-black text-[#F6863B] tracking-tight">Ajukan</span>
            </a>

            <!-- Teachers List -->
            @foreach($teachers as $teacher)
                @php
                    $bgColors = ['f0fdf4', 'eff6ff', 'fdf2f8', 'fff7ed', 'fef3c7'];
                    $colorIndex = $loop->index % count($bgColors);
                    $textColors = ['16a34a', '2563eb', 'db2777', 'ea580c', 'd97706'];
                @endphp
                <div class="flex flex-col items-center gap-2 shrink-0">
                    @if($teacher->no_wa)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $teacher->no_wa) }}" target="_blank">
                    @endif
                    <img src="{{ $teacher->foto ? asset('storage/' . $teacher->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($teacher->name) . '&background=' . $bgColors[$colorIndex] . '&color=' . $textColors[$colorIndex] . '&bold=true' }}" class="w-14 h-14 rounded-full border border-slate-100 object-cover hover:scale-110 transition-transform cursor-pointer" alt="{{ $teacher->name }}">
                    @if($teacher->no_wa)
                        </a>
                    @endif
                    <span class="text-[10px] font-bold text-slate-600 tracking-tight">{{ $teacher->name }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Attendance Rate Display -->
    <div class="space-y-3 px-1">
        <h3 class="text-sm font-black text-slate-900 tracking-tight">Tingkat Kehadiran</h3>
        <div class="bg-gradient-to-br from-{{ $statusColor }}-50 to-{{ $statusColor }}-100/50 rounded-2xl p-6 border border-{{ $statusColor }}-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs text-{{ $statusColor }}-600 font-bold uppercase tracking-widest">Presentase Kehadiran</p>
                    <p class="text-3xl font-black text-{{ $statusColor }}-900">{{ $attendanceRate }}%</p>
                    <p class="text-xs text-{{ $statusColor }}-600 font-medium mt-2">Status: {{ $attendanceStatus }}</p>
                </div>
                <div class="w-20 h-20 rounded-full bg-{{ $statusColor }}-600 flex items-center justify-center text-white">
                    <span class="material-symbols-outlined text-5xl" style="font-variation-settings: 'FILL' 1;">event_available</span>
                </div>
            </div>
        </div>
    </div>

    <!-- "Lastest Transaction" Equivalent: Latest Academic Log -->
    <div class="space-y-3 px-1 pb-16 lg:pb-6">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-black text-slate-900 tracking-tight">Aktivitas Terakhir</h3>
            <a href="{{ route('siswa.attendance') }}" class="text-[11px] font-black text-[#5D3EBC] flex items-center gap-0.5 hover:underline">
                Lihat Semua <span class="material-symbols-outlined text-[10px] font-bold">arrow_forward</span>
            </a>
        </div>

        <div class="bg-white rounded-[2rem] border border-slate-100 p-4 shadow-sm divide-y divide-slate-50">
            @php
                $hasActivities = (isset($recentAttendances) && $recentAttendances->count() > 0) || (isset($recentPoints) && $recentPoints->count() > 0);
            @endphp

            @if(!$hasActivities)
            <div class="py-8 text-center">
                <div class="w-12 h-12 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="material-symbols-outlined text-2xl">inbox</span>
                </div>
                <p class="text-[11px] text-slate-400 font-bold tracking-tight">Belum ada catatan aktivitas masuk.</p>
            </div>
            @endif

            <!-- Attendance Transactions (Hadir/Present Log) -->
            @if(isset($recentAttendances))
                @foreach($recentAttendances as $attendance)
                <div class="py-3 flex items-center justify-between first:pt-0 last:pb-0 gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-lg">event_available</span>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-800 text-xs">Presensi Masuk</h4>
                            <p class="text-[10px] text-slate-400 font-medium">{{ $attendance->created_at->format('M d, Y • H:i') }}</p>
                        </div>
                    </div>
                    <!-- Score Column styled like green +Balance from homepage.jpg -->
                    <span class="text-xs font-black text-emerald-600 whitespace-nowrap">Hadir</span>
                </div>
                @endforeach
            @endif

            <!-- Violation/Points Transactions (Red or Orange depending on violation) -->
            @if(isset($recentPoints))
                @foreach($recentPoints as $point)
                <div class="py-3 flex items-center justify-between first:pt-0 last:pb-0 gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-lg">military_tech</span>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-800 text-xs">{{ $point->type }}</h4>
                            <p class="text-[10px] text-slate-400 font-medium leading-normal">{{ $point->created_at->format('M d, Y') }} • {{ Str::limit($point->reason, 20) }}</p>
                        </div>
                    </div>
                    <!-- Penalty added styled like a red negative transaction in homepage.jpg -->
                    <span class="text-xs font-black text-rose-500 whitespace-nowrap">+{{ $point->score }} Poin</span>
                </div>
                @endforeach
            @endif
        </div>
    </div>

</div>
@endsection