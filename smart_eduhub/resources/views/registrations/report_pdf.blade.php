<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pendaftaran - {{ $statusLabel }} Tahun Ajaran {{ $viewYear }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; color: #1e293b; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #4f46e5; }
        .header img { height: 70px; margin-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; color: #1e1b4b; }
        .header p { margin: 5px 0 0; color: #475569; font-size: 14px; }
        .info-bar { display: flex; justify-content: space-between; background: #eef2ff; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; }
        .info-bar .item { display: flex; align-items: center; gap: 8px; }
        .info-bar strong { color: #4338ca; }
        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 25px; }
        .stat-card { padding: 14px; border-radius: 8px; text-align: center; }
        .stat-card.amber { background: #fef3c7; border: 1px solid #fde68a; }
        .stat-card.blue { background: #dbeafe; border: 1px solid #bfdbfe; }
        .stat-card.green { background: #dcfce7; border: 1px solid #bbf7d0; }
        .stat-card.red { background: #fee2e2; border: 1px solid #fecaca; }
        .stat-card .num { font-size: 28px; font-weight: bold; margin-bottom: 4px; }
        .stat-card.amber .num { color: #92400e; }
        .stat-card.blue .num { color: #1e40af; }
        .stat-card.green .num { color: #166534; }
        .stat-card.red .num { color: #991b1b; }
        .stat-card .label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 12px; }
        thead th { background: #4338ca; color: white; padding: 10px 8px; text-align: left; font-weight: 600; text-transform: uppercase; font-size: 10px; letter-spacing: 0.5px; }
        tbody td { padding: 9px 8px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-verified { background: #dbeafe; color: #1e40af; }
        .badge-approved { background: #dcfce7; color: #166534; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .initial-circle { display: inline-flex; width: 28px; height: 28px; border-radius: 50%; background: #e0e7ff; color: #4338ca; align-items: center; justify-content: center; font-weight: bold; font-size: 11px; margin-right: 8px; vertical-align: middle; }
        .footer { margin-top: 40px; text-align: right; font-size: 12px; color: #64748b; }
        .search-info { background: #f1f5f9; padding: 8px 14px; border-radius: 6px; margin-bottom: 15px; font-size: 12px; color: #475569; }
    </style>
</head>
<body>
    <div class="header">
        @if($logoDataUri)
        <img src="{{ $logoDataUri }}" alt="Logo">
        @endif
        <h1>{{ $appName ?? 'Smart Axioo' }}</h1>
        <p>LAPORAN PENDAFTARAN SISWA BARU</p>
    </div>

    <div class="info-bar">
        <div class="item"><strong>Tahun Ajaran:</strong> {{ $viewYear }}</div>
        <div class="item"><strong>Status:</strong> {{ strtoupper($statusLabel) }}</div>
        <div class="item"><strong>Tanggal Cetak:</strong> {{ $generatedAt->format('d M Y H:i') }}</div>
        <div class="item"><strong>Total Data:</strong> {{ $registrations->count() }}</div>
    </div>

    @if($search)
    <div class="search-info">
        <strong>Hasil Pencarian:</strong> "{{ $search }}"
    </div>
    @endif

    <div class="stats-row">
        <div class="stat-card amber">
            <div class="num">{{ $stats['pending'] ?? 0 }}</div>
            <div class="label">Pending</div>
        </div>
        <div class="stat-card blue">
            <div class="num">{{ $stats['verified'] ?? 0 }}</div>
            <div class="label">Verified</div>
        </div>
        <div class="stat-card green">
            <div class="num">{{ $stats['approved'] ?? 0 }}</div>
            <div class="label">Diterima</div>
        </div>
        <div class="stat-card red">
            <div class="num">{{ $stats['rejected'] ?? 0 }}</div>
            <div class="label">Ditolak</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th style="width:250px">Nama Lengkap & ID</th>
                <th>Kontak</th>
                <th>Pendidikan</th>
                <th>Tanggal Daftar</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registrations as $index => $reg)
            <tr>
                <td style="text-align:center">{{ $index + 1 }}</td>
                <td>
                    <span class="initial-circle">
                        {{ strtoupper(substr($reg->name, 0, 1)) }}
                    </span>
                    <strong>{{ $reg->name }}</strong><br>
                    <small style="color:#64748b">
                        @if($reg->nisn)NISN: {{ $reg->nisn }} @endif
                        @if($reg->no_nik) | NIK: {{ $reg->no_nik }} @endif
                    </small>
                </td>
                <td>
                    <div style="margin-bottom:2px">{{ $reg->email }}</div>
                    <small style="color:#64748b">{{ $reg->phone ?? '-' }}</small>
                </td>
                <td>
                    <div><strong>Asal Sekolah:</strong></div>
                    <div>{{ $reg->previous_school }}</div>
                    @if($reg->angkatan)
                    <small style="background:#e0e7ff;color:#4338ca;padding:2px 6px;border-radius:4px;margin-top:4px;display:inline-block;">Angkatan {{ $reg->angkatan }}</small>
                    @endif
                </td>
                <td>
                    {{ \Carbon\Carbon::parse($reg->created_at)->format('d M Y') }}
                    <br><small style="color:#64748b">{{ \Carbon\Carbon::parse($reg->created_at)->format('H:i') }} WIB</small>
                </td>
                <td>
                    @php
                        $badgeClass = 'badge-pending';
                        if($reg->status === 'verified') $badgeClass = 'badge-blue';
                        elseif($reg->status === 'approved') $badgeClass = 'badge-approved';
                        elseif($reg->status === 'rejected') $badgeClass = 'badge-rejected';
                    @endphp
                    <span class="badge {{ $badgeClass }}">
                        @if($reg->status === 'pending') PENDING
                        @elseif($reg->status === 'verified') VERIFIED
                        @elseif($reg->status === 'approved') DITERIMA
                        @elseif($reg->status === 'rejected') DITOLAK
                        @else {{ strtoupper($reg->status) }}
                        @endif
                    </span>
                </td>
            </tr>
            @endforeach
            @if($registrations->isEmpty())
            <tr>
                <td colspan="6" style="text-align:center; padding:40px; color:#94a3b8;">
                    <strong>TIDAK ADA DATA PENDAFTARAN</strong>
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh Sistem {{ $appName ?? 'Smart Axioo' }} pada {{ $generatedAt->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
