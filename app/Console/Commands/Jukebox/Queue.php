<?php

namespace App\Console\Commands\Jukebox;

use App\Models\Playlist;
use Illuminate\Console\Command;

class Queue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jukebox:queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get a list of songs in the playlist.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tracks = Playlist::with(['track'])->orderBy('position')->get();
        if ($tracks->isEmpty()) {
            $this->info('The queue is empty.');
            return;
        }
        foreach ($tracks as $track) {
            $isPlaying = $track->playing ? ' (playing)' : '';
            $this->info("{$track->position} - {$track->track->info} {$isPlaying}");
        }
    }
}
