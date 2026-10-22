<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * The program a student picked is stored by name (it matches programs.name).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('program')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('program');
        });
    }
};
