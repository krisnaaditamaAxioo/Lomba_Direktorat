<?php

namespace App\Imports;

use App\Models\ExamQuestion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ExamQuestionsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function __construct(private readonly string $academicYear)
    {
    }

    public function prepareForValidation($data, $index)
    {
        if (isset($data['correct_answer'])) {
            $correct = strtolower(trim((string) $data['correct_answer']));
            $data['correct_answer'] = substr($correct, 0, 1);
        }

        if (isset($data['score']) && $data['score'] !== '') {
            $data['score'] = (int) $data['score'];
        }

        return $data;
    }

    public function rules(): array
    {
        return [
            'question' => ['required'],
            'option_a' => ['required'],
            'option_b' => ['required'],
            'option_c' => ['required'],
            'option_d' => ['required'],
            'correct_answer' => ['required', 'in:a,b,c,d'],
            'score' => ['required', 'integer', 'min:1'],
        ];
    }

    public function model(array $row)
    {
        return new ExamQuestion([
            'question' => (string) ($row['question'] ?? ''),
            'option_a' => (string) ($row['option_a'] ?? ''),
            'option_b' => (string) ($row['option_b'] ?? ''),
            'option_c' => (string) ($row['option_c'] ?? ''),
            'option_d' => (string) ($row['option_d'] ?? ''),
            'correct_answer' => (string) ($row['correct_answer'] ?? ''),
            'score' => (int) ($row['score'] ?? 1),
            'academic_year' => $this->academicYear,
        ]);
    }
}

