<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Laporan Absensi Bulanan - {{ $angkatan ?? 'Semua Angkatan' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-container { width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
            table { border-collapse: collapse !important; width: 100% !important; font-size: 9px !important; }
            th, td { border: 1px solid #000 !important; padding: 2px !important; }
            @page { size: landscape; margin: 1cm; }
        }
        body { font-family: 'Inter', sans-serif; }
        .attendance-cell { width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; margin: auto; font-weight: bold; border-radius: 2px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <!-- Action Bar -->
    <div class="no-print sticky top-0 z-50 bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center shadow-sm">
        <div class="flex items-center gap-4">
            <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 bg-orange-600 text-white rounded-xl font-bold text-sm hover:bg-orange-700 transition-all">
                <span class="material-symbols-outlined text-[20px]">print</span>
                Cetak Laporan
            </button>
            <button onclick="window.close()" class="flex items-center gap-2 px-4 py-2 border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all">
                Tutup Pratinjau
            </button>
        </div>
        <div class="text-slate-400 font-bold text-xs uppercase tracking-widest">Mode Pratinjau Cetak</div>
    </div>

    <!-- Main Print Container -->
    <main class="print-container max-w-[1200px] mx-auto bg-white p-12 my-8 shadow-lg print:shadow-none print:my-0">
        <!-- Institutional Header -->
        <header class="flex items-center justify-between border-b-4 border-slate-900 pb-6 mb-8">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-orange-600 flex items-center justify-center rounded-xl">
                    <span class="material-symbols-outlined text-[40px] text-white">school</span>
                </div>
                <div>
                    <h1 class="text-2xl font-black uppercase tracking-tight text-slate-900">{{ \App\Models\Setting::get('school_name', 'SMART EDUHUB CLASS PROGRAM') }}</h1>
                    <p class="text-xs font-medium text-slate-500">{{ \App\Models\Setting::get('school_address', 'Jl. Raya Utama No. 123, Indonesia') }}</p>
                    <p class="text-xs font-medium text-slate-500">Telp: {{ \App\Models\Setting::get('school_phone', '-') }} | Email: {{ \App\Models\Setting::get('school_email', '-') }}</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-lg font-black text-orange-600">LAPORAN ABSENSI BULANAN</h2>
                <p class="text-[10px] font-bold mt-1 uppercase text-slate-400">Tanggal Cetak: {{ date('d F Y') }}</p>
            </div>
        </header>

        <!-- Report Details -->
        <section class="grid grid-cols-2 gap-8 mb-8 bg-slate-50 p-6 rounded-2xl border border-slate-100 print:bg-transparent print:border-none print:p-0">
            <div class="space-y-1">
                <div class="flex text-xs">
                    <span class="w-32 font-bold text-slate-400 uppercase tracking-widest">Periode</span>
                    <span class="font-bold text-slate-900">: {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}</span>
                </div>
                <div class="flex text-xs">
                    <span class="w-32 font-bold text-slate-400 uppercase tracking-widest">Angkatan</span>
                    <span class="font-bold text-slate-900">: {{ $angkatan ?? 'Semua Angkatan' }}</span>
                </div>
            </div>
            <div class="space-y-1 text-right">
                <div class="flex justify-end text-xs">
                    <span class="w-32 font-bold text-slate-400 uppercase tracking-widest text-left">Jumlah Siswa</span>
                    <span class="font-bold text-slate-900">: {{ $students->count() }} Siswa</span>
                </div>
                <div class="flex justify-end text-xs">
                    <span class="w-32 font-bold text-slate-400 uppercase tracking-widest text-left">Hari Terpilih</span>
                    <span class="font-bold text-slate-900">: {{ count($dates) }} Hari</span>
                </div>
            </div>
        </section>

        <!-- Attendance Grid -->
        <div class="mb-10 overflow-hidden">
            <table class="w-full text-left border-collapse border border-slate-900">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="border border-slate-900 p-1 text-center font-bold text-[9px] w-6" rowspan="2">No</th>
                        <th class="border border-slate-900 p-1 font-bold text-[9px]" rowspan="2">Nama Lengkap Siswa</th>
                        <th class="border border-slate-900 p-1 text-center font-bold text-[9px]" colspan="{{ count($dates) }}">Tanggal</th>
                        <th class="border border-slate-900 p-1 text-center font-bold text-[9px]" colspan="6">Rekap</th>
                    </tr>
                    <tr class="bg-slate-50">
                        @foreach($dates as $date)
                            <th class="border border-slate-900 p-0.5 text-center text-[8px] w-5">
                                {{ \Carbon\Carbon::parse($date)->format('d') }}
                            </th>
                        @endforeach
                        <th class="border border-slate-900 p-0.5 text-center text-[8px] w-5">H</th>
                        <th class="border border-slate-900 p-0.5 text-center text-[8px] w-5">O</th>
                        <th class="border border-slate-900 p-0.5 text-center text-[8px] w-5">T</th>
                        <th class="border border-slate-900 p-0.5 text-center text-[8px] w-5">I</th>
                        <th class="border border-slate-900 p-0.5 text-center text-[8px] w-5">S</th>
                        <th class="border border-slate-900 p-0.5 text-center text-[8px] w-5">A</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $index => $student)
                    <tr>
                        <td class="border border-slate-900 p-1 text-center text-[9px]">{{ $index + 1 }}</td>
                        <td class="border border-slate-900 p-1 text-[9px] font-medium uppercase">{{ $student->nama_lengkap }}</td>
                        
                        @php $o=0; $t=0; $i_count=0; $s=0; $a=0; @endphp
                        
                        @foreach($dates as $date)
                            @php
                                $attendance = $student->attendances->where('date', $date)->first();
                                $isHoliday = \App\Models\Holiday::checkHoliday($date);
                                $char = '';
                                $isFutureDate = \Carbon\Carbon::parse($date)->isFuture();
                                
                                if($isHoliday) {
                                    $char = 'L';
                                } elseif($attendance) {
                                    if($attendance->status === 'present') { $char = 'O'; $o++; }
                                    elseif($attendance->status === 'late') { $char = 'T'; $t++; }
                                    elseif($attendance->status === 'excused') {
                                        if(strpos(strtolower($attendance->location), 'sakit') !== false) { $char = 'S'; $s++; }
                                        else { $char = 'I'; $i_count++; }
                                    }
                                    elseif($attendance->status === 'absent') { $char = 'A'; $a++; }
                                } else if(!$isFutureDate) {
                                    $char = 'A'; $a++;
                                }
                            @endphp
                            <td class="border border-slate-900 p-0 text-center text-[8px] font-bold">{{ $char }}</td>
                        @endforeach
                        
                        <td class="border border-slate-900 p-1 text-center text-[9px] font-bold bg-emerald-500 text-white">{{ $o + $t }}</td>
                        <td class="border border-slate-900 p-1 text-center text-[9px] font-bold bg-slate-50">{{ $o }}</td>
                        <td class="border border-slate-900 p-1 text-center text-[9px] font-bold bg-slate-50">{{ $t }}</td>
                        <td class="border border-slate-900 p-1 text-center text-[9px] font-bold bg-slate-50">{{ $i_count }}</td>
                        <td class="border border-slate-900 p-1 text-center text-[9px] font-bold bg-slate-50">{{ $s }}</td>
                        <td class="border border-slate-900 p-1 text-center text-[9px] font-bold bg-slate-50">{{ $a }}</td>
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
                <p class="text-[9px]">NIP. {{ $headmaster_nip }}</p>
            </div>
            <div></div>
            <div class="text-center">
                <p class="text-xs mb-16">{{ \Carbon\Carbon::parse($end_date)->format('d F Y') }}<br/>Admin Akademik</p>
                <div class="w-40 border-b border-slate-900 mx-auto mb-1"></div>
                <p class="text-[10px] font-bold uppercase">{{ $headmaster }}</p>
                <p class="text-[9px]">NIP. {{ $headmaster_nip }}</p>
            </div>
        </section>
    </main>
</body>
</html>
