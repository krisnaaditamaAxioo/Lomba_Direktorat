@extends('layouts.app')

@section('title', 'Ringkasan Laporan Absensi')

@section('styles')
<style>
    .summary-cell { min-width: 60px; text-align: center; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
</style>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Page Header (Reuse from detail report) -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 no-print">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Ringkasan Laporan Absensi</h2>
            <p class="text-slate-500 font-medium mt-1">Rekapitulasi jumlah kehadiran per bulan untuk periode yang dipilih.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <form action="{{ route('attendance.report') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="mode" value="summary">
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Angkatan</label>
                    <select name="angkatan" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all">
                        <option value="">Semua Angkatan</option>
                        @foreach($angkatans as $angkatanItem)
                            <option value="{{ $angkatanItem->code }}" {{ $angkatan === $angkatanItem->code ? 'selected' : '' }}>{{ $angkatanItem->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $start_date }}" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $end_date }}" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Cari Nama Siswa</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama..." class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mode Laporan</label>
                    <select name="mode" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all">
                        <option value="detail" {{ $mode === 'detail' ? 'selected' : '' }}>Harian (1-31 Hari)</option>
                        <option value="summary" {{ $mode === 'summary' ? 'selected' : '' }}>Ringkasan Bulanan</option>
                    </select>
                </div>
            </form>
            
            <div class="flex items-center gap-2 mt-auto">
                <a href="{{ route('attendance.report', array_merge(request()->all(), ['print' => 1])) }}" target="_blank" class="flex items-center gap-2 bg-white border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm hover:bg-slate-50 active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-[20px]">print</span>
                    Cetak PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Legends -->
    <div class="flex flex-wrap gap-4 p-4 bg-slate-50 border border-slate-100 rounded-2xl no-print">
        <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-500 text-white font-bold text-[12px]">H</span>
            <span class="text-xs font-bold text-slate-600">Hadir (Ontime + Telat)</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-100 text-emerald-700 font-bold text-[12px]">O</span>
            <span class="text-xs font-bold text-slate-600">Ontime</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center bg-amber-100 text-amber-700 font-bold text-[12px]">T</span>
            <span class="text-xs font-bold text-slate-600">Telat</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange-100 text-orange-700 font-bold text-[12px]">I</span>
            <span class="text-xs font-bold text-slate-600">Izin</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange-100 text-orange-700 font-bold text-[12px]">S</span>
            <span class="text-xs font-bold text-slate-600">Sakit</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center bg-rose-100 text-rose-700 font-bold text-[12px]">A</span>
            <span class="text-xs font-bold text-slate-600">Alpa</span>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white border border-slate-100 rounded-[2rem] shadow-sm overflow-hidden flex flex-col">
        <div class="p-6 border-b border-slate-50">
            <h3 class="text-xl font-black text-slate-900">Ringkasan Kehadiran - {{ $angkatan ? 'Angkatan ' . $angkatan : 'Semua Angkatan' }}</h3>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse" style="min-width: 800px;">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="p-4 text-[10px] font-black text-slate-400 uppercase tracking-widest sticky left-0 bg-slate-50 z-20 w-48 border-r border-slate-100" rowspan="2">Nama Siswa</th>
                        @foreach($months as $month)
                            <th class="p-2 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-100" colspan="6">{{ $month['name'] }}</th>
                        @endforeach
                        <th class="p-4 text-[10px] font-black text-slate-900 uppercase tracking-widest text-center bg-slate-100 border-l-2 border-slate-200" colspan="6">Total Keseluruhan</th>
                    </tr>
                    <tr class="bg-slate-50/50">
                        @foreach($months as $month)
                            <th class="p-2 text-center text-[9px] font-black text-emerald-700 uppercase border-r border-slate-100 bg-emerald-500 text-white">H</th>
                            <th class="p-2 text-center text-[9px] font-black text-emerald-600 uppercase border-r border-slate-100 bg-emerald-50/30">O</th>
                            <th class="p-2 text-center text-[9px] font-black text-amber-600 uppercase border-r border-slate-100 bg-amber-50/30">T</th>
                            <th class="p-2 text-center text-[9px] font-black text-orange-600 uppercase border-r border-slate-100 bg-orange-50/30">I</th>
                            <th class="p-2 text-center text-[9px] font-black text-orange-600 uppercase border-r border-slate-100 bg-orange-50/30">S</th>
                            <th class="p-2 text-center text-[9px] font-black text-rose-600 uppercase border-r border-slate-100 bg-rose-50/30">A</th>
                        @endforeach
                        <th class="p-2 text-center text-[9px] font-black text-white uppercase border-r border-slate-100 bg-emerald-600 border-l-2 border-slate-200">H</th>
                        <th class="p-2 text-center text-[9px] font-black text-emerald-700 uppercase border-r border-slate-100 bg-emerald-100/30">O</th>
                        <th class="p-2 text-center text-[9px] font-black text-amber-700 uppercase border-r border-slate-100 bg-amber-100/30">T</th>
                        <th class="p-2 text-center text-[9px] font-black text-orange-700 uppercase border-r border-slate-100 bg-orange-100/30">I</th>
                        <th class="p-2 text-center text-[9px] font-black text-orange-700 uppercase border-r border-slate-100 bg-orange-100/30">S</th>
                        <th class="p-2 text-center text-[9px] font-black text-rose-700 uppercase bg-rose-100/30">A</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($students as $student)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-4 sticky left-0 bg-white z-10 border-r border-slate-100">
                            <p class="text-xs font-bold text-slate-900 truncate">{{ $student->nama_lengkap }}</p>
                            <p class="text-[9px] font-black text-orange-600 uppercase tracking-tight mt-0.5">Angkatan {{ $student->angkatan ?? '-' }}</p>
                        </td>
                        
                        @php
                            $total_o = 0; $total_t = 0; $total_i = 0; $total_s = 0; $total_a = 0;
                        @endphp
                        
                        @foreach($months as $month)
                            @php
                                // Get all dates in this month within the selected period
                                $monthStart = \Carbon\Carbon::parse($month['key'] . '-01');
                                $monthEnd = $monthStart->copy()->endOfMonth();
                                $reportStart = \Carbon\Carbon::parse($start_date);
                                $reportEnd = \Carbon\Carbon::parse($end_date);
                                $today = \Carbon\Carbon::today();
                                
                                $periodStart = $monthStart->max($reportStart);
                                $periodEnd = $monthEnd->min($reportEnd)->min($today); // Only count up to today
                                
                                // Calculate total holidays in this period (including Sundays and monthly)
                                $holidayCount = 0;
                                if ($periodStart <= $periodEnd) {
                                    $currentDate = $periodStart->copy();
                                    while ($currentDate <= $periodEnd) {
                                        if (\App\Models\Holiday::checkHoliday($currentDate)) {
                                            $holidayCount++;
                                        }
                                        $currentDate->addDay();
                                    }
                                }
                                
                                $totalDaysInMonth = 0;
                                if ($periodStart <= $periodEnd) {
                                    $totalDaysInMonth = $periodStart->diffInDays($periodEnd) + 1 - $holidayCount;
                                }
                                
                                $attendances = $student->attendances->filter(function($a) use ($periodStart, $periodEnd) {
                                    $attDate = \Carbon\Carbon::parse($a->date);
                                    return $attDate->between($periodStart, $periodEnd);
                                });
                                $o = $attendances->where('status', 'present')->count();
                                $t = $attendances->where('status', 'late')->count();
                                $i_count = $attendances->filter(function($a) {
                                    return $a->status === 'excused' && strpos(strtolower($a->location), 'sakit') === false;
                                })->count();
                                $s = $attendances->filter(function($a) {
                                    return $a->status === 'excused' && strpos(strtolower($a->location), 'sakit') !== false;
                                })->count();
                                $recordedAbsent = $attendances->where('status', 'absent')->count();
                                
                                // Calculate total absent: days without any attendance (up to today, minus holidays) + recorded absent
                                $daysWithAttendance = $o + $t + $i_count + $s + $recordedAbsent;
                                $a = ($totalDaysInMonth - $daysWithAttendance) + $recordedAbsent;

                                $total_o += $o; $total_t += $t; $total_i += $i_count; $total_s += $s; $total_a += $a;
                            @endphp
                            <td class="p-2 text-center text-[11px] font-black text-white bg-emerald-500 border-r border-slate-50">{{ ($o + $t) ?: '-' }}</td>
                            <td class="p-2 text-center text-[11px] font-bold text-emerald-700 bg-emerald-50/10 border-r border-slate-50">{{ $o ?: '-' }}</td>
                            <td class="p-2 text-center text-[11px] font-bold text-amber-700 bg-amber-50/10 border-r border-slate-50">{{ $t ?: '-' }}</td>
                            <td class="p-2 text-center text-[11px] font-bold text-orange-700 bg-orange-50/10 border-r border-slate-50">{{ $i_count ?: '-' }}</td>
                            <td class="p-2 text-center text-[11px] font-bold text-orange-700 bg-orange-50/10 border-r border-slate-50">{{ $s ?: '-' }}</td>
                            <td class="p-2 text-center text-[11px] font-bold text-rose-700 bg-rose-50/10 border-r border-slate-100">{{ $a ?: '-' }}</td>
                        @endforeach
                        
                        <td class="p-2 text-center text-[11px] font-black text-white bg-emerald-600 border-r border-slate-200 border-l-2 border-slate-200">{{ ($total_o + $total_t) ?: '-' }}</td>
                        <td class="p-2 text-center text-[11px] font-black text-emerald-800 bg-emerald-100/20 border-r border-slate-200">{{ $total_o ?: '-' }}</td>
                        <td class="p-2 text-center text-[11px] font-black text-amber-800 bg-amber-100/20 border-r border-slate-200">{{ $total_t ?: '-' }}</td>
                        <td class="p-2 text-center text-[11px] font-black text-orange-800 bg-orange-100/20 border-r border-slate-200">{{ $total_i ?: '-' }}</td>
                        <td class="p-2 text-center text-[11px] font-black text-orange-800 bg-orange-100/20 border-r border-slate-200">{{ $total_s ?: '-' }}</td>
                        <td class="p-2 text-center text-[11px] font-black text-rose-800 bg-rose-100/20">{{ $total_a ?: '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
