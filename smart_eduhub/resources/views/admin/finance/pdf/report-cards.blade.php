<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Daftar Kartu - Smart Axioo</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            color: #1a202c;
            background: #ffffff;
            line-height: 1.4;
        }

        .page { padding: 16mm 14mm 12mm 14mm; }

        .header-bar {
            background: #312e81;
            padding: 13px 18px;
            border-radius: 6px;
            margin-bottom: 14px;
            display: table;
            width: 100%;
        }
        .header-left  { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; width: 32%; }
        .header-bar h1 { font-size: 14pt; font-weight: 700; color: #ffffff; }
        .header-bar .sub { font-size: 7.5pt; color: #c7d2fe; margin-top: 2px; }
        .print-info { color: #a5b4fc; font-size: 7pt; }

        .filter-info {
            background: #f1f5f9;
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 14px;
            font-size: 8.5pt;
            color: #475569;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9pt;
        }
        table.data-table thead tr { background: #1e293b; }
        table.data-table thead th {
            color: #f1f5f9;
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            border: 1px solid #1e293b;
            text-align: left;
        }
        table.data-table thead th.text-right { text-align: right; }
        table.data-table thead th.text-center { text-align: center; }

        table.data-table tbody tr:nth-child(odd) td { background: #f8fafc; }
        table.data-table tbody tr:nth-child(even) td { background: #ffffff; }
        table.data-table tbody tr:last-child td { border-bottom: 2px solid #cbd5e1; }

        table.data-table tbody td {
            padding: 7px 10px;
            border: 1px solid #e2e8f0;
            color: #334155;
            vertical-align: middle;
        }
        table.data-table tbody td.text-right { text-align: right; }
        table.data-table tbody td.text-center { text-align: center; }
        table.data-table tbody td.num { font-variant-numeric: tabular-nums; }

        .status-pill {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
        }
        .pill-active   { background: #d1fae5; color: #065f46; }
        .pill-frozen   { background: #fef3c7; color: #92400e; }
        .pill-inactive { background: #fee2e2; color: #991b1b; }

        .footer {
            margin-top: 14px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            display: table;
            width: 100%;
            font-size: 7.5pt;
            color: #94a3b8;
        }
        .footer-left  { display: table-cell; }
        .footer-right { display: table-cell; text-align: right; }
    </style>
</head>
<body>
<div class="page">
    <div class="header-bar">
        <div class="header-left">
            <h1>Daftar Kartu Siswa</h1>
            <div class="sub">Data kartu dompet digital siswa</div>
        </div>
        <div class="header-right">
            <div class="print-info">Dicetak: {{ now()->format('d M Y, H:i') }}</div>
            <div class="print-info" style="margin-top:3px;">Smart Axioo</div>
        </div>
    </div>

    @if($nama || $angkatan)
    <div class="filter-info">
        <strong>Filter:</strong>
        @if($nama) Nama: {{ $nama }} @endif
        @if($nama && $angkatan) • @endif
        @if($angkatan) Angkatan: {{ $angkatan }} @endif
    </div>
    @endif

    <table class="data-table">
        <thead>
            <tr>
                <th style="width:5%">No</th>
                <th style="width:30%">Nama Siswa</th>
                <th style="width:15%">NIS</th>
                <th style="width:10%">Angkatan</th>
                <th style="width:20%">Nomor Kartu</th>
                <th class="text-right" style="width:12%">Saldo</th>
                <th class="text-center" style="width:8%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cards as $i => $card)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $card->student->nama_lengkap ?? '-' }}</td>
                <td>{{ $card->student->nis_siswa ?? '-' }}</td>
                <td>{{ $card->student->angkatan ?? '-' }}</td>
                <td style="font-family: monospace;">{{ $card->card_number }}</td>
                <td class="text-right num">Rp {{ number_format($card->balance, 0, ',', '.') }}</td>
                <td class="text-center">
                    @php $pillClass = match($card->status) { 'active' => 'pill-active', 'frozen' => 'pill-frozen', default => 'pill-inactive' }; @endphp
                    <span class="status-pill {{ $pillClass }}">{{ ucfirst($card->status) }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px; color: #94a3b8;">Tidak ada data kartu.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="footer-left">
            Total: {{ $cards->count() }} kartu
        </div>
        <div class="footer-right">
            Smart Axioo &mdash; Sistem Keuangan
        </div>
    </div>
</div>
</body>
</html>
