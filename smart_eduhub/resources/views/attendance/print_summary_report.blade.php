<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Ringkasan Laporan Absensi Bulanan - {{ $angkatan ? 'Angkatan ' . $angkatan : 'Semua Angkatan' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-container { width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
            table { border-collapse: collapse !important; width: 100% !important; font-size: 9px !important; }
            th, td { border: 1px solid #000 !important; padding: 4px !important; }
            @page { size: landscape; margin: 1cm; }
        }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <!-- Action Bar -->
    <div class="no-print sticky top-0 z-50 bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center shadow-sm">
        <div class="flex items-center gap-4">
            <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 bg-orange-600 text-white rounded-xl font-bold text-sm hover:bg-orange-700 transition-all">
                <span class="material-symbols-outlined text-[20px]">print</span>
                Cetak Ringkasan
            </button>
            <button onclick="window.close()" class="flex items-center gap-2 px-4 py-2 border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all">
                Tutup Pratinjau
            </button>
        </div>
        <div class="text-slate-400 font-bold text-xs uppercase tracking-widest">Mode Ringkasan Bulanan</div>
    </div>

    <!-- Main Print Container -->
    <main class="print-container max-w-[1200px] mx-auto bg-white p-12 my-8 shadow-lg print:shadow-none print:my-0">
        <!-- Institutional Header (Reuse) -->
        <header class="flex items-center justify-between border-b-4 border-slate-900 pb-6 mb-8">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-orange-600 flex items-center justify-center rounded-xl">
                    <span class="material-symbols-outlined text-[40px] text-white">school</span>
                </div>
                <div>
                    <h1 class="text-2xl font-black uppercase tracking-tight text-slate-900">{{ \App\Models\Setting::get('school_name', 'SMART AXIOO CLASS PROGRAM') }}</h1>
                    <p class="text-xs font-medium text-slate-500">{{ \App\Models\Setting::get('school_address', 'Jl. Raya Utama No. 123, Indonesia') }}</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-lg font-black text-orange-600">RINGKASAN ABSENSI BULANAN</h2>
                <p class="text-[10px] font-bold mt-1 uppercase text-slate-400">Periode: {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}</p>
            </div>
        </header>

        <!-- Report Details -->
        <section class="grid grid-cols-2 gap-8 mb-8">
            <div class="space-y-1">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Angkatan: <span class="text-slate-900 ml-2">{{ $angkatan ? 'Angkatan ' . $angkatan : 'Semua Angkatan' }}</span></p>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Penandatangan: <span class="text-slate-900 ml-2">{{ $headmaster }}</span></p>
            </div>
            <div class="text-right">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Keterangan:</p>
                <p class="text-[9px] text-slate-500 font-bold mt-1">H=Hadir (Ontime+Telat), O=Ontime, T=Telat, I=Izin, S=Sakit, A=Alpa</p>
            </div>
        </section>

        <!-- Attendance Grid -->
        <div class="mb-10">
            <table class="w-full text-left border-collapse border border-slate-900">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="border border-slate-900 p-2 text-center font-bold text-[10px] w-8" rowspan="2">No</th>
                        <th class="border border-slate-900 p-2 font-bold text-[10px]" rowspan="2">Nama Lengkap Siswa</th>
                        @foreach($months as $month)
                            <th class="border border-slate-900 p-2 text-center font-bold text-[10px]" colspan="6">{{ $month['name'] }}</th>
                        @endforeach
                        <th class="border border-slate-900 p-2 text-center font-bold text-[10px]" colspan="6">Total</th>
                    </tr>
                    <tr class="bg-slate-50">
                        @foreach($months as $month)
                            <th class="border border-slate-900 p-1 text-center text-[9px] w-6 bg-emerald-500 text-white">H</th>
                            <th class="border border-slate-900 p-1 text-center text-[9px] w-6">O</th>
                            <th class="border border-slate-900 p-1 text-center text-[9px] w-6">T</th>
                            <th class="border border-slate-900 p-1 text-center text-[9px] w-6">I</th>
                            <th class="border border-slate-900 p-1 text-center text-[9px] w-6">S</th>
                            <th class="border border-slate-900 p-1 text-center text-[9px] w-6">A</th>
                        @endforeach
                        <th class="border border-slate-900 p-1 text-center text-[9px] w-6 bg-emerald-600 text-white">H</th>
                        <th class="border border-slate-900 p-1 text-center text-[9px] w-6">O</th>
                        <th class="border border-slate-900 p-1 text-center text-[9px] w-6">T</th>
                        <th class="border border-slate-900 p-1 text-center text-[9px] w-6">I</th>
                        <th class="border border-slate-900 p-1 text-center text-[9px] w-6">S</th>
                        <th class="border border-slate-900 p-1 text-center text-[9px] w-6">A</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $index => $student)
                    <tr>
                        <td class="border border-slate-900 p-2 text-center text-[10px]">{{ $index + 1 }}</td>
                        <td class="border border-slate-900 p-2 text-[10px] font-bold uppercase">{{ $student->nama_lengkap }}</td>
                        
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
                            <td class="border border-slate-900 p-1 text-center text-[10px] font-black bg-emerald-500 text-white">{{ ($o + $t) ?: '-' }}</td>
                            <td class="border border-slate-900 p-1 text-center text-[10px]">{{ $o ?: '-' }}</td>
                            <td class="border border-slate-900 p-1 text-center text-[10px]">{{ $t ?: '-' }}</td>
                            <td class="border border-slate-900 p-1 text-center text-[10px]">{{ $i_count ?: '-' }}</td>
                            <td class="border border-slate-900 p-1 text-center text-[10px]">{{ $s ?: '-' }}</td>
                            <td class="border border-slate-900 p-1 text-center text-[10px]">{{ $a ?: '-' }}</td>
                        @endforeach
                        
                        <td class="border border-slate-900 p-1 text-center text-[10px] font-black bg-emerald-600 text-white">{{ ($total_o + $total_t) ?: '-' }}</td>
                        <td class="border border-slate-900 p-1 text-center text-[10px] font-bold bg-slate-50">{{ $total_o ?: '-' }}</td>
                        <td class="border border-slate-900 p-1 text-center text-[10px] font-bold bg-slate-50">{{ $total_t ?: '-' }}</td>
                        <td class="border border-slate-900 p-1 text-center text-[10px] font-bold bg-slate-50">{{ $total_i ?: '-' }}</td>
                        <td class="border border-slate-900 p-1 text-center text-[10px] font-bold bg-slate-50">{{ $total_s ?: '-' }}</td>
                        <td class="border border-slate-900 p-1 text-center text-[10px] font-bold bg-slate-50">{{ $total_a ?: '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Signature Section -->
        <section class="grid grid-cols-3 gap-12 mt-12">
            <div class="text-center">
                <p class="text-xs mb-16">Mengetahui,<br/>Kepala Sekolah</p>
                <div class="w-40 border-b border-slate-900 mx-auto mb-1"></div>
                <p class="text-[10px] font-bold uppercase">{{ $headmaster }}</p>
            </div>
            <div></div>
            <div class="text-center">
                <p class="text-xs mb-16">{{ \Carbon\Carbon::parse($end_date)->format('d F Y') }}<br/>Admin Akademik</p>
                <div class="w-40 border-b border-slate-900 mx-auto mb-1"></div>
                <p class="text-[10px] font-bold uppercase">{{ $headmaster }}</p>
            </div>
        </section>
    </main>
</body>
</html>
