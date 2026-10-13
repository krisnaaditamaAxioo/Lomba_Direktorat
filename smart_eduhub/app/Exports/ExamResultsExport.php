<?php

namespace App\Exports;

use App\Models\ExamResult;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExamResultsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return ExamResult::query()
            ->with('registration')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'ID Pendaftaran',
            'Nama',
            'Angkatan',
            'Tahun Akademik',
            'Total Soal',
            'Jawaban Benar',
            'Skor',
            'Tanggal Ujian',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->registration->application_id ?? '-',
            $row->registration->name ?? '-',
            $row->registration->angkatan ?? '-',
            $row->registration->academic_year ?? '-',
            $row->total_questions,
            $row->correct_answers,
            $row->score,
            $row->created_at?->format('d/m/Y H:i') ?? '-',
        ];
    }
}

