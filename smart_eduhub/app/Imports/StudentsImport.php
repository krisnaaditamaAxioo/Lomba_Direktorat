<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Angkatan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class StudentsImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    public $imported = 0;
    public $errors = 0;
    public $errorMessages = [];

    public function model(array $row)
    {
        \Log::info('Processing row', ['data' => $row]);
        
        try {
            // Ambil data dengan nama kolom
            $username = $row['username'] ?? $row['Username'] ?? null;
            $pin = $row['pin'] ?? $row['PIN'] ?? '123456';
            $status = $row['status'] ?? $row['Status (0=Aktif, 1=Nonaktif)'] ?? 0;
            $namaLengkap = $row['nama_lengkap'] ?? $row['Nama Lengkap'] ?? null;
            $idAngkatan = $row['id_angkatan'] ?? $row['ID Angkatan'] ?? null;
            $nisn = $row['nisn'] ?? $row['NISN'] ?? null;
            $noNik = $row['no_nik'] ?? $row['No NIK'] ?? null;
            $gender = $row['gender'] ?? $row['Jenis Kelamin (0=Laki-laki, 1=Perempuan)'] ?? 0;
            $motherName = $row['mother_name'] ?? $row['Nama Ibu'] ?? null;
            $religion = $row['religion'] ?? $row['Agama'] ?? null;
            $height = $row['height'] ?? $row['Tinggi Badan'] ?? null;
            $weight = $row['weight'] ?? $row['Berat Badan'] ?? null;
            $placeOfBirth = $row['place_of_birth'] ?? $row['Tempat Lahir'] ?? null;
            $dateOfBirth = $row['dateofbirth'] ?? $row['Tanggal Lahir (YYYY-MM-DD)'] ?? null;
            $address = $row['address'] ?? $row['Alamat'] ?? null;
            $nisSiswa = $row['nis_siswa'] ?? $row['NIS Siswa'] ?? null;
            $email = $row['email'] ?? $row['Email'] ?? null;
            
            \Log::info('Extracted data', [
                'nama' => $namaLengkap,
                'angkatan' => $idAngkatan,
            ]);
            
            if (empty($namaLengkap)) {
                $this->errors++;
                $this->errorMessages[] = "Nama lengkap kosong";
                \Log::warning('Skipping: nama_lengkap empty');
                return null;
            }

            if (empty($idAngkatan)) {
                $this->errors++;
                $this->errorMessages[] = "ID Angkatan kosong untuk $namaLengkap";
                \Log::warning('Skipping: id_angkatan empty');
                return null;
            }

            // Cari angkatan
            $angkatan = Angkatan::where('id', $idAngkatan)->where('is_active', true)->first();
            
            if (!$angkatan) {
                $this->errors++;
                $this->errorMessages[] = "Angkatan dengan ID $idAngkatan tidak ditemukan untuk $namaLengkap";
                \Log::warning('Skipping: angkatan not found', ['angkatan_id' => $idAngkatan]);
                return null;
            }

            $angkatanId = $angkatan->id;

            // Mapping gender
            if (is_string($gender)) {
                $gender = strtoupper(trim($gender));
                if ($gender === 'L') {
                    $gender = 0;
                } elseif ($gender === 'P') {
                    $gender = 1;
                } else {
                    $gender = (int)$gender;
                }
            } else {
                $gender = (int)$gender;
            }
            $gender = ($gender === 1) ? 1 : 0;

            // Mapping status
            if (is_string($status)) {
                $status = strtolower(trim($status));
                if ($status === 'active') {
                    $status = 0;
                } elseif ($status === 'inactive' || $status === 'on leave') {
                    $status = 1;
                } else {
                    $status = (int)$status;
                }
            } else {
                $status = (int)$status;
            }
            $status = ($status === 1) ? 1 : 0;

            // Prepare data
            $studentData = [
                'nama_lengkap' => $namaLengkap,
                'email' => $email,
                'username' => $username ?? $nisSiswa ?? $nisn ?? null,
                'pin' => $pin,
                'angkatan_id' => $angkatanId,
                'angkatan' => $angkatan->code,
                'status' => $status,
                'nisn' => $nisn,
                'no_nik' => $noNik,
                'gender' => $gender,
                'mother_name' => $motherName,
                'religion' => $religion,
                'height' => !empty($height) ? (int)$height : null,
                'weight' => !empty($weight) ? (int)$weight : null,
                'place_of_birth' => $placeOfBirth,
                'dateofbirth' => $dateOfBirth,
                'address' => $address,
                'nis_siswa' => $nisSiswa,
            ];

            // Cari siswa yang sudah ada
            $existingStudent = null;
            if (!empty($nisSiswa)) {
                $existingStudent = Student::where('nis_siswa', $nisSiswa)->first();
            }
            if (!$existingStudent && !empty($username)) {
                $existingStudent = Student::where('username', $username)->first();
            }
            if (!$existingStudent && !empty($nisn)) {
                $existingStudent = Student::where('nisn', $nisn)->first();
            }

            if ($existingStudent) {
                unset($studentData['username']);
                $existingStudent->update($studentData);
                $student = $existingStudent;
            } else {
                do {
                    $idSiswa = 'STU' . now()->format('YmdHis') . rand(1000, 9999);
                } while (Student::where('id_siswa', $idSiswa)->exists());
                
                $studentData['id_siswa'] = $idSiswa;
                
                if (empty($studentData['username'])) {
                    $studentData['username'] = $idSiswa;
                }
                
                $student = Student::create($studentData);
            }

            $academicYear = \App\Models\Setting::get('academic_year');
            $academicYearId = \App\Models\AcademicYear::getIdByName($academicYear);
            if ($academicYearId && $student) {
                \App\Models\AcademicYearStudent::updateOrCreate(
                    ['student_id' => $student->id, 'academic_year_id' => $academicYearId],
                    []
                );
            }

            $this->imported++;
            \Log::info('Successfully imported/updated student', ['id' => $student->id, 'nama' => $student->nama_lengkap]);
            return $student;
        } catch (\Exception $e) {
            $this->errors++;
            $this->errorMessages[] = "Error: " . $e->getMessage();
            \Log::error('Import student error', ['message' => $e->getMessage(), 'row' => $row, 'trace' => $e->getTraceAsString()]);
            return null;
        }
    }
}
