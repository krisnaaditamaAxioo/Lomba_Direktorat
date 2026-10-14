<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekap Nilai - {{ $student->nama_lengkap }}</title>
    <style>
        :root { --ink:#172033; --muted:#64748b; --line:#dbe3ee; --blue:#1d4ed8; --brand-soft:#eff6ff; }
        * { box-sizing:border-box; }
        body { margin:0; background:#eef2f7; color:var(--ink); font-family:Arial, Helvetica, sans-serif; font-size:12px; }
        .page { max-width:980px; margin:28px auto; background:#fff; padding:34px 38px 42px; box-shadow:0 12px 35px rgba(15,23,42,.08); }
        .toolbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:22px; color:var(--muted); font-size:12px; }
        button { border:0; border-radius:8px; padding:10px 16px; background:var(--blue); color:#fff; font-weight:700; cursor:pointer; }
        .letterhead { display:flex; align-items:center; gap:18px; padding-bottom:18px; border-bottom:3px solid var(--blue); }
        .logo { width:70px; height:70px; object-fit:contain; }
        .school-name { margin:0; font-size:19px; letter-spacing:.4px; text-transform:uppercase; }
        .school-address { margin:6px 0 0; color:var(--muted); font-size:11px; }
        .title-block { text-align:center; margin:26px 0 20px; }
        .title-block h1 { margin:0; font-size:18px; letter-spacing:1.2px; }
        .title-block p { margin:8px 0 0; color:var(--blue); font-size:14px; font-weight:700; }
        .meta-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:18px; }
        .meta-item { padding:11px 13px; background:#f8fafc; border:1px solid var(--line); border-radius:8px; }
        .meta-label { display:block; margin-bottom:5px; color:var(--muted); font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.7px; }
        .meta-value { font-weight:700; }
        .summary { display:flex; justify-content:space-between; align-items:center; padding:15px 18px; margin-bottom:20px; background:var(--brand-soft); border:1px solid #bfdbfe; border-radius:10px; }
        .summary-label { color:#8A1E1E; font-weight:700; }
        .summary-value { color:var(--blue); font-size:22px; font-weight:800; }
        table { width:100%; border-collapse:collapse; table-layout:fixed; }
        th, td { border:1px solid var(--line); padding:9px 7px; font-size:11px; text-align:left; overflow-wrap:anywhere; word-break:break-word; }
        th { background:#eaf1fb; color:#334155; font-size:9px; letter-spacing:.5px; text-transform:uppercase; }
        tbody tr:nth-child(even) { background:#f8fafc; }
        th:first-child, td:first-child { width:34px; } th:nth-child(4), td:nth-child(4), th:nth-child(5), td:nth-child(5) { width:58px; } th:nth-child(6), td:nth-child(6) { width:78px; } th:last-child, td:last-child { width:105px; }
        td.center { text-align:center; }
        td.score { text-align:center; font-weight:700; }
        .status { color:#166534; font-weight:700; }
        .pending { color:#b45309; font-weight:700; }
        .signature { display:flex; justify-content:flex-end; margin-top:48px; text-align:center; }
        .signature-box { width:210px; }.signature-space { height:52px; }.signature-name { border-top:1px solid var(--ink); padding-top:7px; font-weight:700; }
        @media (max-width:700px) { .page { margin:0; padding:20px; box-shadow:none; } .meta-grid { grid-template-columns:1fr; } }
        @page { size: A4 portrait; margin: 10mm; }
        @media print { body { background:#fff; } .page { max-width:none; margin:0; padding:0; box-shadow:none; } .toolbar { display:none; } .letterhead, .title-block, .meta-grid, .summary { break-inside:avoid; } thead { display:table-header-group; } tr { break-inside:avoid; } .summary { -webkit-print-color-adjust:exact; print-color-adjust:exact; } }
    </style>
</head>
<body>
<main class="page">
    <div class="toolbar"><span>Pratinjau dokumen rekap nilai</span><button onclick="window.print()">Cetak Dokumen</button></div>
    <header class="letterhead">
        <img class="logo" src="{{ asset('images/SEH.png') }}" alt="Logo sekolah">
        <div><h1 class="school-name">{{ \App\Models\Setting::get('school_name', 'SMART AXIOO CLASS PROGRAM') }}</h1><p class="school-address">{{ \App\Models\Setting::get('school_address', 'Laporan hasil belajar siswa') }}</p></div>
    </header>
    <section class="title-block"><h1>REKAPITULASI NILAI SISWA</h1><p>{{ $student->nama_lengkap }}</p></section>
    <div class="meta-grid">
        <div class="meta-item"><span class="meta-label">NISN</span><span class="meta-value">{{ $student->nisn ?: '-' }}</span></div>
        <div class="meta-item"><span class="meta-label">Kelas / Angkatan</span><span class="meta-value">{{ $student->angkatanModel?->name ?: '-' }}</span></div>
        <div class="meta-item"><span class="meta-label">Tanggal Cetak</span><span class="meta-value">{{ date('d/m/Y') }}</span></div>
    </div>
    <div class="summary"><span class="summary-label">Rata-rata nilai</span><span class="summary-value">{{ number_format($avgScore, 1) }}%</span></div>
    <table>
        <thead><tr><th>No</th><th>Kursus</th><th>Modul / Evaluasi</th><th>Nilai</th><th>Maks.</th><th>Persentase</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($submissions as $index => $submission)
            @php($max = $submission->max_score ?: 100)
            <tr><td class="center">{{ $index + 1 }}</td><td>{{ $submission->module->course->title ?? '-' }}</td><td>{{ $submission->module->title ?? '-' }}</td><td class="score">{{ $submission->score }}</td><td class="center">{{ $max }}</td><td class="score">{{ number_format($max > 0 ? ($submission->score / $max) * 100 : 0, 1) }}%</td><td class="{{ $submission->status === 'graded' ? 'status' : 'pending' }}">{{ $submission->status === 'graded' ? 'Sudah Dinilai' : 'Menunggu Penilaian' }}</td></tr>
        @empty
            <tr><td colspan="7">Belum ada nilai.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="signature"><div class="signature-box"><div class="signature-space"></div><div class="signature-name">Siswa</div></div></div>
</main>
</body>
</html>
