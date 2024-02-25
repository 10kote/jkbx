<?php

namespace Tests\Feature\Jukebox\Commands;

use App\Console\Commands\Jukebox\TrackList;
use App\Models\Track;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithConsoleEvents;
use Tests\TestCase;

class TracklistCommandTest extends TestCase
{
    use WithConsoleEvents, RefreshDatabase;

    /**
     *  Check the output for the empty list of tracks
     *
     * @return void
     */
    public function test_EmptyData(): void
    {
        $this->artisan('jukebox:list')
            ->expectsOutput(TrackList::EMPTY_TRACK_LIST)
            ->assertExitCode(0);
    }


    /**
     * Check the output for the list of tracks
     * @return void
     */
    public function test_WithData(): void
    {
        $this->seed(DatabaseSeeder::class);
        $tracks = Track::all();
        $cmdOutput = $this->artisan('jukebox:list');
        $cmdOutput->doesntExpectOutput(TrackList::EMPTY_TRACK_LIST)->assertExitCode(0);
        foreach ($tracks as $track) {
            $cmdOutput->expectsOutputToContain("{$track->info}");
        }

    }

}
