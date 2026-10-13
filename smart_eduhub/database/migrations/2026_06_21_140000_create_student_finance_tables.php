<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('card_number')->unique();
            $table->decimal('balance', 15, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'frozen'])->default('active');
            $table->timestamps();
        });

        Schema::create('student_card_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('parent_name');
            $table->string('parent_phone');
            $table->string('statement_file'); // Surat pernyataan
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('student_card_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('card_id')->constrained('student_cards')->onDelete('cascade');
            $table->enum('type', ['topup', 'transfer', 'payment']);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->text('description')->nullable();
            $table->foreignId('related_card_id')->nullable()->constrained('student_cards')->onDelete('set null'); // Untuk transfer
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_card_transactions');
        Schema::dropIfExists('student_card_requests');
        Schema::dropIfExists('student_cards');
    }
};
