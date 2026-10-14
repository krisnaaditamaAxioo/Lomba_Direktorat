<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bukti Pendaftaran</title>
    <style>
        @page { margin: 26px 22px 58px 22px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #0f172a; }
        .kop { border-bottom: 3px solid #0f172a; padding-bottom: 10px; margin-bottom: 16px; }
        .kop-table { width: 100%; border-collapse: collapse; }
        .kop-table td { border: none; padding: 0; vertical-align: middle; }
        .kop-left { padding-right: 10px; }
        .logo { width: 130px; text-align: right; }
        .logo img { width: 120px; }
        .kop-line1 { font-size: 15px; font-weight: 700; letter-spacing: 0.06em; }
        .kop-line2 { font-size: 11px; font-weight: 700; margin-top: 2px; }
        .kop-line3 { font-size: 10px; color: #475569; margin-top: 4px; }
        .section-title { font-size: 13px; font-weight: 700; margin: 18px 0 8px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 8px 10px; border: 1px solid #e2e8f0; vertical-align: top; }
        .label { width: 34%; background: #f8fafc; font-weight: 700; color: #334155; }
        .value { width: 66%; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 999px; background: #f1f5f9; color: #0f172a; font-weight: 700; font-size: 11px; }
        .footer { position: fixed; left: 0; right: 0; bottom: -38px; border-top: 1px solid #e2e8f0; padding-top: 8px; font-size: 9px; color: #64748b; }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { border: none; padding: 0; }
        .small { font-size: 11px; color: #475569; }
        a { color: #1d4ed8; text-decoration: none; }
    </style>
</head>
<body>
    <div class="kop">
        <table class="kop-table">
            <tr>
                <td class="kop-left">
                    <div class="kop-line1">AXIOO CLASS PROGRAM</div>
                    <div class="kop-line2">{{ $appName ?? 'Smart Axioo' }}</div>
                    <div class="kop-line3">Bukti Pendaftaran Siswa Baru</div>
                </td>
                <td class="logo">
                    @if(!empty($logoDataUri))
                        <img src="{{ $logoDataUri }}" alt="Axioo">
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Informasi Pendaftaran</div>
    <table>
        <tr>
            <td class="label">ID Daftar</td>
            <td class="value">{{ $registration->application_id }}</td>
        </tr>
        <tr>
            <td class="label">Nama</td>
            <td class="value">{{ $registration->name }}</td>
        </tr>
        <tr>
            <td class="label">Status</td>
            <td class="value"><span class="badge">{{ $statusLabel }}</span></td>
        </tr>
        <tr>
            <td class="label">Tahun Akademik</td>
            <td class="value">{{ $registration->academic_year ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Angkatan</td>
            <td class="value">{{ $registration->angkatan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Daftar</td>
            <td class="value">{{ $registration->created_at?->format('d-m-Y H:i') ?? '-' }}</td>
        </tr>
    </table>

    <div class="section-title">Cek Status</div>
    <div class="small">Gunakan link berikut untuk cek status pendaftaran:</div>
    <div style="margin-top: 8px;">
        <a href="{{ $trackUrl }}">{{ $trackUrl }}</a>
    </div>

    @if($whatsappGroupLink)
        <div class="section-title">Grup WhatsApp</div>
        <div style="margin-top: 12px; display: flex; align-items: center; gap: 20px;">
            <div>
                <div class="small">Scan QR code berikut untuk masuk ke grup WhatsApp:</div>
                <div style="margin-top: 8px; padding: 10px; border: 1px solid #e2e8f0; display: inline-block;">
                    <img src="{{ $whatsappQrCode }}" alt="QR Code Grup WhatsApp" style="width: 120px; height: 120px;">
                </div>
            </div>
            <div style="flex: 1;">
                <div class="small">Atau klik link berikut:</div>
                <div style="margin-top: 8px;">
                    <a href="{{ $whatsappGroupLink }}">{{ $whatsappGroupLink }}</a>
                </div>
            </div>
        </div>
    @endif

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>{{ $appName ?? 'Smart Axioo' }} • Bukti Pendaftaran</td>
                <td style="text-align:right;">Dicetak pada {{ ($generatedAt ?? now())->format('d-m-Y H:i') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
