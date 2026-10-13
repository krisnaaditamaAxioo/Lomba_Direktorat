<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Laporan Keuangan &mdash; {{ $card->student->nama_lengkap ?? 'Siswa' }}</title>
<style>
    @page {
        size: landscape;
        margin: 10mm;
    }

    /* ── Reset ── */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'DejaVu Sans', Arial, sans-serif;
        font-size: 9pt;
        color: #1a202c;
        background: #ffffff;
        line-height: 1.4;
    }

    /* ── Page Layout ── */
    .page { padding: 16mm 14mm 12mm 14mm; }

    /* ── Header ── */
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

    /* ── Student Card ── */
    .student-card {
        display: table;
        width: 100%;
        border-collapse: separate;
        border-spacing: 8px 0;
        margin-bottom: 12px;
    }
    .sc-cell { display: table-cell; vertical-align: top; }
    .sc-box {
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 10px 13px;
        background: #f8fafc;
    }
    .sc-box.balance-box {
        background: #1e3a5f;
        border-color: #1e3a5f;
    }
    .sc-label { font-size: 7pt; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 3px; }
    .sc-box.balance-box .sc-label { color: #93c5fd; }
    .sc-value { font-size: 11pt; font-weight: 700; color: #1e293b; }
    .sc-box.balance-box .sc-value { color: #ffffff; font-size: 13pt; }
    .sc-sub   { font-size: 7pt; color: #94a3b8; margin-top: 2px; }
    .sc-box.balance-box .sc-sub { color: #93c5fd; }

    /* ── Status Badge (inline) ── */
    .status-pill {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 3px;
        font-size: 7pt;
        font-weight: 700;
        text-transform: uppercase;
    }
    .pill-active   { background: #d1fae5; color: #065f46; }
    .pill-frozen   { background: #e0e7ff; color: #3730a3; }
    .pill-inactive { background: #f1f5f9; color: #64748b; }

    /* ── Divider ── */
    .divider { border: none; border-top: 1px solid #e2e8f0; margin: 10px 0; }

    /* ── Section Title ── */
    .section-title {
        font-size: 8.5pt;
        font-weight: 700;
        color: #312e81;
        border-left: 3px solid #4f46e5;
        padding-left: 7px;
        margin-bottom: 7px;
        margin-top: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ── Summary Row ── */
    .summary-table { width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 4px; }
    .summary-cell { width: 33.3%; padding: 9px 12px; border-radius: 5px; vertical-align: top; }
    .cell-topup    { background: #ecfdf5; border: 1px solid #6ee7b7; }
    .cell-payment  { background: #fff1f2; border: 1px solid #fca5a5; }
    .cell-transfer { background: #eef2ff; border: 1px solid #a5b4fc; }
    .summary-label { font-size: 7pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: #64748b; }
    .summary-value { font-size: 11pt; font-weight: 700; margin-top: 3px; }
    .topup-color    { color: #059669; }
    .payment-color  { color: #dc2626; }
    .transfer-color { color: #4338ca; }
    .neutral-color  { color: #1e293b; }
    .summary-count  { font-size: 7pt; color: #94a3b8; margin-top: 2px; }

    /* ── Data Table ── */
    table.data-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 12px;
        font-size: 8pt;
    }
    table.data-table thead tr { background: #1e293b; }
    table.data-table thead th {
        color: #f1f5f9;
        font-size: 7.5pt;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 7px 9px;
        border: 1px solid #1e293b;
        text-align: left;
        white-space: nowrap;
    }
    table.data-table thead th.text-right  { text-align: right; }
    table.data-table thead th.text-center { text-align: center; }

    table.data-table tbody tr:nth-child(odd)  td { background: #f8fafc; }
    table.data-table tbody tr:nth-child(even) td { background: #ffffff; }
    table.data-table tbody tr:last-child td { border-bottom: 2px solid #cbd5e1; }

    table.data-table tbody td {
        padding: 6px 9px;
        border: 1px solid #e2e8f0;
        color: #334155;
        vertical-align: middle;
    }
    table.data-table tbody td.text-right  { text-align: right; }
    table.data-table tbody td.text-center { text-align: center; }
    table.data-table tbody td.num { font-variant-numeric: tabular-nums; }

    /* ── Badges ── */
    .badge {
        display: inline-block;
        padding: 2px 7px;
        border-radius: 3px;
        font-size: 7pt;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-topup    { background: #d1fae5; color: #065f46; }
    .badge-payment  { background: #fee2e2; color: #991b1b; }
    .badge-transfer { background: #e0e7ff; color: #3730a3; }

    /* ── Totals row ── */
    .totals-row td {
        background: #f1f5f9 !important;
        font-weight: 700;
        border-top: 2px solid #cbd5e1 !important;
        font-size: 8pt;
    }

    /* ── Empty state ── */
    .empty-row td { text-align: center; color: #94a3b8; font-style: italic; padding: 16px; }

    /* ── Footer ── */
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

    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    {{-- HEADER --}}
    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    <div class="header-bar">
        <div class="header-left">
            <h1>Laporan Keuangan Siswa</h1>
            <div class="sub">Riwayat transaksi kartu uang elektronik</div>
        </div>
        <div class="header-right">
            <div class="print-info">Dicetak: {{ now()->format('d M Y, H:i') }}</div>
            <div class="print-info" style="margin-top:3px;">Smart Axioo &mdash; Sistem Keuangan</div>
        </div>
    </div>

    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    {{-- INFO SISWA & KARTU --}}
    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    <table class="student-card">
        <tr>
            {{-- Nama Siswa --}}
            <td class="sc-cell" style="width:38%">
                <div class="sc-box">
                    <div class="sc-label">Nama Siswa</div>
                    <div class="sc-value">{{ $card->student->nama_lengkap ?? '-' }}</div>
                    <div class="sc-sub">
                        NIS: {{ $card->student->nis_siswa ?? '-' }}
                        &nbsp;&bull;&nbsp;
                        Angkatan: {{ $card->student->angkatan ?? '-' }}
                    </div>
                </div>
            </td>
            {{-- No. Kartu --}}
            <td class="sc-cell" style="width:30%">
                <div class="sc-box">
                    <div class="sc-label">Nomor Kartu</div>
                    <div class="sc-value" style="font-family: monospace; letter-spacing: 1.5px; font-size:12pt;">{{ $card->card_number }}</div>
                    <div class="sc-sub">
                        Status:&nbsp;
                        @php $pillClass = match($card->status) { 'active' => 'pill-active', 'frozen' => 'pill-frozen', default => 'pill-inactive' }; @endphp
                        <span class="status-pill {{ $pillClass }}">{{ ucfirst($card->status) }}</span>
                    </div>
                </div>
            </td>
            {{-- Saldo --}}
            <td class="sc-cell" style="width:32%">
                <div class="sc-box balance-box">
                    <div class="sc-label">Saldo Saat Ini</div>
                    <div class="sc-value">Rp {{ number_format($card->balance, 0, ',', '.') }}</div>
                    <div class="sc-sub">{{ $transactions->count() }} total transaksi</div>
                </div>
            </td>
            <td class='summary-cell cell-payment'>
                <div class='summary-label'>Tarik Tunai</div>
                <div class='summary-value payment-color'>Rp {{ number_format(\, 0, ',', '.') }}</div>
                <div class='summary-count'>{{ \ }} transaksi</div>
            </td>
        </tr>
    </table>

    @if($from && $to)
    <div style="font-size:7.5pt; color:#64748b; margin-bottom:10px; background:#f1f5f9; padding:5px 10px; border-radius:4px; border-left:3px solid #4f46e5;">
        <strong>Periode laporan:</strong> {{ \Carbon\Carbon::parse($from)->isoFormat('D MMMM Y') }} &ndash; {{ \Carbon\Carbon::parse($to)->isoFormat('D MMMM Y') }}
    </div>
    @endif

    <hr class="divider">

    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    {{-- RINGKASAN TRANSAKSI --}}
    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    @php
        $totalTopup    = $transactions->where('type', 'topup')->sum('amount');
        $totalPayment  = $transactions->where('type', 'payment')->sum('amount');
        $totalTransfer = $transactions->where('type', 'transfer')->sum('amount');
        $cntTopup      = $transactions->where('type', 'topup')->count();
        $cntPayment    = $transactions->where('type', 'payment')->count();
        $cntTransfer   = $transactions->where('type', 'transfer')->count();
    @endphp

    <div class="section-title">Ringkasan Transaksi</div>
    <table class="summary-table">
        <tr>
            <td class="summary-cell cell-topup">
                <div class="summary-label">Top Up</div>
                <div class="summary-value topup-color">Rp {{ number_format($totalTopup, 0, ',', '.') }}</div>
                <div class="summary-count">{{ $cntTopup }} transaksi</div>
            </td>
            <td class="summary-cell cell-payment">
                <div class="summary-label">Pembayaran</div>
                <div class="summary-value payment-color">Rp {{ number_format($totalPayment, 0, ',', '.') }}</div>
                <div class="summary-count">{{ $cntPayment }} transaksi</div>
            </td>
            <td class="summary-cell cell-transfer">
                <div class="summary-label">Transfer</div>
                <div class="summary-value transfer-color">Rp {{ number_format($totalTransfer, 0, ',', '.') }}</div>
                <div class="summary-count">{{ $cntTransfer }} transaksi</div>
            </td>
            <td class='summary-cell cell-payment'>
                <div class='summary-label'>Tarik Tunai</div>
                <div class='summary-value payment-color'>Rp {{ number_format(\, 0, ',', '.') }}</div>
                <div class='summary-count'>{{ \ }} transaksi</div>
            </td>
        </tr>
    </table>

    <hr class="divider">

    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    {{-- DETAIL TRANSAKSI --}}
    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    <div class="section-title">Detail Transaksi</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:5%">No</th>
                <th style="width:16%">Tanggal &amp; Waktu</th>
                <th style="width:18%">No. Referensi</th>
                <th class="text-center" style="width:9%">Jenis</th>
                <th class="text-right" style="width:16%">Jumlah (Rp)</th>
                <th class="text-right" style="width:16%">Saldo Sebelum (Rp)</th>
                <th class="text-right" style="width:16%">Saldo Sesudah (Rp)</th>
                <th style="width:14%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $i => $t)
            @php
                $isCredit = $t->type === 'topup' || ($t->type === 'transfer' && $t->balance_after > $t->balance_before);
            @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td style="white-space:nowrap; font-size:7.5pt;">{{ $t->created_at->format('d/m/Y') }}<br><span style="color:#94a3b8;">{{ $t->created_at->format('H:i:s') }}</span></td>
                <td style="font-family:monospace; font-size:7pt; color:#475569;">{{ $t->reference_number ?? '-' }}</td>
                <td class="text-center">
                    <span class="badge badge-{{ $t->type }}">
                        {{ $t->type === 'topup' ? 'Top Up' : ($t->type === 'payment' ? 'Bayar' : 'Transfer') }}
                    </span>
                </td>
                <td class="text-right num {{ $isCredit ? 'topup-color' : 'payment-color' }}" style="font-weight:700;">
                    {{ $isCredit ? '+' : '&minus;' }}{{ number_format($t->amount, 0, ',', '.') }}
                </td>
                <td class="text-right num" style="color:#64748b;">{{ number_format($t->balance_before, 0, ',', '.') }}</td>
                <td class="text-right num neutral-color" style="font-weight:600;">{{ number_format($t->balance_after, 0, ',', '.') }}</td>
                <td style="font-size:7.5pt; color:#475569;">
                    {{ \Illuminate\Support\Str::limit($t->description ?? '-', 30) }}
                    @if($t->performedBy)
                    <br><span style="font-size:6.5pt; color:#94a3b8;">Petugas: {{ $t->performedBy->name }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="8">Tidak ada transaksi pada periode ini.</td></tr>
            @endforelse
            @if($transactions->count() > 0)
            <tr class="totals-row">
                <td colspan="4" style="text-align:right; padding-right:10px; color:#1e293b;">TOTAL</td>
                <td class="text-right num" style="color:#1e293b;">—</td>
                <td class="text-right num" style="color:#1e293b;">—</td>
                <td class="text-right num neutral-color">{{ number_format($card->balance, 0, ',', '.') }}</td>
                <td style="color:#64748b; font-size:7.5pt;">Saldo saat ini</td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    {{-- PETUGAS --}}
    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    @if($transactions->count() > 0)
    <div class="section-title">Rincian Petugas</div>
    <table class="data-table" style="width:60%;">
        <thead>
            <tr>
                <th>Nama Petugas</th>
                <th class="text-center">Jumlah Transaksi</th>
                <th class="text-right">Total Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions->groupBy(fn($t) => $t->performedBy->name ?? 'Sistem/Siswa') as $petugas => $trxGroup)
            <tr>
                <td>{{ $petugas }}</td>
                <td class="text-center">{{ $trxGroup->count() }}</td>
                <td class="text-right num neutral-color" style="font-weight:600;">{{ number_format($trxGroup->sum('amount'), 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    {{-- FOOTER --}}
    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    <div class="footer">
        <div class="footer-left">
            Laporan ini dibuat secara otomatis oleh sistem pada {{ now()->format('d M Y, H:i:s') }}
        </div>
        <div class="footer-right">
            Smart Axioo &mdash; Laporan Keuangan Siswa
        </div>
    </div>

</div>
</body>
</html>
