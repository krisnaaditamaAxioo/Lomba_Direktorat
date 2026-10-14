<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_cards', function (Blueprint $table) {
            $table->string('pin')->nullable()->after('status');
        });

        Schema::table('student_card_transactions', function (Blueprint $table) {
            $table->string('reference_number')->nullable()->unique()->after('balance_after');
        });
    }

    public function down(): void
    {
        Schema::table('student_card_transactions', function (Blueprint $table) {
            $table->dropUnique(['reference_number']);
            $table->dropColumn('reference_number');
        });

        Schema::table('student_cards', function (Blueprint $table) {
            $table->dropColumn('pin');
        });
    }
};
