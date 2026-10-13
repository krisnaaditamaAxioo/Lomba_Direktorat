<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_questions', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->text('question');
            $blueprint->text('option_a');
            $blueprint->text('option_b');
            $blueprint->text('option_c');
            $blueprint->text('option_d');
            $blueprint->enum('correct_answer', ['a', 'b', 'c', 'd']);
            $blueprint->integer('score')->default(1);
            $blueprint->string('academic_year')->nullable();
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
    }
};
