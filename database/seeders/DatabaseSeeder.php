<?php

namespace Database\Seeders;

use App\Models\Coloc;
use App\Models\Roommate;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $coloc = Coloc::create(['name' => 'Appart de Lea']);
        $coloc->createDefaultTasks();

        Roommate::create([
            'coloc_id' => $coloc->id,
            'first_name' => 'Lea',
            'avatar_slug' => 'personnage-01',
            'order' => 0,
        ]);

        Roommate::create([
            'coloc_id' => $coloc->id,
            'first_name' => 'Hugo',
            'avatar_slug' => 'personnage-05',
            'order' => 1,
        ]);

        Roommate::create([
            'coloc_id' => $coloc->id,
            'first_name' => 'Camille',
            'avatar_slug' => 'personnage-09',
            'order' => 2,
        ]);
    }
}
