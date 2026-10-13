<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->foreignId('major_id')->nullable()->after('previous_school')->constrained('majors');
            $table->text('address')->nullable()->after('major_id');
            
            // Drop old columns if needed or just leave them
            if (Schema::hasColumn('registrations', 'grade_applying')) {
                $table->dropColumn('grade_applying');
            }
            if (Schema::hasColumn('registrations', 'gpa')) {
                $table->dropColumn('gpa');
            }
            if (Schema::hasColumn('registrations', 'document_count')) {
                $table->dropColumn('document_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['phone', 'major_id', 'address']);
            $table->string('grade_applying')->nullable();
            $table->decimal('gpa', 3, 2)->nullable();
            $table->integer('document_count')->default(0);
        });
    }
};
