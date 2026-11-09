<?php

namespace Database\Seeders;

use App\Models\Term;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        // The advisor created in DatabaseSeeder
        $terms = Term::take(9)->get();

        foreach ($terms as $term) {
            DB::table('programs')->insert([
                'name' => 'One Year Test Program',
                'course' => $term->course,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
