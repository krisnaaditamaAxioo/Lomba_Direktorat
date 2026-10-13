<?php

namespace App\Exports;

use App\Models\Angkatan;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StudentsTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Data Siswa' => new class implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings
            {
                public function collection()
                {
                    return collect([
                        [
                            'contohsiswa', // Username
                            '123456', // PIN
                            0, // Status (0=Aktif, 1=Nonaktif)
                            'Nama Siswa Contoh', // Nama Lengkap
                            1, // ID Angkatan
                            '1234567890', // NISN
                            '3201123456789012', // No NIK
                            0, // Jenis Kelamin (0=Laki-laki, 1=Perempuan)
                            'Nama Ibu Contoh', // Nama Ibu
                            'Islam', // Agama
                            165, // Tinggi Badan
                            55, // Berat Badan
                            'Jakarta', // Tempat Lahir
                            '2005-01-01', // Tanggal Lahir
                            'Jl. Contoh No. 123, Jakarta', // Alamat
                            '12345', // NIS Siswa
                            'contoh@example.com', // Email
                        ],
                    ]);
                }
                public function headings(): array
                {
                    return [
                        'Username',
                        'PIN',
                        'Status (0=Aktif, 1=Nonaktif)',
                        'Nama Lengkap',
                        'ID Angkatan',
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
            },
            'Daftar Angkatan' => new class implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings
            {
                public function collection()
                {
                    return Angkatan::where('is_active', true)->get(['id', 'code', 'name']);
                }
                public function headings(): array
                {
                    return ['ID Angkatan', 'Kode Angkatan', 'Nama Angkatan'];
                }
            },
        ];
    }
}
