<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Laporan Keuangan Siswa</title>
<style>
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
    .page { padding: 18mm 15mm 14mm 15mm; }

    /* ── Header ── */
    .header-bar {
        background: #312e81;
        padding: 14px 20px;
        border-radius: 6px;
        margin-bottom: 16px;
        display: table;
        width: 100%;
    }
    .header-left  { display: table-cell; vertical-align: middle; }
    .header-right { display: table-cell; vertical-align: middle; text-align: right; width: 38%; }
    .header-bar h1 { font-size: 15pt; font-weight: 700; color: #ffffff; letter-spacing: 0.3px; }
    .header-bar .sub { font-size: 8pt; color: #c7d2fe; margin-top: 3px; }
    .header-bar .period-badge {
        background: rgba(255,255,255,0.18);
        color: #e0e7ff;
        font-size: 7.5pt;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 4px;
        display: inline-block;
        margin-top: 4px;
    }
    .print-info { color: #a5b4fc; font-size: 7pt; margin-top: 4px; }

    /* ── Divider ── */
    .divider { border: none; border-top: 1px solid #e2e8f0; margin: 12px 0; }

    /* ── Section Title ── */
    .section-title {
        font-size: 9pt;
        font-weight: 700;
        color: #312e81;
        border-left: 3px solid #4f46e5;
        padding-left: 7px;
        margin-bottom: 8px;
        margin-top: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ── Summary Cards (table-based) ── */
    .summary-table { width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-bottom: 4px; }
    .summary-cell { width: 20%; padding: 10px 10px; border-radius: 6px; vertical-align: top; }
    .cell-topup            { background: #ecfdf5; border: 1px solid #6ee7b7; }
    .cell-payment          { background: #fff1f2; border: 1px solid #fca5a5; }
    .cell-transfer         { background: #eef2ff; border: 1px solid #a5b4fc; }
    .cell-transfer_receive { background: #ecfeff; border: 1px solid #67e8f9; }
    .cell-withdraw         { background: #fffbeb; border: 1px solid #fcd34d; }
    .summary-label { font-size: 7pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; }
    .summary-value { font-size: 11pt; font-weight: 700; margin-top: 3px; }
    .topup-color              { color: #059669; }
    .payment-color            { color: #dc2626; }
    .transfer-color           { color: #4338ca; }
    .transfer-receive-color   { color: #0891b2; }
    .withdraw-color           { color: #b45309; }
    .summary-count  { font-size: 7pt; color: #94a3b8; margin-top: 2px; }

    /* ── Tables ── */
    table.data-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 14px;
        font-size: 8pt;
    }
    table.data-table thead tr {
        background: #1e293b;
    }
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
    table.data-table thead th.text-right { text-align: right; }
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
    .badge-topup            { background: #d1fae5; color: #065f46; }
    .badge-payment          { background: #fee2e2; color: #991b1b; }
    .badge-transfer         { background: #e0e7ff; color: #3730a3; }
    .badge-transfer_receive { background: #cffafe; color: #155e75; }
    .badge-withdraw         { background: #fef3c7; color: #92400e; }

    /* ── Per-Student Block ── */
    .student-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    .student-table .stu-head { background: #0f172a; }
    .student-table .stu-head td { color: #f1f5f9; padding: 7px 10px; font-size: 8pt; }
    .student-table .stu-head .stu-name  { font-weight: 700; font-size: 9pt; }
    .student-table .stu-head .stu-info  { font-size: 7pt; color: #94a3b8; margin-top: 2px; }
    .student-table .stu-head .stu-bal   { text-align: right; font-weight: 700; font-size: 10pt; color: #6ee7b7; }
    .student-table .stu-stat td { padding: 5px 9px; font-size: 8pt; border: 1px solid #e2e8f0; background: #f8fafc; text-align: center; }
    .student-table .stu-stat .stat-lbl  { font-size: 7pt; color: #64748b; font-weight: 700; text-transform: uppercase; }
    .student-table .stu-stat .stat-val  { font-size: 9.5pt; font-weight: 700; margin-top: 2px; }

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

    /* ── Page Break ── */
    .page-break { page-break-after: always; }

    /* ── Totals row ── */
    .totals-row td {
        background: #f1f5f9 !important;
        font-weight: 700;
        border-top: 2px solid #cbd5e1 !important;
        font-size: 8pt;
    }

    /* ── Empty state ── */
    .empty-row td { text-align: center; color: #94a3b8; font-style: italic; padding: 16px; }
</style>
</head>
<body>
<div class="page">

    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    {{-- HEADER --}}
    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    <div class="header-bar">
        <div class="header-left">
            @if(isset($filteredStudent) && $filteredStudent)
                <h1>Laporan Keuangan &mdash; {{ $filteredStudent->nama_lengkap }}</h1>
                <div class="sub">Rekap transaksi kartu uang elektronik siswa ({{ $filteredStudent->nis_siswa ?? '-' }})</div>
            @else
                <h1>Laporan Keuangan Siswa</h1>
                <div class="sub">Rekap transaksi kartu uang elektronik seluruh siswa</div>
            @endif
            <div class="period-badge">
                Periode: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
            </div>
        </div>
        <div class="header-right">
            <div class="print-info">Dicetak: {{ now()->format('d M Y, H:i') }}</div>
            <div class="print-info" style="margin-top:4px;">Smart Axioo &mdash; Sistem Keuangan</div>
        </div>
    </div>

    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    {{-- RINGKASAN TOTAL --}}
    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    <div class="section-title">Ringkasan Periode</div>
    <table class="summary-table">
        <tr>
            <td class="summary-cell cell-topup">
                <div class="summary-label">Total Top Up</div>
                <div class="summary-value topup-color">Rp {{ number_format($grandTotal['topup'], 0, ',', '.') }}</div>
                <div class="summary-count">{{ optional($summary->get('topup'))->count ?? 0 }} trx</div>
            </td>
            <td class="summary-cell cell-payment">
                <div class="summary-label">Total Pembayaran</div>
                <div class="summary-value payment-color">Rp {{ number_format($grandTotal['payment'], 0, ',', '.') }}</div>
                <div class="summary-count">{{ optional($summary->get('payment'))->count ?? 0 }} trx</div>
            </td>
            <td class="summary-cell cell-transfer">
                <div class="summary-label">Trf Keluar</div>
                <div class="summary-value transfer-color">Rp {{ number_format($grandTotal['transfer'], 0, ',', '.') }}</div>
                <div class="summary-count">{{ optional($summary->get('transfer'))->count ?? 0 }} trx</div>
            </td>
            <td class="summary-cell cell-transfer_receive">
                <div class="summary-label">Trf Masuk</div>
                <div class="summary-value transfer-receive-color">Rp {{ number_format($grandTotal['transfer_receive'], 0, ',', '.') }}</div>
                <div class="summary-count">{{ optional($summary->get('transfer_receive'))->count ?? 0 }} trx</div>
            </td>
            <td class="summary-cell cell-withdraw">
                <div class="summary-label">Tarik Tunai</div>
                <div class="summary-value withdraw-color">Rp {{ number_format($grandTotal['withdraw'], 0, ',', '.') }}</div>
                <div class="summary-count">{{ optional($summary->get('withdraw'))->count ?? 0 }} trx</div>
            </td>
        </tr>
    </table>

    <hr class="divider">

    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    {{-- REKAP HARIAN --}}
    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    <div class="section-title">Rekap Harian</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:4%">No</th>
                <th style="width:14%">Tanggal</th>
                <th class="text-right" style="width:15%">Top Up (Rp)</th>
                <th class="text-right" style="width:15%">Pembayaran (Rp)</th>
                <th class="text-right" style="width:15%">Trf Keluar (Rp)</th>
                <th class="text-right" style="width:15%">Trf Masuk (Rp)</th>
                <th class="text-right" style="width:15%">Tarik Tunai (Rp)</th>
                <th class="text-center" style="width:7%">Jml Trx</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dailySummary as $i => $d)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($d->transaction_date)->isoFormat('D MMM Y') }}</td>
                <td class="text-right num topup-color">{{ number_format($d->total_topup, 0, ',', '.') }}</td>
                <td class="text-right num payment-color">{{ number_format($d->total_payment, 0, ',', '.') }}</td>
                <td class="text-right num transfer-color">{{ number_format($d->total_transfer, 0, ',', '.') }}</td>
                <td class="text-right num transfer-receive-color">{{ number_format($d->total_transfer_receive, 0, ',', '.') }}</td>
                <td class="text-right num withdraw-color">{{ number_format($d->total_withdraw, 0, ',', '.') }}</td>
                <td class="text-center">{{ $d->total_transactions }}</td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="8">Tidak ada data transaksi pada periode ini.</td></tr>
            @endforelse
            @if($dailySummary->count() > 0)
            <tr class="totals-row">
                <td colspan="2" style="text-align:right; padding-right:10px;">TOTAL</td>
                <td class="text-right num topup-color">{{ number_format($grandTotal['topup'], 0, ',', '.') }}</td>
                <td class="text-right num payment-color">{{ number_format($grandTotal['payment'], 0, ',', '.') }}</td>
                <td class="text-right num transfer-color">{{ number_format($grandTotal['transfer'], 0, ',', '.') }}</td>
                <td class="text-right num transfer-receive-color">{{ number_format($grandTotal['transfer_receive'], 0, ',', '.') }}</td>
                <td class="text-right num withdraw-color">{{ number_format($grandTotal['withdraw'], 0, ',', '.') }}</td>
                <td class="text-center">{{ $dailySummary->sum('total_transactions') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="page-break"></div>

    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    {{-- REKAP PER SISWA --}}
    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    <div class="section-title">Rekap Per Siswa</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:4%">No</th>
                <th style="width:26%">Nama Siswa</th>
                <th style="width:11%">NIS</th>
                <th style="width:14%">No. Kartu</th>
                <th class="text-right" style="width:15%">Top Up (Rp)</th>
                <th class="text-right" style="width:15%">Bayar (Rp)</th>
                <th class="text-right" style="width:10%">Jml Trx</th>
                <th class="text-right" style="width:14%">Saldo (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($perStudentSummary as $i => $s)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td style="font-weight:600;">{{ $s->nama_lengkap }}</td>
                <td>{{ $s->nis_siswa ?? '-' }}</td>
                <td style="font-family: monospace; font-size:7.5pt;">{{ $s->card_number }}</td>
                <td class="text-right num topup-color">{{ number_format($s->total_topup, 0, ',', '.') }}</td>
                <td class="text-right num payment-color">{{ number_format($s->total_payment, 0, ',', '.') }}</td>
                <td class="text-center">{{ $s->total_trx }}</td>
                <td class="text-right num" style="font-weight:700; color:#1e293b;">{{ number_format($s->current_balance, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="9">Tidak ada data siswa pada periode ini.</td></tr>
            @endforelse
            @if($perStudentSummary->count() > 0)
            <tr class="totals-row">
                <td colspan="4" style="text-align:right; padding-right:10px;">TOTAL</td>
                <td class="text-right num topup-color">{{ number_format($perStudentSummary->sum('total_topup'), 0, ',', '.') }}</td>
                <td class="text-right num payment-color">{{ number_format($perStudentSummary->sum('total_payment'), 0, ',', '.') }}</td>
                <td class="text-center">{{ $perStudentSummary->sum('total_trx') }}</td>
                <td class="text-right num" style="color:#1e293b;">{{ number_format($perStudentSummary->sum('current_balance'), 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="page-break"></div>

    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    {{-- DETAIL SEMUA TRANSAKSI --}}
    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    <div class="section-title">Detail Semua Transaksi</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:4%">No</th>
                <th style="width:14%">Tanggal</th>
                <th style="width:22%">Nama Siswa</th>
                <th style="width:13%">No. Kartu</th>
                <th class="text-center" style="width:9%">Jenis</th>
                <th class="text-right" style="width:14%">Jumlah (Rp)</th>
                <th class="text-right" style="width:14%">Saldo Akhir (Rp)</th>
                <th style="width:10%">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $i => $t)
            @php
                $isCredit = in_array($t->type, ['topup', 'transfer_receive']);
                switch ($t->type) {
                    case 'topup': $txLabel = 'Top Up'; break;
                    case 'payment': $txLabel = 'Bayar'; break;
                    case 'transfer': $txLabel = 'Trf Keluar'; break;
                    case 'transfer_receive': $txLabel = 'Trf Masuk'; break;
                    case 'withdraw': $txLabel = 'Tarik Tunai'; break;
                    default: $txLabel = ucfirst($t->type);
                }
            @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td style="white-space:nowrap; font-size:7.5pt;">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                <td style="font-weight:600; font-size:8pt;">{{ $t->card->student->nama_lengkap ?? '-' }}</td>
                <td style="font-family:monospace; font-size:7.5pt;">{{ $t->card->card_number ?? '-' }}</td>
                <td class="text-center">
                    <span class="badge badge-{{ $t->type }}">{{ $txLabel }}</span>
                </td>
                <td class="text-right num {{ $isCredit ? 'topup-color' : 'payment-color' }}">
                    {{ $isCredit ? '+' : '-' }}{{ number_format($t->amount, 0, ',', '.') }}
                </td>
                <td class="text-right num" style="color:#334155;">{{ number_format($t->balance_after, 0, ',', '.') }}</td>
                <td style="font-size:7.5pt; color:#64748b;">{{ \Illuminate\Support\Str::limit($t->performedBy->name ?? 'Sistem', 15) }}</td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="8">Tidak ada transaksi pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

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
