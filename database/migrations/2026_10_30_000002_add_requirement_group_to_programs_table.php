<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Which requirement group a program's course belongs to, such as Core or
     * Electives. Existing rows become Core.
     */
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->string('requirement_group')->default('Core');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn('requirement_group');
        });
    }
};
