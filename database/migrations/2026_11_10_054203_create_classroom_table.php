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
        //>>> Scheduling Table! <<<
        Schema::table('classrooms', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('course'); //The course being taught
            $table->string('teacher'); //Teacher teaching (name)
            $table->integer('seats'); //how many seats?
            $table->integer('seatsOpen'); //how full is the class?
            $table->boolean('fall'); //is it offered this fall?
            $table->boolean('winter'); //this winter?
            $table->boolean('spring'); //this spring?
            $table->boolean('summer'); //this summer?
            $table->integer('timeStart'); // 1-24 what time is class?
            $table->integer('timeEnd'); // 1-24 when does class end?


            $table->foreign('course')->references('id')->on('courses')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
