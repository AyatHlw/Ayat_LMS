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
        Schema::create('students_progress', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('course_enroll_id')->constrained('course_enrolls')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('video_id')->constrained('videos')->cascadeOnDelete()->cascadeOnUpdate();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students_progress');
    }
};
