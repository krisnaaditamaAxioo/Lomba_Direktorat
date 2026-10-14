<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('admin_notifications', 'read_at')) {
            Schema::table('admin_notifications', function (Blueprint $table) {
                $table->timestamp('read_at')->nullable()->after('data');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('admin_notifications', 'read_at')) {
            Schema::table('admin_notifications', function (Blueprint $table) {
                $table->dropColumn('read_at');
            });
        }
    }
};
