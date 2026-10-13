<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['students', 'parents'] as $tableName) {
            if (!Schema::hasColumn($tableName, 'remember_token')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->rememberToken();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['students', 'parents'] as $tableName) {
            if (Schema::hasColumn($tableName, 'remember_token')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('remember_token');
                });
            }
        }
    }
};