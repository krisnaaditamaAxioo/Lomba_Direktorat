<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hasil Seleksi</title>
    <style>
        @page { margin: 26px 22px 58px 22px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #0f172a; }
        .content-wrapper { padding: 40px; padding-left: 55px; padding-right: 55px; }
        
        .kop { border-bottom: 3px solid #0f172a; padding-bottom: 10px; margin-bottom: 16px; }
        .kop-table { width: 100%; border-collapse: collapse; }
        .kop-table td { border: none; padding: 0; vertical-align: middle; }
        .kop-left { padding-right: 10px; }
        .logo { width: 130px; text-align: right; }
        .logo img { width: 120px; }
        .kop-line1 { font-size: 15px; font-weight: 700; letter-spacing: 0.06em; }
        .kop-line2 { font-size: 11px; font-weight: 700; margin-top: 2px; }
        .kop-line3 { font-size: 10px; color: #475569; margin-top: 4px; }

        .badge { display: inline-block; padding: 4px 10px; border-radius: 999px; background: #f1f5f9; color: #0f172a; font-weight: 700; font-size: 11px; }
        .section-title { font-size: 13px; font-weight: 700; margin: 18px 0 8px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 8px 10px; border: 1px solid #e2e8f0; vertical-align: top; }
        .label { width: 34%; background: #f8fafc; font-weight: 700; color: #334155; }
        .value { width: 66%; }
        .note { margin-top: 14px; font-size: 11px; color: #475569; }
        .letter-title { text-align: center; font-size: 15px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; margin: 10px 0 14px; }
        .para { line-height: 1.6; text-align: justify; }
        
        .signature-wrap { width: 100%; margin-top: 30px; }
        .sig-table { width: 100%; border-collapse: collapse; }
        .sig-table td { border: none; padding: 0; }
        .sig-box { width: 50%; vertical-align: top; }
        .sig-line { margin-top: 50px; border-top: 1px solid #94a3b8; width: 200px; }
        .page-break { page-break-before: always; }

        .footer { position: fixed; left: 0; right: 0; bottom: -38px; border-top: 1px solid #e2e8f0; padding-top: 8px; font-size: 9px; color: #64748b; }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { border: none; padding: 0; }
        a { color: #1d4ed8; text-decoration: none; }
    </style>
</head>
<body>
    <div class="content-wrapper">
        <div class="kop">
            <table class="kop-table">
                <tr>
                    <td class="kop-left">
                        <div class="kop-line1">AXIOO CLASS PROGRAM</div>
                        <div class="kop-line2">{{ $appName ?? 'Smart Axioo' }}</div>
                        <div class="kop-line3">Hasil Seleksi Pendaftaran</div>
                    </td>
                    <td class="logo">
                        @if(!empty($logoDataUri))
                            <img src="{{ $logoDataUri }}" alt="Axioo">
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        @if($registration->status === 'approved')
            <div class="letter-title">Pemberitahuan Hasil Seleksi</div>

            <p class="para">Berdasarkan hasil seleksi yang telah dilakukan, dengan ini Axioo Class Program menyatakan bahwa:</p>
            <table style="margin: 12px 0;">
                <tr><td class="label">Nama Peserta</td><td class="value">{{ $registration->name }}</td></tr>
                <tr><td class="label">ID Daftar</td><td class="value">{{ $registration->application_id }}</td></tr>
                <tr><td class="label">Angkatan</td><td class="value">{{ $registration->angkatan ?? '-' }}</td></tr>
                <tr><td class="label">Tahun Akademik</td><td class="value">{{ $registration->academic_year ?? '-' }}</td></tr>
            </table>

            <p class="para">
                Dinyatakan <b>LULUS</b> dan <b>DITERIMA</b> sebagai peserta program <b>Axioo Class Program (ACP)</b>.
                Peserta diharapkan bersedia mematuhi ketentuan dan tata tertib yang berlaku selama kegiatan program berlangsung.
            </p>

            <p class="para" style="margin-top: 12px;">
                Demikian pemberitahuan ini disampaikan untuk dipergunakan sebagaimana mestinya.
            </p>

            <div class="signature-wrap">
                <table class="sig-table">
                    <tr>
                        <td class="sig-box"></td>
                        <td class="sig-box" style="text-align:center;">
                            <div style="font-size: 11px; color:#475569;">Admin/Verifikator</div>
                            <div class="sig-line" style="margin-left:auto; margin-right:auto;"></div>
                            <div style="margin-top: 6px; font-weight:700;">( ............................ )</div>
                        </td>
                    </tr>
                </table>
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

            <div class="page-break"></div>

            <div class="kop">
                <table class="kop-table">
                    <tr>
                        <td class="kop-left">
                            <div class="kop-line1">AXIOO CLASS PROGRAM</div>
                            <div class="kop-line2">{{ $appName ?? 'Smart Axioo' }}</div>
                            <div class="kop-line3">Hasil Seleksi Pendaftaran</div>
                        </td>
                        <td class="logo">
                            @if(!empty($logoDataUri))
                                <img src="{{ $logoDataUri }}" alt="Axioo">
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <div class="letter-title">Surat Pernyataan Kesanggupan</div>
            <p class="para">Saya yang bertanda tangan di bawah ini:</p>
            <table style="margin: 12px 0;">
                <tr><td class="label">Nama</td><td class="value">{{ $registration->name }}</td></tr>
                <tr><td class="label">ID Daftar</td><td class="value">{{ $registration->application_id }}</td></tr>
            </table>

            <p class="para">
                Dengan ini menyatakan <b>siap dan bersedia</b> mengikuti <b>Program Unggulan Axioo Class Program (ACP)</b> dengan penuh tanggung jawab, mematuhi aturan,
                menjaga etika, serta mengikuti seluruh kegiatan pembelajaran/sertifikasi sesuai ketentuan program.
            </p>

            <p class="para" style="margin-top: 12px;">
                Apabila di kemudian hari saya melanggar ketentuan program, saya bersedia menerima sanksi sesuai peraturan yang berlaku.
                Demikian surat pernyataan ini saya buat dengan sebenarnya.
            </p>

            <div class="signature-wrap">
                <table class="sig-table">
                    <tr>
                        <td class="sig-box">
                            <div style="font-size: 11px; color:#475569;">Tertanda,</div>
                            <div class="sig-line"></div>
                            <div style="margin-top: 6px; font-weight:700;">{{ $registration->name }}</div>
                        </td>
                        <td class="sig-box" style="text-align:right;">
                            <div style="font-size: 11px; color:#475569;">Mengetahui</div>
                            <div class="sig-line" style="margin-left:auto;"></div>
                            <div style="margin-top: 6px; font-weight:700;">( ............................ )</div>
                        </td>
                    </tr>
                </table>
            </div>
        @else
            <div class="section-title">Status Seleksi</div>
            <div style="margin-bottom: 10px;">
                <span class="badge">{{ $statusLabel }}</span>
            </div>

            <table>
                <tr><td class="label">ID Daftar</td><td class="value">{{ $registration->application_id }}</td></tr>
                <tr><td class="label">Nama</td><td class="value">{{ $registration->name }}</td></tr>
                <tr><td class="label">Angkatan</td><td class="value">{{ $registration->angkatan ?? '-' }}</td></tr>
                <tr><td class="label">Tahun Akademik</td><td class="value">{{ $registration->academic_year ?? '-' }}</td></tr>
            </table>

            <div class="note">
                @if($registration->status === 'rejected')
                    Mohon maaf, Anda dinyatakan <b>Ditolak</b>. Silakan hubungi admin untuk informasi lebih lanjut.
                @else
                    Dokumen ini menampilkan status seleksi terakhir.
                @endif
                <div style="margin-top: 8px;">Link cek status: <a href="{{ $trackUrl }}">{{ $trackUrl }}</a></div>
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
        @endif
    </div>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>{{ $appName ?? 'Smart Axioo' }} • Hasil Seleksi</td>
                <td style="text-align:right;">Dicetak pada {{ ($generatedAt ?? now())->format('d-m-Y H:i') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>