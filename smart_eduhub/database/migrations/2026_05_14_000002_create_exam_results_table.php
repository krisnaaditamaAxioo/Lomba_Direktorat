<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_results', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('registration_id')->constrained('registrations')->onDelete('cascade');
            $blueprint->integer('total_questions');
            $blueprint->integer('correct_answers');
            $blueprint->integer('score');
            $blueprint->timestamp('started_at')->nullable();
            $blueprint->timestamp('finished_at')->nullable();
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
