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

        // >>> Scheduling Table! <<<
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('course'); // The course taken
            $table->string('student'); // Student who took the course
            $table->string('letterGrade'); // Letter grade, needed in case of Z or W etc.
            $table->float('gpa'); // Grade as number

            $table->foreign('course')->references('id')->on('courses')->onUpdate('cascade');
            $table->foreign('student')->references('id')->on('users')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
