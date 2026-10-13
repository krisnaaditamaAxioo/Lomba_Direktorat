<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Surat - {{ $student->nama_lengkap }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 1cm 2cm;
            background-color: #fff;
            font-size: 11pt;
        }
        .logo-container {
            margin-bottom: 30px;
        }
        .logo-container img {
            height: 40px;
        }
        .letter-meta {
            margin-bottom: 25px;
        }
        .letter-meta table {
            border-collapse: collapse;
        }
        .letter-meta td {
            vertical-align: top;
            padding: 1px 0;
        }
        .meta-label { width: 80px; }
        .meta-separator { width: 20px; text-align: center; }
        
        .salutation {
            margin-bottom: 20px;
            font-weight: normal;
        }
        
        .content-section {
            margin-bottom: 20px;
            text-align: justify;
        }
        .content-section p {
            margin-bottom: 15px;
            text-indent: 0;
        }
        
        .data-telampir {
            color: #d93025;
            font-style: italic;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .student-box {
            margin: 15px 0;
        }
        .student-box table {
            width: 100%;
        }
        .student-box td {
            padding: 2px 0;
        }
        
        .closing {
            margin-top: 30px;
            margin-bottom: 40px;
        }
        
        .signature-section {
            margin-top: 30px;
            width: 350px;
        }
        .signature-date {
            margin-bottom: 5px;
        }
        .signature-role {
            margin-bottom: 10px;
        }
        .signature-content {
            display: table;
            width: 100%;
        }
        .signature-qr {
            display: table-cell;
            vertical-align: middle;
            width: 80px;
            height: 80px;
            border: 1px solid #eee;
            background: #f9f9f9;
            padding: 2px;
        }
        .signature-qr img {
            width: 80px;
            height: 80px;
            display: block;
        }
        .signature-details {
            display: table-cell;
            vertical-align: bottom;
            padding-left: 15px;
            padding-bottom: 5px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 2px;
        }
        .signature-id {
            font-size: 10pt;
        }
        
        .footer {
            margin-top: 50px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .footer-brand {
            font-style: italic;
            font-size: 9pt;
            margin-bottom: 3px;
        }
        .footer-address {
            font-style: italic;
            font-size: 8pt;
            color: #444;
        }
        
        .bold { font-weight: bold; }
        .red { color: #d93025; }
        .italic { font-style: italic; }
        
        @media print {
            .no-print { display: none; }
            body { padding: 1cm 2cm; }
        }
        
        .list-item {
            margin-left: 20px;
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    @if(!request()->has('view_only'))
    <div class="no-print" style="position: fixed; top: 0; left: 0; right: 0; background: #f1f5f9; padding: 10px; text-align: center; border-bottom: 1px solid #ddd; z-index: 1000;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #4f46e5; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">Cetak Surat</button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #64748b; color: white; border: none; border-radius: 6px; cursor: pointer; margin-left: 10px;">Tutup</button>
    </div>
    @endif

    <div class="logo-container">
        <!-- Logo Axioo Class Program -->
        <img src="{{ asset('images/SEH.png') }}" alt="Axioo Class Program Logo">
    </div>

    <div class="letter-meta">
        <table>
            <tr>
                <td class="meta-label">Nomor</td>
                <td class="meta-separator">:</td>
                <td>{{ date('Y') }}/{{ strtoupper($type) }}/ACP/{{ str_pad($student->id, 3, '0', STR_PAD_LEFT) }}</td>
            </tr>
            <tr>
                <td class="meta-label">Lamp</td>
                <td class="meta-separator">:</td>
                <td>-</td>
            </tr>
            <tr>
                <td class="meta-label">Perihal</td>
                <td class="meta-separator">:</td>
                <td class="bold">
                    @if($type == 'binaan') Pembinaan Kedisiplinan Siswa
                    @elseif($type == 'sp1') Surat Peringatan 1 (SP1)
                    @elseif($type == 'sp2') Surat Peringatan 2 (SP2)
                    @elseif($type == 'sp3') Surat Peringatan 3 (SP3) / Hal Serius
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="salutation">
        Assalamu'alaikum Wr. Wb.,
    </div>

    <div class="content-section">
        <p>Kepada Yth. Orang Tua / Wali Murid dari <span class="bold">{{ $student->nama_lengkap }}</span> di tempat.</p>
        
        <p>Sehubungan dengan perkembangan kedisiplinan putra/putri Bapak/Ibu di lingkungan sekolah, melalui surat ini kami informasikan akumulasi poin pelanggaran yang telah tercapai.</p>

        <div class="data-telampir">Data Pelanggaran</div>
        
        <p>Data yang tertera di bawah adalah status poin kedisiplinan siswa yang bersangkutan pada sistem <span class="bold">Axioo Class Program</span> tahun akademik <span class="bold">{{ $academicYear }}</span>.</p>

        <div class="student-box">
            <table>
                <tr>
                    <td width="120">Nama Lengkap</td>
                    <td width="20">:</td>
                    <td class="bold">{{ $student->nama_lengkap }}</td>
                </tr>
                <tr>
                    <td>NIS / NISN</td>
                    <td>:</td>
                    <td>{{ $student->nis_siswa ?? '-' }} / {{ $student->nisn ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Angkatan</td>
                    <td>:</td>
                    <td>{{ $student->angkatan ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Total Poin</td>
                    <td>:</td>
                    <td class="bold red">{{ $netPoints }} Poin</td>
                </tr>
            </table>
        </div>

        <p>
            @if($type == 'binaan')
                Berdasarkan data tersebut, siswa dinyatakan perlu mendapatkan <span class="bold red">PEMBINAAN KHUSUS</span>. Kami mengharapkan Bapak/Ibu dapat memberikan perhatian lebih terhadap perilaku dan kedisiplinan siswa di rumah.
            @elseif($type == 'sp1')
                Sehubungan dengan hal tersebut, kami menerbitkan <span class="bold red">SURAT PERINGATAN 1 (SP1)</span>. Mohon kerja sama Bapak/Ibu untuk membimbing putra/putrinya agar tidak mengulangi pelanggaran di masa mendatang.
            @elseif($type == 'sp2')
                Sehubungan dengan hal tersebut, kami menerbitkan <span class="bold red">SURAT PERINGATAN 2 (SP2)</span>. <span class="bold">Bapak/Ibu diharap hadir di sekolah</span> untuk melakukan koordinasi pembinaan bersama wali kelas dan bagian kesiswaan.
            @elseif($type == 'sp3')
                Ini adalah <span class="bold red">SURAT PERINGATAN 3 (SP3)</span> yang bersifat <span class="bold">SANGAT SERIUS</span>. Pelanggaran telah mencapai batas maksimal. <span class="bold">Kehadiran Bapak/Ibu di sekolah bersifat WAJIB</span> untuk membicarakan status keberlanjutan siswa di program ini.
            @endif
        </p>

        <p>Demikian surat ini kami sampaikan. Atas perhatian dan kerja sama Bapak/Ibu, kami ucapkan terima kasih.</p>
    </div>

    <div class="closing">
        <div class="signature-section">
            <div class="signature-date">Pringsewu, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</div>
            <div class="signature-role">Hormat kami,<br>PIC ACP</div>
            
            <div class="signature-content">
                <div class="signature-qr">
                    <!-- QR Code TTD Digital -->
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode('Surat Resmi: ' . date('Y') . '/' . strtoupper($type) . '/ACP/' . $student->id . ' | Pejabat: ' . $headmaster) }}&bgcolor=ffffff&color=000000&margin=1" 
                         alt="QR Code TTD">
                </div>
                <div class="signature-details">
                    <div class="signature-name">{{ strtoupper($headmaster) }}</div>
                    <div class="signature-id">NBM. {{ $headmaster_nip }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="footer-brand">Axioo Class Program - Axioo Excellence Hub</div>
        <div class="footer-address">
            {{ \App\Models\Setting::get('school_name', 'SMK Muhammadiyah 1 Pringsewu') }} ( Jl. Pemuda No. 56, Pringsewu Utara, Kec. Pringsewu, Kabupaten Pringsewu, Lampung 35373 )
        </div>
    </div>
</body>
</html>
