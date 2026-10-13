<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Siswa - {{ $viewYear }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; color: #1e293b; margin: 0; padding: 15px; font-size: 11px; }
        .header { text-align: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 2px solid #4f46e5; }
        .header img { height: 55px; margin-bottom: 8px; }
        .header h1 { margin: 0; font-size: 20px; color: #1e1b4b; }
        .header p { margin: 4px 0 0; color: #475569; font-size: 11px; }
        .info-bar { display: flex; justify-content: space-between; background: #eef2ff; padding: 8px 14px; border-radius: 6px; margin-bottom: 12px; font-size: 10px; flex-wrap: wrap; gap: 6px; }
        .info-bar .item strong { color: #4338ca; }
        .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 15px; }
        .stat-card { padding: 10px; border-radius: 6px; text-align: center; }
        .stat-card.blue { background: #dbeafe; border: 1px solid #bfdbfe; }
        .stat-card.green { background: #dcfce7; border: 1px solid #bbf7d0; }
        .stat-card.slate { background: #f1f5f9; border: 1px solid #e2e8f0; }
        .stat-card .num { font-size: 20px; font-weight: bold; }
        .stat-card.blue .num { color: #1e40af; }
        .stat-card.green .num { color: #166534; }
        .stat-card.slate .num { color: #334155; }
        .stat-card .label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; margin-top: 3px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 9px; }
        thead th { background: #4338ca; color: white; padding: 6px 4px; text-align: left; font-weight: 700; text-transform: uppercase; font-size: 8px; letter-spacing: 0.3px; }
        tbody td { padding: 5px 4px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        .initial-circle { display: inline-flex; width: 22px; height: 22px; border-radius: 50%; background: #e0e7ff; color: #4338ca; align-items: center; justify-content: center; font-weight: bold; font-size: 9px; margin-right: 5px; vertical-align: middle; }
        .akun-box { background: #fef3c7; border: 1px solid #fde68a; padding: 4px 6px; border-radius: 4px; }
        .akun-box .label { font-size: 8px; color: #92400e; font-weight: 700; text-transform: uppercase; }
        .akun-box .val { font-size: 10px; font-weight: 700; color: #78350f; font-family: 'Courier New', monospace; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 8px; font-size: 8px; font-weight: bold; }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }
        .footer { margin-top: 25px; text-align: right; font-size: 9px; color: #64748b; }
        .search-info { background: #f1f5f9; padding: 5px 10px; border-radius: 4px; margin-bottom: 10px; font-size: 9px; color: #475569; }
        .angkatan-badge { display: inline-block; background: #ede9fe; color: #5b21b6; padding: 1px 5px; border-radius: 4px; font-size: 8px; font-weight: 700; }
    </style>
</head>
<body>
    <div class="header">
        @if($logoDataUri)
        <img src="{{ $logoDataUri }}" alt="Logo">
        @endif
        <h1>{{ $appName ?? 'Smart Axioo' }}</h1>
        <p>LAPORAN DATA SISWA LENGKAP DENGAN AKUN LOGIN</p>
    </div>

    <div class="info-bar">
        <div class="item"><strong>Tahun Ajaran:</strong> {{ $viewYear }}</div>
        <div class="item"><strong>Tanggal Cetak:</strong> {{ $generatedAt->format('d M Y H:i') }}</div>
        <div class="item"><strong>Total Data:</strong> {{ $stats['total'] }}</div>
        <div class="item"><strong>Status:</strong> {{ strtoupper($statusFilter == 'all' ? 'Semua' : $statusFilter) }}</div>
        @if($angkatanFilter != 'all')
        <div class="item"><strong>Angkatan:</strong> {{ $angkatanFilter }}</div>
        @endif
    </div>

    @if($search)
    <div class="search-info">
        <strong>Hasil Pencarian:</strong> "{{ $search }}" — {{ $students->count() }} data ditemukan
    </div>
    @endif
<!-- 
    <div class="stats-row">
        <div class="stat-card blue">
            <div class="num">{{ $stats['total'] }}</div>
            <div class="label">Total Siswa</div>
        </div>
        <div class="stat-card green">
            <div class="num">{{ $stats['active'] }}</div>
            <div class="label">Aktif</div>
        </div>
        <div class="stat-card slate">
            <div class="num">{{ $stats['inactive'] }}</div>
            <div class="label">Tidak Aktif</div>
        </div>
    </div> -->

    <table>
        <thead>
            <tr>
                <th style="width:25px">No</th>
                <th style="width:160px">DATA PRIBADI SISWA</th>
                <th style="width:130px">DATA KELAHIRAN</th>
                <th style="width:110px">NISN / NIK / NIS</th>
                <th style="width:140px">ALAMAT & KONTAK</th>
                <th style="width:100px">AYAH / IBU</th>
                <th style="width:100px">LAINNYA</th>
                <th style="width:100px">AKUN LOGIN</th>
                <th style="width:45px">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $s)
            <tr>
                <td style="text-align:center">{{ $index + 1 }}</td>
                <td>
                    <span class="initial-circle">{{ strtoupper(substr($s->nama_lengkap ?? '?', 0, 1)) }}</span>
                    <strong>{{ $s->nama_lengkap }}</strong><br>
                    <small style="color:#64748b">
                        ID: {{ $s->id_siswa }}<br>
                        Kelamin: {{ ($s->gender == 0 || $s->gender == 'L') ? 'Laki-laki' : 'Perempuan' }}<br>
                        Agama: {{ $s->religion ?? '-' }}<br>
                        @if(!empty($s->angkatan_code))
                            <span class="angkatan-badge">{{ $s->angkatan_code }}{{ !empty($s->angkatan_name) ? ' - '.$s->angkatan_name : '' }}</span>
                        @elseif(!empty($s->angkatan))
                            <span class="angkatan-badge">{{ $s->angkatan }}</span>
                        @endif
                    </small>
                </td>
                <td>
                    <strong>{{ $s->place_of_birth ?? '-' }}</strong><br>
                    <small style="color:#64748b">{{ $s->dateofbirth ? \Carbon\Carbon::parse($s->dateofbirth)->format('d M Y') : '-' }}</small><br>
                    <small style="color:#475569">
                        TB: {{ $s->height ? $s->height.' cm' : '-' }}<br>
                        BB: {{ $s->weight ? $s->weight.' kg' : '-' }}
                    </small>
                </td>
                <td>
                    <div><strong>NISN:</strong><br><span style="font-family:'Courier New',monospace;font-size:10px">{{ $s->nisn ?? '-' }}</span></div>
                    <div style="margin-top:3px"><strong>NIK:</strong><br><span style="font-family:'Courier New',monospace;font-size:10px">{{ $s->no_nik ?? '-' }}</span></div>
                    <div style="margin-top:3px"><strong>NIS:</strong><br><span style="font-family:'Courier New',monospace;font-size:10px">{{ $s->nis_siswa ?? '-' }}</span></div>
                </td>
                <td>
                    <small style="color:#334155;line-height:1.4">
                        {{ $s->address ?? '-' }}
                    </small>
                    <hr style="margin:4px 0;border:none;border-top:1px dashed #cbd5e1">
                    <small style="color:#64748b">
                        <div>✉ {{ $s->email ?? '-' }}</div>
                        <div>📱 {{ $s->phone ?? '-' }}</div>
                        <div>👨‍👩‍👧 {{ $s->parent_phone ?? '-' }}</div>
                    </small>
                </td>
                <td>
                    <small>
                        <div><strong>Ibu:</strong><br>{{ $s->mother_name ?? '-' }}</div>
                        <div style="margin-top:4px"><strong>Sekolah Asal:</strong><br>{{ $s->previous_school ?? '-' }}</div>
                    </small>
                </td>
                <td>
                    <small>
                        <div><strong>Tanggal Daftar:</strong><br>{{ \Carbon\Carbon::parse($s->created_at)->format('d M Y') }}</div>
                        <div style="margin-top:4px"><strong>Role:</strong><br>{{ $s->role ?? 'student' }}</div>
                    </small>
                </td>
                <td>
                    <div class="akun-box">
                        <div>
                            <div class="label">Username</div>
                            <div class="val">{{ $s->username }}</div>
                        </div>
                        <div style="margin-top:3px">
                            <div class="label">PIN Login (6 digit)</div>
                            <div class="val">{{ $s->pin }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    @if($s->status == 0 || $s->status === 'active')
                        <span class="badge badge-active">AKTIF</span>
                    @else
                        <span class="badge badge-inactive">OFF</span>
                    @endif
                </td>
            </tr>
            @endforeach
            @if($students->isEmpty())
            <tr>
                <td colspan="9" style="text-align:center; padding:30px; color:#94a3b8;">
                    <strong>TIDAK ADA DATA SISWA</strong>
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh Sistem {{ $appName ?? 'Smart Axioo' }} pada {{ $generatedAt->format('d/m/Y H:i:s') }}</p>
        <p style="margin-top:2px">Halaman ini berisi DATA RAHASIA (username & PIN siswa) — harap dijaga kerahasiaannya.</p>
    </div>
</body>
</html>
