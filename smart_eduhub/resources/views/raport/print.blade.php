<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raport Industri - {{ $raport->student->nama_lengkap ?? 'Siswa' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            background-color: #f8fafc;
            color: #000000;
        }

        @page {
            size: A4 portrait;
            margin: 10mm 15mm;
        }

        .page-break {
            page-break-before: always;
            break-before: page;
        }

        /* Container Watermark */
        .watermark-container {
            position: relative;
            overflow: hidden;
        }

        /* Teks Watermark di Lapisan Paling Belakang */
        .watermark-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 4rem;
            font-weight: 900;
            color: rgba(0, 0, 0, 0.06);
            white-space: nowrap;
            pointer-events: none;
            user-select: none;
            z-index: 0;
            text-transform: uppercase;
            letter-spacing: 12px;
        }

        /* Memastikan tabel transparan agar watermark tembus/terlihat */
        .table-transparent {
            background-color: rgba(255, 255, 255, 0.75) !important;
        }

        .table-header-transparent {
            background-color: rgba(241, 245, 249, 0.85) !important;
        }

        @media print {
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-page {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                min-height: 100vh !important;
            }
            .watermark-text {
                color: rgba(0, 0, 0, 0.08) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body class="py-8 px-4 sm:px-6">

    <!-- Action Bar (No Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex items-center justify-between bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <a href="{{ route('raport.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-xl transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar
        </a>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl shadow-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak / Download PDF
            </button>
        </div>
    </div>

    <!-- Printable Paper Area: Halaman 1 (Raport Hasil Belajar) -->
    <div class="print-page watermark-container max-w-4xl mx-auto bg-white p-8 sm:p-10 rounded-2xl border border-slate-300 shadow-xl flex flex-col justify-between min-h-[1050px]">
        
        <!-- Watermark (Layer Belakang z-0) -->
        <div class="watermark-text">DOKUMEN LEGAL</div>

        <!-- Layer Konten Utama (z-10 di atas watermark) -->
        <div class="relative z-10">
            <!-- Header Raport dengan Logo di Sebelah Kiri -->
           <div class="pb-3 border-b-2 border-black mb-6 flex items-center justify-between gap-5">
    <div>
        <h2 class="text-xs font-black uppercase tracking-[3px] text-black">AXIOO CLASS PROGRAM</h2>
        <h1 class="text-xl font-extrabold text-black leading-tight">CAPAIAN HASIL BELAJAR KELAS INDUSTRI</h1>
        <p class="text-xs font-medium text-slate-700">Laporan Evaluasi Perkembangan Kompetensi & Sertifikasi Siswa</p>
    </div>
    <img src="{{ asset('images/axioo.png') }}" alt="Axioo Logo" class="h-10 w-auto object-contain shrink-0">
</div>

            <!-- Identitas Siswa -->
            <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-xs p-4 mb-6 bg-slate-50/70 border border-slate-200 rounded-lg">
                <div class="flex items-center">
                    <span class="w-36  text-black uppercase tracking-wider">Nama Siswa</span>
                    <span class=" text-black text-sm">: {{ $raport->student->nama_lengkap ?? '-' }}</span>
                </div>
                <div class="flex items-center">
                    <span class="w-36  text-black uppercase tracking-wider">Tingkat / Kelas</span>
                    <span class=" text-black">: {{ $raport->tingkat ?? '-' }}</span>
                </div>
                <div class="flex items-center">
                    <span class="w-36  text-black uppercase tracking-wider">NIS / NISN</span>
                    <span class=" text-black">: {{ $raport->student->nis_siswa ?? '-' }} / {{ $raport->student->nisn ?? '-' }}</span>
                </div>
                <div class="flex items-center">
                    <span class="w-36  text-black uppercase tracking-wider">Program Keahlian</span>
                    <span class=" text-black">: {{ $raport->program_keahlian ?? ($raport->angkatan->nama_angkatan ?? '-') }}</span>
                </div>
                <div class="flex items-center">
                    <span class="w-36  text-black uppercase tracking-wider">Tahun Pelajaran</span>
                    <span class=" text-black">: {{ $raport->tahun_pelajaran }}</span>
                </div>
            </div>

            <!-- Tabel A: Penilaian Capaian Pembelajaran -->
            <div class="mb-6">
                <h3 class="text-xs font-bold uppercase tracking-wider text-black mb-2 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-black"></span>
                    A. PENILAIAN CAPAIAN PEMBELAJARAN
                </h3>
                <table class="w-full border-collapse text-xs border border-black table-transparent">
                    <thead>
                        <tr class="table-header-transparent text-black border-b border-black">
                            <th class="border border-black px-3 py-2 text-center font-bold w-10">NO</th>
                            <th class="border border-black px-4 py-2 text-left font-bold w-64">ASPEK PENILAIAN</th>
                            <th class="border border-black px-3 py-2 text-center font-bold w-24">PREDIKAT</th>
                            <th class="border border-black px-4 py-2 text-left font-bold">KETERANGAN / DESKRIPSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($raport->capaianPembelajaran as $aspek)
                        <tr>
                            <td class="border border-black px-3 py-2 text-center  text-black">{{ $aspek->no }}</td>
                            <td class="border border-black px-4 py-2  text-black">{{ $aspek->aspek_penilaian }}</td>
                            <td class="border border-black px-3 py-2 text-center  text-black">{{ $aspek->predikat }}</td>
                            <td class="border border-black px-4 py-2 text-black leading-relaxed font-normal">{{ $aspek->keterangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="border border-black px-4 py-3 text-center text-slate-600 font-medium">Belum ada aspek penilaian yang dimasukkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Tabel B: Uji Sertifikasi -->
            <div class="mb-6">
                <h3 class="text-xs font-bold uppercase tracking-wider text-black mb-2 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-black"></span>
                    B. UJI SERTIFIKASI KELAS INDUSTRI
                </h3>
                <table class="w-full border-collapse text-xs border border-black table-transparent">
                    <thead>
                        <tr class="table-header-transparent text-black border-b border-black">
                            <th class="border border-black px-3 py-2 text-center font-bold w-10">NO</th>
                            <th class="border border-black px-4 py-2 text-left font-bold">NAMA UJIAN / SERTIFIKASI</th>
                            <th class="border border-black px-4 py-2 text-left font-bold w-48">NOMOR SERTIFIKAT</th>
                            <th class="border border-black px-3 py-2 text-center font-bold w-32">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($raport->ujiSertifikasi as $ujian)
                        <tr>
                            <td class="border border-black px-3 py-2 text-center  text-black">{{ $ujian->no }}</td>
                            <td class="border border-black px-4 py-2 text-black">{{ $ujian->nama_ujian }}</td>
                            <td class="border border-black px-4 py-2  text-black">{{ $ujian->no_serti ?? '-' }}</td>
                            <td class="border border-black px-3 py-2 text-center">
                                {{ $ujian->status === 'LULUS' ? 'LULUS' : 'TIDAK LULUS' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="border border-black px-4 py-3 text-center text-slate-600 font-medium">Belum ada sertifikasi yang didaftarkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Catatan Evaluasi -->
            <div class="mb-8">
                <h3 class="text-xs font-bold uppercase tracking-wider text-black mb-2 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-black"></span>
                    C. CATATAN & EVALUASI
                </h3>
                <div class="border border-black p-3.5 bg-slate-50/70 text-xs text-black leading-relaxed min-h-[60px]">
                    {{ $raport->catatan ?: 'Tidak ada catatan khusus.' }}
                </div>
            </div>

            <!-- Tanda Tangan Halaman 1 -->
            @php
                $trainerName = $raport->trainer_name ?: \App\Models\Setting::get('trainer_name', '');
                $trainerNoSerti = $raport->trainer_no_serti ?: \App\Models\Setting::get('trainer_no_serti', '');
                $trainerNbm = $raport->trainer_nbm ?: \App\Models\Setting::get('trainer_nbm', '');
            @endphp
            <div class="grid grid-cols-2 gap-8 text-xs pt-4">
                <div class="text-center flex flex-col justify-between h-36">
                    <div>
                        <p class="font-bold text-black uppercase tracking-wider text-[11px]">Trainer Axioo Class Program</p>
                        <p class="text-slate-600 text-[10px]">Axioo Class Program</p>
                    </div>
                    <div>
                        <p class="font-extrabold text-black text-sm underline">{{ $trainerName ?: '( ................................... )' }}</p>
                        <p class="text-black text-[11px] font-mono mt-0.5">No. Serti: {{ $trainerNoSerti ?: '-' }}</p>
                        @if($trainerNbm)
                        <p class="text-black text-[10px] font-mono">NBM: {{ $trainerNbm }}</p>
                        @endif
                    </div>
                </div>

                <div class="text-center flex flex-col justify-between h-36">
                    <div>
                        <p class="font-bold text-black uppercase tracking-wider text-[11px]">Wali Kelas</p>
                        <p class="text-slate-600 text-[10px]">Angkatan {{ $raport->angkatan->nama_angkatan ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="font-extrabold text-black text-sm underline">{{ $raport->wali_kelas->name ?? '( ................................... )' }}</p>
                        <p class="text-black text-[11px] font-mono mt-0.5">NIP / NBM: {{ $raport->wali_kelas->nip ?? $raport->wali_kelas->nbm ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Halaman 1 -->
        <div class="relative z-10 pt-4 border-t border-slate-300 mt-8 flex items-center justify-between text-[10px] text-slate-600 font-mono">
            <span>Raport Kelas Industri — {{ now()->translatedFormat('d F Y') }}</span>
            <span class="font-semibold uppercase tracking-wider">Axioo Class Program</span>
        </div>
    </div>

    <!-- Printable Paper Area: Halaman 2 (Point Kedisiplinan & Penghargaan) -->
    <div class="print-page page-break watermark-container max-w-4xl mx-auto bg-white p-8 sm:p-10 rounded-2xl border border-slate-300 shadow-xl mt-8 flex flex-col justify-between min-h-[1050px]">
        
        <!-- Watermark (Layer Belakang z-0) -->
        <div class="watermark-text">DOKUMEN LEGAL</div>

        <!-- Layer Konten Utama (z-10 di atas watermark) -->
        <div class="relative z-10">
            <!-- Header Halaman 2 dengan Logo di Sebelah Kiri -->
            <div class="pb-3 border-b-2 border-black mb-6 flex items-center justify-between gap-5">
    <div>
        <h2 class="text-xs font-black uppercase tracking-[3px] text-black">AXIOO CLASS PROGRAM</h2>
        <h1 class="text-xl font-extrabold text-black leading-tight">REKAP POIN PELANGGARAN & PENGHARGAAN</h1>
        <p class="text-xs font-medium text-slate-700">Catatan Kedisiplinan & Prestasi Siswa — {{ $raport->student->nama_lengkap ?? '-' }} (NIS: {{ $raport->student->nis_siswa ?? '-' }})</p>
    </div>
    <img src="{{ asset('images/axioo.png') }}" alt="Axioo Logo" class="h-10 w-auto object-contain shrink-0">
</div>

            <!-- Ringkasan Total Poin -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="p-3.5 rounded-lg border border-black bg-slate-50/70 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-black uppercase tracking-wider">Total Poin Pelanggaran</p>
                        <p class="text-2xl font-black text-black mt-0.5">{{ $totalViolationScore }} <span class="text-xs font-bold text-slate-700">Poin</span></p>
                    </div>
                    <div class="w-9 h-9 rounded-lg border border-black flex items-center justify-center text-black font-bold text-sm bg-white/80">
                        {{ $violations->count() }}
                    </div>
                </div>
                <div class="p-3.5 rounded-lg border border-black bg-slate-50/70 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-black uppercase tracking-wider">Total Poin Penghargaan</p>
                        <p class="text-2xl font-black text-black mt-0.5">{{ $totalAchievementScore }} <span class="text-xs font-bold text-slate-700">Poin</span></p>
                    </div>
                    <div class="w-9 h-9 rounded-lg border border-black flex items-center justify-center text-black font-bold text-sm bg-white/80">
                        {{ $achievements->count() }}
                    </div>
                </div>
            </div>

            <!-- Tabel C: Catatan Pelanggaran -->
            <div class="mb-6">
                <h3 class="text-xs font-bold uppercase tracking-wider text-black mb-2 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-black"></span>
                    C. CATATAN PELANGGARAN
                </h3>
                <table class="w-full border-collapse text-xs border border-black table-transparent">
                    <thead>
                        <tr class="table-header-transparent text-black border-b border-black">
                            <th class="border border-black px-3 py-2 text-center font-bold w-10">NO</th>
                            <th class="border border-black px-4 py-2 text-center font-bold w-32">TANGGAL</th>
                            <th class="border border-black px-4 py-2 text-left font-bold">DESKRIPSI PELANGGARAN</th>
                            <th class="border border-black px-3 py-2 text-center font-bold w-20">POIN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($violations as $v)
                        <tr>
                            <td class="border border-black px-3 py-2 text-center text-black">{{ $loop->iteration }}</td>
                            <td class="border border-black px-4 py-2 text-center text-black font-mono">{{ $v->date ? \Carbon\Carbon::parse($v->date)->format('d/m/Y') : '-' }}</td>
                            <td class="border border-black px-4 py-2 text-black">{{ $v->reason }}</td>
                            <td class="border border-black px-3 py-2 text-center font-bold text-black">+{{ $v->score }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="border border-black px-4 py-3 text-center text-slate-600 font-medium">Tidak ada catatan pelanggaran (Siswa Tertib).</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Tabel D: Catatan Penghargaan -->
            <div class="mb-8">
                <h3 class="text-xs font-bold uppercase tracking-wider text-black mb-2 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-black"></span>
                    D. CATATAN PENGHARGAAN & PRESTASI
                </h3>
                <table class="w-full border-collapse text-xs border border-black table-transparent">
                    <thead>
                        <tr class="table-header-transparent text-black border-b border-black">
                            <th class="border border-black px-3 py-2 text-center font-bold w-10">NO</th>
                            <th class="border border-black px-4 py-2 text-center font-bold w-32">TANGGAL</th>
                            <th class="border border-black px-4 py-2 text-left font-bold">DESKRIPSI PENGHARGAAN</th>
                            <th class="border border-black px-3 py-2 text-center font-bold w-20">POIN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($achievements as $a)
                        <tr>
                            <td class="border border-black px-3 py-2 text-center font-semibold text-black">{{ $loop->iteration }}</td>
                            <td class="border border-black px-4 py-2 text-center text-black font-mono">{{ $a->date ? \Carbon\Carbon::parse($a->date)->format('d/m/Y') : '-' }}</td>
                            <td class="border border-black px-4 py-2 font-semibold text-black">{{ $a->reason }}</td>
                            <td class="border border-black px-3 py-2 text-center font-bold text-black">+{{ $a->score }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="border border-black px-4 py-3 text-center text-slate-600 font-medium">Belum ada catatan penghargaan/prestasi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Tanda Tangan Halaman 2 -->
            <div class="grid grid-cols-2 gap-8 text-xs pt-4">
                <div class="text-center flex flex-col justify-between h-32">
                    <p class="font-bold text-black uppercase tracking-wider text-[11px]">Guru BK / Kesiswaan</p>
                    <p class="font-extrabold text-black text-sm underline">( ................................... )</p>
                </div>
                <div class="text-center flex flex-col justify-between h-32">
                    <p class="font-bold text-black uppercase tracking-wider text-[11px]">Wali Kelas</p>
                    <p class="font-extrabold text-black text-sm underline">{{ $raport->wali_kelas->name ?? '( ................................... )' }}</p>
                </div>
            </div>
        </div>

        <!-- Footer Halaman 2 -->
        <div class="relative z-10 pt-4 border-t border-slate-300 mt-8 flex items-center justify-between text-[10px] text-slate-600 font-mono">
            <span>Raport Kelas Industri — {{ now()->translatedFormat('d F Y') }}</span>
            <span class="font-semibold uppercase tracking-wider">Axioo Class Program</span>
        </div>
    </div>

</body>
</html>