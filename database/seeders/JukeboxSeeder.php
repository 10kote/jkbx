<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Track;
use Illuminate\Database\Seeder;

class JukeboxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Artist::factory()
              ->count(4)
              ->has(Track::factory()->count(10))
              ->create();
    }
}
