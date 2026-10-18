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
            // Set "role", set role default as "student"
            $table->string('role')->default('student');
            // Set "advisor_id", "nullable" allows empty column,"constrained" creates foreign key to point to user_id, "nullOnDelete" becomes empty in database (Does not delete students data only advisor) 
            $table->foreignId('advisor_id')->nullable()->constrained('users')->nullOnDelete();
        });
        Schema::table('plans', function (Blueprint $table) {
            // Creates new plan sets it as "draft"
             $table->string('status')->default('draft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // remoeves the status column and its data
            $table->dropColumn('status');
        });

        Schema::table('users', function (Blueprint $table) {
            // Drop contsrained foreign_id
            $table->dropConstrainedForeignId('advisor_id');
            // Drop role
            $table->dropColumn('role');
        });
    }
};
