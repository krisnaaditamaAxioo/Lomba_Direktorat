<?php

namespace App\Exports;

use App\Models\ExamQuestion;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExamQuestionsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private readonly string $academicYear)
    {
    }

    public function collection(): Collection
    {
        return ExamQuestion::query()
            ->where('academic_year', $this->academicYear)
            ->orderBy('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'question',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'correct_answer',
            'score',
        ];
    }

    public function map($row): array
    {
        return [
            $row->question,
            $row->option_a,
            $row->option_b,
            $row->option_c,
            $row->option_d,
            $row->correct_answer,
            $row->score,
        ];
    }
}

