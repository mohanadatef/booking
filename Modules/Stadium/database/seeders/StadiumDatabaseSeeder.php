<?php

namespace Modules\Stadium\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Stadium\Models\Stadium;

class StadiumDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for ($i=0;$i <= 2;$i++) {
            $stadium = Stadium::create([
                'name' => 'Stadium '.($i+1),
            ]);
            $stadium->pitches()->createMany([
                ['name' => 'Pitch 1'],
                ['name' => 'Pitch 2'],
                ['name' => 'Pitch 3'],
            ]);
        }
    }
}
