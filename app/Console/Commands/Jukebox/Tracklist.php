<?php

namespace App\Console\Commands\Jukebox;

use App\Models\Track;
use Illuminate\Console\Command;

class Tracklist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jukebox:list';

    public const EMPTY_TRACK_LIST = 'No tracks found.';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get a list of all tracks in the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tracks = Track::with('artist')->get();
        if ($tracks->isEmpty()) {
            $this->info(self::EMPTY_TRACK_LIST);
            return;
        }
        foreach ($tracks as $track) {
            $this->info("{$track->id}: {$track->title} by {$track->artist->name}");
        }
    }
}
