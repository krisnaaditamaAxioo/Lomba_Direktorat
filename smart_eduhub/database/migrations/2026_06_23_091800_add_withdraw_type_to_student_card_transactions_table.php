<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL only — SQLite tidak mendukung MODIFY COLUMN / ENUM
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE student_card_transactions MODIFY COLUMN type ENUM('topup', 'transfer', 'payment', 'transfer_receive', 'withdraw') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE student_card_transactions MODIFY COLUMN type ENUM('topup', 'transfer', 'payment', 'transfer_receive') NOT NULL");
        }
    }
};
