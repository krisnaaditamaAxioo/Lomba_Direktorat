<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hasil Ujian Seleksi</title>
    <style>
        @page { margin: 26px 22px 58px 22px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #0f172a; }
        .kop { border-bottom: 3px solid #0f172a; padding-bottom: 10px; margin-bottom: 14px; }
        .kop-table { width: 100%; border-collapse: collapse; }
        .kop-table td { border: none; padding: 0; }
        .logo { width: 120px; }
        .logo img { width: 110px; }
        .kop-right { padding-left: 10px; }
        .kop-inner { width: 100%; border-collapse: collapse; }
        .kop-inner td { border: none; padding: 0; vertical-align: top; }
        .kop-line1 { font-size: 15px; font-weight: 700; letter-spacing: 0.06em; }
        .kop-line2 { font-size: 11px; font-weight: 700; margin-top: 2px; }
        .kop-line3 { font-size: 10px; color: #475569; margin-top: 4px; }
        .kop-meta { text-align: right; font-size: 10px; color: #475569; white-space: nowrap; }
        .footer { position: fixed; left: 0; right: 0; bottom: -38px; border-top: 1px solid #e2e8f0; padding-top: 8px; font-size: 9px; color: #64748b; }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { border: none; padding: 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e2e8f0; padding: 6px 8px; }
        th { background: #f8fafc; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: #334155; }
        td { vertical-align: top; }
        .num { text-align: right; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <div class="kop">
        <table class="kop-table">
            <tr>
                <td class="logo">
                    @if(!empty($logoDataUri))
                        <img src="{{ $logoDataUri }}" alt="Axioo">
                    @endif
                </td>
                <td class="kop-right">
                    <table class="kop-inner">
                        <tr>
                            <td>
                                <div class="kop-line1">AXIOO CLASS PROGRAM</div>
                                <div class="kop-line2">{{ $appName ?? 'Smart Axioo' }}</div>
                                <div class="kop-line3">Laporan Hasil Ujian Seleksi</div>
                            </td>
                            <td class="kop-meta">Dicetak: {{ $generatedAt->format('d-m-Y H:i') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:34px;">No</th>
                <th>ID Daftar</th>
                <th>Nama</th>
                <th style="width:80px;">Angkatan</th>
                <th style="width:90px;">Benar/Total</th>
                <th style="width:60px;" class="center">Skor</th>
                <th style="width:110px;">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $i => $res)
            <tr>
                <td class="num">{{ $i + 1 }}</td>
                <td>{{ $res->registration->application_id ?? '-' }}</td>
                <td>{{ $res->registration->name ?? '-' }}</td>
                <td class="center">{{ $res->registration->angkatan ?? '-' }}</td>
                <td class="center">{{ $res->correct_answers }} / {{ $res->total_questions }}</td>
                <td class="center">{{ $res->score }}</td>
                <td>{{ $res->created_at?->format('d-m-Y H:i') ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>{{ $appName ?? 'Smart Axioo' }} • Hasil Ujian Seleksi</td>
                <td style="text-align:right;">Dicetak pada {{ $generatedAt->format('d-m-Y H:i') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
