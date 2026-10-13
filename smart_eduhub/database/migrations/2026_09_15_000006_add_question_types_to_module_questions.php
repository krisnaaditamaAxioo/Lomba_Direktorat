<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('module_questions', function (Blueprint $table) {
            $table->string('type')->default('multiple_choice')->after('question');
        });
    }

    public function down(): void
    {
        Schema::table('module_questions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};