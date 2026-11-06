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
        Schema::create('enrolls', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('student');
            $table->integer('course');

            $table->foreign('student')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
            $table->foreign('course')
                ->references('id')
                ->on('courses')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrolls');
    }
};
