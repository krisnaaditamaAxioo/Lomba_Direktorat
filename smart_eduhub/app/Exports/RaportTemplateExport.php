<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RaportTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Template Raport' => new class implements FromCollection, WithHeadings, ShouldAutoSize
            {
                public function collection()
                {
                    return collect([
                        [
                            '12345',                       // NIS / ID Siswa
                            '2025/2026_Genap',             // Tahun Pelajaran
                            'X (Sepuluh)',                 // Tingkat
                            'Rekayasa Perangkat Lunak',   // Program Keahlian
                            'Budi Santoso, S.Kom',         // Trainer Name
                            'SER-TR-2025-001',             // Trainer No Serti
                            'NBM-123456',                  // Trainer NBM
                            'Tingkatkan terus kedisiplinan dan semangat belajar.', // Catatan
                            'Sikap dan kemauan kerja', 'A', 'Sangat Baik',
                            'Kreatif dan inovatif kerja', 'A', 'Sangat Baik',
                            'Ketrampilan menggunakan Alat', 'B', 'Baik',
                            'Kedisiplinan dan tanggung jawab', 'A', 'Sangat Baik',
                            'Kemauan Melaksanakan tugas', 'A', 'Sangat Baik',
                            'UJI SERTIFIKASI ACP 1', 'ACP-2025-001', 'LULUS',
                            'UJI SERTIFIKASI ACP 2', 'ACP-2025-002', 'LULUS',
                            'UJI SERTIFIKASI ACP 3', '', 'TIDAK LULUS',
                        ]
                    ]);
                }

                public function headings(): array
                {
                    return [
                        'NIS / ID Siswa',
                        'Tahun Pelajaran',
                        'Tingkat',
                        'Program Keahlian',
                        'Nama Trainer',
                        'No Serti Trainer',
                        'NBM Trainer',
                        'Catatan',
                        'Aspek 1 Nama', 'Aspek 1 Predikat (A/B/C/D)', 'Aspek 1 Keterangan',
                        'Aspek 2 Nama', 'Aspek 2 Predikat (A/B/C/D)', 'Aspek 2 Keterangan',
                        'Aspek 3 Nama', 'Aspek 3 Predikat (A/B/C/D)', 'Aspek 3 Keterangan',
                        'Aspek 4 Nama', 'Aspek 4 Predikat (A/B/C/D)', 'Aspek 4 Keterangan',
                        'Aspek 5 Nama', 'Aspek 5 Predikat (A/B/C/D)', 'Aspek 5 Keterangan',
                        'Sertifikasi 1 Ujian', 'Sertifikasi 1 No Serti', 'Sertifikasi 1 Status (LULUS/TIDAK LULUS)',
                        'Sertifikasi 2 Ujian', 'Sertifikasi 2 No Serti', 'Sertifikasi 2 Status (LULUS/TIDAK LULUS)',
                        'Sertifikasi 3 Ujian', 'Sertifikasi 3 No Serti', 'Sertifikasi 3 Status (LULUS/TIDAK LULUS)',
                    ];
                }
            },

            'Daftar Siswa Referensi' => new class implements FromCollection, WithHeadings, ShouldAutoSize
            {
                public function collection()
                {
                    return Student::with('angkatan')
                        ->where('status', 0)
                        ->orderBy('nama_lengkap')
                        ->get()
                        ->map(function ($s) {
                            return [
                                'nis'          => $s->nis_siswa ?: $s->id_siswa,
                                'id_siswa'     => $s->id_siswa,
                                'nisn'         => $s->nisn,
                                'nama_lengkap' => $s->nama_lengkap,
                                'angkatan'     => $s->angkatan?->name ?? '-',
                            ];
                        });
                }

                public function headings(): array
                {
                    return [
                        'NIS Siswa',
                        'ID Siswa',
                        'NISN',
                        'Nama Lengkap',
                        'Angkatan',
                    ];
                }
            }
        ];
    }
}
