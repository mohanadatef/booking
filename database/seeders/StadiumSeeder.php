<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Stadium\Entities\Stadium;

class StadiumSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $stadiums = Stadium::factory()->count(2)->create();

        foreach ($stadiums as $stadium) {
            $stadium->pitches()->createMany([
                ['name' => 'Pitch 1'],
                ['name' => 'Pitch 2'],
                ['name' => 'Pitch 3'],
            ]);
        }
    }
}
