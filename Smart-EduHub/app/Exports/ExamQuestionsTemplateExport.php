<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExamQuestionsTemplateExport implements FromArray, WithHeadings
{
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

    public function array(): array
    {
        return [
            [
                'Contoh: 2 + 2 = ?',
                '3',
                '4',
                '5',
                '6',
                'b',
                10,
            ],
        ];
    }
}

