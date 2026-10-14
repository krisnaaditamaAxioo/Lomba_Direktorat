<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_action_histories', function (Blueprint $table) {
            $table->id();
            // Tidak pakai foreign key agar riwayat tetap ada walau transaksi dihapus
            $table->unsignedBigInteger('transaction_id')->nullable()->index();
            $table->enum('action', ['created', 'updated', 'deleted']);
            $table->json('snapshot'); // snapshot lengkap data transaksi pada saat aksi
            $table->json('changes')->nullable(); // untuk update: { field: { old, new } }
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_action_histories');
    }
};
