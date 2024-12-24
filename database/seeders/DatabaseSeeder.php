<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Stadium\Database\Seeders\StadiumDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            StadiumDatabaseSeeder::class
        ]);
    }
}
