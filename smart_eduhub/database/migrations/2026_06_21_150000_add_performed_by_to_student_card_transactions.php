<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_card_transactions', function (Blueprint $table) {
            $table->foreignId('performed_by')->nullable()->after('description')->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('student_card_transactions', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['performed_by_id']);
            $table->dropColumn('performed_by');
        });
    }
};
