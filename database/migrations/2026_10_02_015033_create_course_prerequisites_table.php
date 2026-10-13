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
        Schema::create('course_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('course');
            $table->integer('prereq');

            $table->foreign('course')
                ->references('id')
                ->in('courses')
                ->onUpdate('cascade');
            $table->foreign('prereq')
                ->references('id')
                ->in('courses')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_prerequisites');
    }
};
