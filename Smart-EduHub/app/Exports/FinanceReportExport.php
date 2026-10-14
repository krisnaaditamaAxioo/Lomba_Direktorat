<?php

namespace App\Exports;

use App\Models\StudentCardTransaction;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinanceReportExport implements WithMultipleSheets
{
    protected $from;
    protected $to;
    protected $type;
    protected $studentId;

    public function __construct(string $from, string $to, string $type = 'all', ?int $studentId = null)
    {
        $this->from = $from;
        $this->to   = $to;
        $this->type = $type;
        $this->studentId = $studentId;
    }

    public function sheets(): array
    {
        return [
            new FinanceSummarySheet($this->from, $this->to, $this->studentId),
            new FinanceTransactionsSheet($this->from, $this->to, $this->type, $this->studentId),
            new FinancePerStudentSheet($this->from, $this->to, $this->studentId),
        ];
    }
}


class FinanceSummarySheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected $from;
    protected $to;
    protected $studentId;

    public function __construct($from, $to, $studentId = null)
    {
        $this->from = $from;
        $this->to   = $to;
        $this->studentId = $studentId;
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function collection()
    {
        $query = StudentCardTransaction::with(['card.student'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$this->from, $this->to]);

        if ($this->studentId) {
            $query->whereHas('card', function ($cardQuery) {
                $cardQuery->where('student_id', $this->studentId);
            });
        }

        return $query->selectRaw('DATE(created_at) as tgl')
            ->selectRaw("SUM(CASE WHEN type = 'topup' THEN amount ELSE 0 END) as topup")
            ->selectRaw("SUM(CASE WHEN type = 'payment' THEN amount ELSE 0 END) as payment")
            ->selectRaw("SUM(CASE WHEN type = 'transfer' THEN amount ELSE 0 END) as transfer")
            ->selectRaw("SUM(CASE WHEN type = 'withdraw' THEN amount ELSE 0 END) as withdraw")
            ->selectRaw('COUNT(*) as total_trx')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('tgl', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return ['Tanggal', 'Total Top Up (Rp)', 'Total Pembayaran (Rp)', 'Total Transfer (Rp)', 'Total Tarik Tunai (Rp)', 'Jumlah Transaksi'];
    }

    public function map($row): array
    {
        return [
            \Carbon\Carbon::parse($row->tgl)->format('d/m/Y'),
            (float) $row->topup,
            (float) $row->payment,
            (float) $row->transfer,
            (float) $row->withdraw,
            (int) $row->total_trx,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}


class FinanceTransactionsSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected $from;
    protected $to;
    protected $type;
    protected $studentId;

    public function __construct($from, $to, $type = 'all', $studentId = null)
    {
        $this->from = $from;
        $this->to   = $to;
        $this->type = $type;
        $this->studentId = $studentId;
    }

    public function title(): string
    {
        return 'Detail Transaksi';
    }

    public function collection()
    {
        $query = StudentCardTransaction::with(['card.student', 'performedBy'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$this->from, $this->to]);

        if ($this->studentId) {
            $query->whereHas('card', function ($cardQuery) {
                $cardQuery->where('student_id', $this->studentId);
            });
        }

        if ($this->type !== 'all') {
            $query->where('type', $this->type);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No. Referensi',
            'Tanggal & Waktu',
            'Nama Siswa',
            'No. Kartu',
            'Jenis Transaksi',
            'Jumlah (Rp)',
            'Saldo Sebelum (Rp)',
            'Saldo Sesudah (Rp)',
            'Keterangan',
            'Petugas',
        ];
    }

    public function map($t): array
    {
        $typeLabel = match ($t->type) {
            'topup'    => 'Top Up',
            'payment'  => 'Pembayaran',
            'transfer' => 'Transfer',
            'withdraw' => 'Tarik Tunai',
            default    => ucfirst($t->type),
        };

        return [
            $t->reference_number ?? '-',
            $t->created_at->format('d/m/Y H:i'),
            $t->card->student->nama_lengkap ?? '-',
            $t->card->card_number ?? '-',
            $typeLabel,
            (float) $t->amount,
            (float) $t->balance_before,
            (float) $t->balance_after,
            $t->description ?? '-',
            $t->performedBy->name ?? 'Sistem/Siswa',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}


class FinancePerStudentSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected $from;
    protected $to;
    protected $studentId;

    public function __construct($from, $to, $studentId = null)
    {
        $this->from = $from;
        $this->to   = $to;
        $this->studentId = $studentId;
    }

    public function title(): string
    {
        return 'Rekap Per Siswa';
    }

    public function collection()
    {
        $query = StudentCardTransaction::with(['card.student'])
            ->whereBetween(DB::raw('DATE(student_card_transactions.created_at)'), [$this->from, $this->to])
            ->join('student_cards', 'student_card_transactions.card_id', '=', 'student_cards.id')
            ->join('students', 'student_cards.student_id', '=', 'students.id');

        if ($this->studentId) {
            $query->where('students.id', $this->studentId);
        }

        return $query->selectRaw('students.nama_lengkap, students.nis_siswa, student_cards.card_number, student_cards.balance as current_balance')
            ->selectRaw("SUM(CASE WHEN student_card_transactions.type = 'topup' THEN student_card_transactions.amount ELSE 0 END) as total_topup")
            ->selectRaw("SUM(CASE WHEN student_card_transactions.type = 'payment' THEN student_card_transactions.amount ELSE 0 END) as total_payment")
            ->selectRaw("SUM(CASE WHEN student_card_transactions.type = 'transfer' THEN student_card_transactions.amount ELSE 0 END) as total_transfer")
            ->selectRaw("SUM(CASE WHEN student_card_transactions.type = 'withdraw' THEN student_card_transactions.amount ELSE 0 END) as total_withdraw")
            ->selectRaw('COUNT(student_card_transactions.id) as total_trx')
            ->groupBy('students.nama_lengkap', 'students.nis_siswa', 'student_cards.card_number', 'student_cards.balance')
            ->orderBy('students.nama_lengkap')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'NIS',
            'No. Kartu',
            'Total Top Up (Rp)',
            'Total Pembayaran (Rp)',
            'Total Transfer (Rp)',
            'Total Tarik Tunai (Rp)',
            'Jumlah Transaksi',
            'Saldo Saat Ini (Rp)',
        ];
    }

    public function map($row): array
    {
        return [
            $row->nama_lengkap,
            $row->nis_siswa ?? '-',
            $row->card_number,
            (float) $row->total_topup,
            (float) $row->total_payment,
            (float) $row->total_transfer,
            (float) $row->total_withdraw,
            (int) $row->total_trx,
            (float) $row->current_balance,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}

