<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $viewYear;

    public function __construct($viewYear)
    {
        $this->viewYear = $viewYear;
    }

    public function collection()
    {
        $viewYearId = \App\Models\AcademicYear::getIdByName($this->viewYear);
        return Student::query()
            ->join('academic_year_student', 'students.id', '=', 'academic_year_student.student_id')
            ->leftJoin('angkatans', 'students.angkatan_id', '=', 'angkatans.id')
            ->where('academic_year_student.academic_year_id', $viewYearId)
            ->select(
                'students.*',
                'angkatans.id as angkatan_id',
                'angkatans.code as angkatan_code',
                'angkatans.name as angkatan_name'
            )
            ->get();
    }

    public function map($student): array
    {
        return [
            $student->id_siswa,
            $student->username,
            $student->pin,
            $student->status, // 0 atau 1
            $student->nama_lengkap,
            $student->angkatan_id,
            $student->angkatan_code,
            $student->angkatan_name,
            $student->nisn,
            $student->no_nik,
            $student->gender, // 0 atau 1
            $student->mother_name,
            $student->religion,
            $student->height,
            $student->weight,
            $student->place_of_birth,
            $student->dateofbirth,
            $student->address,
            $student->nis_siswa,
            $student->email,
        ];
    }

    public function headings(): array
    {
        return [
            'ID Siswa',
            'Username',
            'PIN',
            'Status (0=Aktif, 1=Nonaktif)',
            'Nama Lengkap',
            'ID Angkatan',
            'Kode Angkatan',
            'Nama Angkatan',
            'NISN',
            'No NIK',
            'Jenis Kelamin (0=Laki-laki, 1=Perempuan)',
            'Nama Ibu',
            'Agama',
            'Tinggi Badan',
            'Berat Badan',
            'Tempat Lahir',
            'Tanggal Lahir (YYYY-MM-DD)',
            'Alamat',
            'NIS Siswa',
            'Email',
        ];
    }
}
