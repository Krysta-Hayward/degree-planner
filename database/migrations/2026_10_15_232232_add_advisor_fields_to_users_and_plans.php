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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('role')->default('student');
            $table->string('advisor_id')->nullable()->constrained('users')->nullOnDelete();
        });
        Schema::table('plans', function (Blueprint $table) {
            //
             $table->string('status')->default('draft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
        Schema::table('plans', function (Blueprint $table) {
            //
        });
    }
};
