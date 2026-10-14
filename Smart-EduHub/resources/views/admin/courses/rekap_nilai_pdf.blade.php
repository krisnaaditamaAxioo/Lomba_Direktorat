<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekap Nilai - {{ $course->title }}</title>
    <style>
        @page { margin: 24px 20px 55px 20px; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #0f172a; line-height: 1.4; }

        /* ── KOP / Header ── */
        .kop { border-bottom: 3px double #1e293b; padding-bottom: 10px; margin-bottom: 14px; }
        .kop-table { width: 100%; border-collapse: collapse; }
        .kop-table td { border: none; padding: 0; vertical-align: middle; }
        .logo { width: 105px; }
        .logo img { width: 100px; }
        .kop-right { padding-left: 8px; }
        .kop-inner { width: 100%; border-collapse: collapse; }
        .kop-inner td { border: none; padding: 0; vertical-align: top; }
        .kop-line1 { font-size: 15px; font-weight: 700; letter-spacing: 0.06em; color: #0f172a; }
        .kop-line2 { font-size: 11px; font-weight: 700; color: #334155; margin-top: 2px; }
        .kop-line3 { font-size: 10px; color: #475569; margin-top: 3px; }
        .kop-meta { text-align: right; font-size: 9px; color: #64748b; white-space: nowrap; vertical-align: bottom; }

        /* ── Title Section ── */
        .report-title { text-align: center; margin-bottom: 16px; }
        .report-title h1 { font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #0f172a; margin-bottom: 3px; }
        .report-title p { font-size: 10px; color: #475569; }

        /* ── Summary Boxes ── */
        .summary-wrap { margin-bottom: 16px; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { border: none; padding: 0; vertical-align: top; }
        .summary-box { border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; text-align: center; margin: 0 3px; }
        .summary-box .label { font-size: 8px; text-transform: uppercase; letter-spacing: 0.12em; color: #64748b; font-weight: 700; }
        .summary-box .value { font-size: 18px; font-weight: 700; color: #0f172a; margin-top: 2px; }
        .summary-box .sub { font-size: 8px; color: #94a3b8; margin-top: 1px; }
        .summary-box.primary { background: #eef2ff; border-color: #c7d2fe; }
        .summary-box.primary .value { color: #4338ca; }
        .summary-box.success { background: #f0fdf4; border-color: #bbf7d0; }
        .summary-box.success .value { color: #15803d; }
        .summary-box.warning { background: #fffbeb; border-color: #fde68a; }
        .summary-box.warning .value { color: #b45309; }
        .summary-box.info { background: #eff6ff; border-color: #bfdbfe; }
        .summary-box.info .value { color: #1d4ed8; }

        /* ── Main Table ── */
        table.main { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.main th, table.main td { border: 1px solid #cbd5e1; padding: 5px 6px; }
        table.main th {
            background: #f1f5f9;
            text-align: center;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #334155;
            font-weight: 700;
        }
        table.main td { vertical-align: middle; font-size: 9.5px; }
        table.main tbody tr:nth-child(even) { background: #f8fafc; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: 700; }

        /* Score cells */
        .score-cell { text-align: center; font-weight: 700; font-size: 9px; }
        .score-pct { font-size: 7.5px; color: #64748b; font-weight: 400; display: block; margin-top: 1px; }

        /* Average highlight */
        .avg-cell { text-align: center; font-weight: 700; font-size: 11px; }
        .avg-high { color: #15803d; }
        .avg-mid { color: #1d4ed8; }
        .avg-low { color: #b45309; }
        .avg-fail { color: #dc2626; }

        /* Predikat badges */
        .predikat { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 8px; font-weight: 700; letter-spacing: 0.04em; }
        .predikat-a { background: #dcfce7; color: #14532d; border: 1px solid #86efac; }
        .predikat-b { background: #dbeafe; color: #1e3a8a; border: 1px solid #93c5fd; }
        .predikat-c { background: #fef3c7; color: #78350f; border: 1px solid #fcd34d; }
        .predikat-d { background: #fee2e2; color: #7f1d1d; border: 1px solid #fca5a5; }

        /* Summary row */
        table.main tfoot td {
            background: #e2e8f0;
            font-weight: 700;
            font-size: 9px;
            border: 1px solid #94a3b8;
        }

        /* Keterangan */
        .keterangan { margin-top: 14px; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; }
        .keterangan h4 { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #334155; margin-bottom: 6px; }
        .keterangan-table { width: 100%; border-collapse: collapse; }
        .keterangan-table td { border: none; padding: 2px 6px; font-size: 9px; color: #475569; vertical-align: top; }

        /* Signature */
        .signature { margin-top: 30px; page-break-inside: avoid; }
        .signature-table { width: 100%; border-collapse: collapse; }
        .signature-table td { border: none; padding: 0; vertical-align: top; text-align: center; font-size: 10px; }
        .sig-line { border-bottom: 1px solid #0f172a; width: 180px; margin: 0 auto; }
        .sig-name { font-weight: 700; margin-top: 4px; }
        .sig-title { font-size: 9px; color: #475569; }

        /* ── Footer ── */
        .footer { position: fixed; left: 0; right: 0; bottom: -36px; border-top: 1px solid #cbd5e1; padding-top: 6px; font-size: 8px; color: #64748b; }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { border: none; padding: 0; }
    </style>
</head>
<body>
    {{-- ═══════════════ KOP SURAT ═══════════════ --}}
    <div class="kop">
        <table class="kop-table">
            <tr>
                <td class="logo">
                    @if(!empty($logoDataUri))
                        <img src="{{ $logoDataUri }}" alt="Logo">
                    @endif
                </td>
                <td class="kop-right">
                    <table class="kop-inner">
                        <tr>
                            <td>
                                <div class="kop-line1">AXIOO CLASS PROGRAM</div>
                                <div class="kop-line2">{{ $appName }}</div>
                                <div class="kop-line3">Sistem Informasi Manajemen Siswa Binaan</div>
                            </td>
                            <td class="kop-meta">Dicetak: {{ $generatedAt->format('d-m-Y H:i') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- ═══════════════ JUDUL LAPORAN ═══════════════ --}}
    <div class="report-title">
        <h1>Rekapitulasi Nilai Siswa</h1>
        <p>Kursus: <strong>{{ $course->title }}</strong> · Target: <strong>{{ $course->angkatan ? $course->angkatan->name : 'Semua Angkatan' }}</strong> · {{ $gradableModules->count() }} Modul</p>
    </div>

    {{-- ═══════════════ RINGKASAN STATISTIK ═══════════════ --}}
    <div class="summary-wrap">
        <table class="summary-table">
            <tr>
                <td style="width:25%; padding-right:4px;">
                    <div class="summary-box primary">
                        <div class="label">Rata-Rata Kursus</div>
                        <div class="value">{{ number_format($courseAverageScore, 1) }}%</div>
                        <div class="sub">{{ $totalCourseStudentsWithGrades }} siswa dinilai</div>
                    </div>
                </td>
                <td style="width:25%; padding:0 2px;">
                    <div class="summary-box success">
                        <div class="label">Nilai Tertinggi</div>
                        <div class="value">{{ number_format($highestScore, 1) }}%</div>
                        <div class="sub">{{ $highestStudent ? $highestStudent->nama_lengkap : '-' }}</div>
                    </div>
                </td>
                <td style="width:25%; padding:0 2px;">
                    <div class="summary-box warning">
                        <div class="label">Nilai Terendah</div>
                        <div class="value">{{ number_format($lowestScore, 1) }}%</div>
                        <div class="sub">{{ $lowestStudent ? $lowestStudent->nama_lengkap : '-' }}</div>
                    </div>
                </td>
                <td style="width:25%; padding-left:4px;">
                    <div class="summary-box info">
                        <div class="label">Ketuntasan (≥ 75)</div>
                        @php($passPct = $students->count() > 0 ? round(($passedCount / $students->count()) * 100, 1) : 0)
                        <div class="value">{{ $passPct }}%</div>
                        <div class="sub">{{ $passedCount }} / {{ $students->count() }} siswa</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ═══════════════ TABEL NILAI ═══════════════ --}}
    <table class="main">
        <thead>
            <tr>
                <th style="width:26px;">No</th>
                <th style="text-align:left; min-width:120px;">Nama Siswa</th>
                <th style="width:60px;">NISN</th>
                @foreach($gradableModules as $mod)
                    <th style="width:{{ max(55, 100 / max($gradableModules->count(), 1)) }}px;">
                        {{ Str::limit($mod->title, 18) }}<br>
                        <span style="font-weight:400; font-size:7px; text-transform:none; letter-spacing:0;">(Maks {{ $mod->questions->sum('score') ?: 100 }})</span>
                    </th>
                @endforeach
                <th style="width:55px;">Total</th>
                <th style="width:52px; background:#e0e7ff; color:#3730a3;">Rata-Rata</th>
                <th style="width:70px;">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $student)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-left font-bold" style="font-size:9px;">{{ $student->nama_lengkap }}</td>
                    <td class="text-center" style="font-size:8.5px; color:#64748b;">{{ $student->nisn ?: '-' }}</td>

                    @foreach($gradableModules as $mod)
                        @php($item = $student->scores_list[$mod->id] ?? null)
                        <td class="score-cell">
                            @if($item && $item['score'] !== null)
                                {{ number_format($item['score'], 0) }}/{{ $item['max_score'] }}
                                <span class="score-pct">{{ number_format($item['percentage'], 1) }}%</span>
                            @else
                                <span style="color:#cbd5e1;">—</span>
                            @endif
                        </td>
                    @endforeach

                    <td class="text-center font-bold" style="font-size:9px;">
                        {{ number_format($student->total_score, 0) }}<span style="color:#94a3b8; font-weight:400;">/{{ $student->total_max_score }}</span>
                    </td>

                    <td class="avg-cell {{ $student->average_score >= 85 ? 'avg-high' : ($student->average_score >= 75 ? 'avg-mid' : ($student->average_score >= 65 ? 'avg-low' : 'avg-fail')) }}" style="background:#f0f4ff;">
                        {{ number_format($student->average_score, 1) }}%
                    </td>

                    <td class="text-center">
                        @if($student->average_score >= 90)
                            <span class="predikat predikat-a">A</span>
                        @elseif($student->average_score >= 80)
                            <span class="predikat predikat-b">B</span>
                        @elseif($student->average_score >= 70)
                            <span class="predikat predikat-c">C</span>
                        @else
                            <span class="predikat predikat-d">D</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $gradableModules->count() + 5 }}" class="text-center" style="padding:20px; color:#94a3b8;">
                        Belum ada data siswa.
                    </td>
                </tr>
            @endforelse
        </tbody>

        @if($students->isNotEmpty() && $gradableModules->isNotEmpty())
        <tfoot>
            <tr>
                <td colspan="3" class="text-left font-bold" style="font-size:8px; text-transform:uppercase; letter-spacing:0.06em;">
                    Rata-Rata Kelas Per Modul
                </td>
                @foreach($gradableModules as $mod)
                    @php($modStat = $moduleSummary[$mod->id] ?? null)
                    <td class="text-center font-bold" style="font-size:9px; color:#4338ca;">
                        @if($modStat && $modStat['count'] > 0)
                            {{ number_format($modStat['avg_percentage'], 1) }}%
                            <span style="display:block; font-size:7px; color:#64748b; font-weight:400;">({{ $modStat['count'] }} siswa)</span>
                        @else
                            —
                        @endif
                    </td>
                @endforeach
                <td class="text-center" style="font-size:8px; color:#64748b;">SUMMARY</td>
                <td class="text-center font-bold" style="font-size:11px; color:#4338ca; background:#e0e7ff;">
                    {{ number_format($courseAverageScore, 1) }}%
                </td>
                <td class="text-center" style="font-size:8px; color:#64748b;">OVERALL</td>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- ═══════════════ KETERANGAN PREDIKAT ═══════════════ --}}
    <div class="keterangan">
        <h4>Keterangan Predikat</h4>
        <table class="keterangan-table">
            <tr>
                <td style="width:25%;"><span class="predikat predikat-a">A</span> Sangat Baik (≥ 90%)</td>
                <td style="width:25%;"><span class="predikat predikat-b">B</span> Baik (80% – 89%)</td>
                <td style="width:25%;"><span class="predikat predikat-c">C</span> Cukup (70% – 79%)</td>
                <td style="width:25%;"><span class="predikat predikat-d">D</span> Kurang (< 70%)</td>
            </tr>
        </table>
    </div>

    {{-- ═══════════════ TANDA TANGAN ═══════════════ --}}
    <div class="signature">
        <table class="signature-table">
            <tr>
                <td style="width:50%;">
                    <p style="font-size:9px; color:#475569;">Mengetahui,</p>
                    <p style="font-size:10px; font-weight:700; margin-top:2px;">Koordinator Program</p>
                    <br><br><br>
                    <div class="sig-line"></div>
                    <p class="sig-name">( ................................ )</p>
                    <p class="sig-title">NIP. ___________________</p>
                </td>
                <td style="width:50%;">
                    <p style="font-size:9px; color:#475569;">{{ $generatedAt->translatedFormat('d F Y') }}</p>
                    <p style="font-size:10px; font-weight:700; margin-top:2px;">Instruktur / Guru Pembimbing</p>
                    <br><br><br>
                    <div class="sig-line"></div>
                    <p class="sig-name">( ................................ )</p>
                    <p class="sig-title">NIP. ___________________</p>
                </td>
            </tr>
        </table>
    </div>

    {{-- ═══════════════ FOOTER ═══════════════ --}}
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>{{ $appName }} • Rekapitulasi Nilai Kursus: {{ $course->title }}</td>
                <td style="text-align:right;">Halaman <span class="pagenum"></span> • Dicetak {{ $generatedAt->format('d-m-Y H:i') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
