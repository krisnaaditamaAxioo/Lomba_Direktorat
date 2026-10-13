<?php

namespace App\Observers;

use App\Models\Student;
use App\Models\AdminNotification;

class StudentObserver
{
    public function created(Student $student): void
    {
        // Kirim notifikasi ke admin ketika ada siswa baru
        AdminNotification::sendToAdmins(
            'new_student',
            'Siswa Baru Terdaftar',
            "Siswa baru dengan nama {$student->nama_lengkap} telah terdaftar!",
            ['student_id' => $student->id]
        );
    }
}
