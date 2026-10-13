<?php

namespace App\Observers;

use App\Models\StudentCardTransaction;
use App\Models\StudentNotification;
use App\Models\AdminNotification;

class StudentCardTransactionObserver
{
    public function created(StudentCardTransaction $transaction)
    {
        $transaction->load('card.student');
        $student = $transaction->card->student;

        $typeLabels = [
            'topup' => 'Topup Saldo',
            'withdraw' => 'Penarikan Saldo',
            'payment' => 'Pembayaran',
            'transfer' => 'Transfer Keluar',
            'transfer_receive' => 'Transfer Masuk',
        ];

        $title = $typeLabels[$transaction->type] ?? 'Transaksi Keuangan';
        $message = match ($transaction->type) {
            'topup' => "Saldo Anda telah ditambahkan sebesar Rp " . number_format($transaction->amount, 0, ',', '.') . " - {$transaction->description}",
            'withdraw' => "Anda telah menarik saldo sebesar Rp " . number_format($transaction->amount, 0, ',', '.') . " - {$transaction->description}",
            'payment' => "Pembayaran sebesar Rp " . number_format($transaction->amount, 0, ',', '.') . " berhasil - {$transaction->description}",
            'transfer' => "Transfer keluar sebesar Rp " . number_format($transaction->amount, 0, ',', '.') . " - {$transaction->description}",
            'transfer_receive' => "Anda menerima transfer sebesar Rp " . number_format($transaction->amount, 0, ',', '.') . " - {$transaction->description}",
            default => "Transaksi keuangan: {$transaction->type} sebesar Rp " . number_format($transaction->amount, 0, ',', '.'),
        };

        StudentNotification::sendTo(
            $student->id,
            'finance',
            $title,
            $message,
            ['transaction_id' => $transaction->id, 'type' => $transaction->type, 'amount' => $transaction->amount]
        );

        // Kirim notifikasi ke admin untuk transaksi keuangan
        $adminMessage = "Transaksi {$title} dari siswa {$student->nama_lengkap}: Rp " . number_format($transaction->amount, 0, ',', '.') . " - {$transaction->description}";
        
        AdminNotification::sendToAdmins(
            'finance',
            "Transaksi Keuangan Baru: {$title}",
            $adminMessage,
            ['transaction_id' => $transaction->id, 'student_id' => $student->id, 'type' => $transaction->type, 'amount' => $transaction->amount]
        );
    }
}
