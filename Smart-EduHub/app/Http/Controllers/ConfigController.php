<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConfigController extends Controller
{
    // Menggantikan fetch_settings.php
    public function fetchSettings() 
    {
        $settings = DB::table('settings')->get(['key', 'value']);

        if ($settings->isNotEmpty()) {
            return response()->json(["status" => "success", "data" => $settings]);
        }
        return response()->json(["status" => "error", "message" => "No settings found"]);
    }

    // Menggantikan fetch_students.php
   public function fetchStudents() 
{
    // Pastikan key-nya "data" dengan isi array "nis" dan "nama_lengkap"
    $students = DB::table('students')
        ->whereNotNull('nis_siswa')
        ->where('nis_siswa', '!=', '')
        ->get(['nis_siswa as nis', 'nama_lengkap']);

    // Pastikan trim diterapkan agar tidak ada spasi yang menyebabkan mismatch
    $formatted = $students->map(function ($s) {
        return [
            "nis" => trim((string)$s->nis),
            "nama_lengkap" => (string)$s->nama_lengkap
        ];
    });

    return response()->json([
        "status" => "success", 
        "data" => $formatted
    ]);
}

  public function saveAttendance(Request $request) 
    {
        // Mengambil input dari JSON request
        $input = $request->json()->all();

        if (empty($input) || !isset($input['nis'])) {
            return response()->json(["status" => "error", "message" => "Data tidak lengkap"]);
        }

        $nis = trim($input['nis']);
        $timestamp = $input['timestamp'];
        $location = $input['location'] ?? 'Mobile Device';

        try {
            // 1. Cari student_id berdasarkan nis_siswa
            $student = DB::table('students')->where('nis_siswa', $nis)->first();
            if (!$student) {
                return response()->json(["status" => "error", "message" => "NIS tidak terdaftar"]);
            }

            // 2. Ambil Settings
            $settings = DB::table('settings')->pluck('value', 'key');
            $limitTime = $settings['check_in_time'] ?? "08:00";
            $academicYear = $settings['academic_year'] ?? "2026/2027";

            // 3. Olah Waktu
            $timeUnix = strtotime($timestamp);
            $dateOnly = date("Y-m-d", $timeUnix);
            $timeOnly = date("H:i:s", $timeUnix);
            $status = (strtotime($timeOnly) <= strtotime($limitTime)) ? "present" : "late";

            // 4. Cek Duplikasi
            $exists = DB::table('attendances')
                ->where('student_id', $student->id)
                ->where('date', $dateOnly)
                ->exists();

            if ($exists) {
                return response()->json(["status" => "error", "message" => "Siswa sudah absen hari ini"]);
            }

            // 5. Simpan ke database
            DB::table('attendances')->insert([
                'student_id'    => $student->id,
                'date'          => $dateOnly,
                'check_in'      => $timeOnly,
                'status'        => $status,
                'academic_year' => $academicYear,
                'location'      => $location,
                'created_at'    => now(),
                'updated_at'    => now()
            ]);

            return response()->json([
                "status" => "success", 
                "message" => "Absensi Berhasil", 
                "attendance_status" => $status
            ]);

        } catch (\Exception $e) {
            Log::error("Gagal Simpan Absensi: " . $e->getMessage());
            return response()->json(["status" => "error", "message" => "Terjadi kesalahan server"]);
        }
    }
}