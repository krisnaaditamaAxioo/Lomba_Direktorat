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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('application_id')->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('grade_applying');
            $table->string('previous_school')->nullable();
            $table->decimal('gpa', 3, 2)->nullable();
            $table->string('status')->default('pending'); // pending, verified, incomplete, rejected, approved
            $table->integer('document_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
