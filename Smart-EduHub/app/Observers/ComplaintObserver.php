<?php

namespace App\Observers;

use App\Models\Complaint;
use App\Models\AdminNotification;

class ComplaintObserver
{
    public function created(Complaint $complaint): void
    {
        // Kirim notifikasi ke admin ketika ada pengaduan baru
        $complainantName = $complaint->complainant->nama_lengkap ?? $complaint->complainant->name ?? 'Pengguna';
        
        AdminNotification::sendToAdmins(
            'new_complaint',
            'Pengaduan Baru Masuk',
            "Ada pengaduan baru dari {$complainantName}: {$complaint->title}",
            ['complaint_id' => $complaint->id]
        );
    }
}
