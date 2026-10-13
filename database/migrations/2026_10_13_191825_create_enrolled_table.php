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
        Schema::create('enrolled', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreign('student')->references('id')->in('users');
            $table->foreign('term')->references('id')->in('terms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrolled');
    }
};
