<?php

namespace App\Exports;

use App\Models\StudentCard;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class FinanceStudentExport implements WithMultipleSheets
{
    protected $cardId;
    protected $from;
    protected $to;

    public function __construct(int $cardId, ?string $from = null, ?string $to = null)
    {
        $this->cardId = $cardId;
        $this->from   = $from;
        $this->to     = $to;
    }

    public function sheets(): array
    {
        return [
            new FinanceStudentInfoSheet($this->cardId),
            new FinanceStudentTransactionsSheet($this->cardId, $this->from, $this->to),
        ];
    }
}


class FinanceStudentInfoSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected $cardId;

    public function __construct($cardId)
    {
        $this->cardId = $cardId;
    }

    public function title(): string
    {
        return 'Info Siswa & Kartu';
    }

    public function collection()
    {
        return StudentCard::with('student')->where('id', $this->cardId)->get();
    }

    public function headings(): array
    {
        return [
            'Nama Siswa', 'NIS', 'Angkatan',
            'No. Kartu', 'Status Kartu', 'Saldo Saat Ini (Rp)',
        ];
    }

    public function map($card): array
    {
        return [
            $card->student->nama_lengkap ?? '-',
            $card->student->nis_siswa ?? '-',
            $card->student->angkatan ?? '-',
            $card->card_number,
            ucfirst($card->status),
            (float) $card->balance,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}


class FinanceStudentTransactionsSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected $cardId;
    protected $from;
    protected $to;

    public function __construct($cardId, $from, $to)
    {
        $this->cardId = $cardId;
        $this->from   = $from;
        $this->to     = $to;
    }

    public function title(): string
    {
        return 'Riwayat Transaksi';
    }

    public function collection()
    {
        $query = \App\Models\StudentCardTransaction::with(['performedBy'])
            ->where('card_id', $this->cardId);

        if ($this->from && $this->to) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$this->from, $this->to]);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No. Referensi',
            'Tanggal & Waktu',
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
