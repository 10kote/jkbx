<?php

namespace App\Console\Commands\Jukebox;

use App\Models\Playlist;
use Illuminate\Console\Command;

class Playing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jukebox:playing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the currently playing song.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $queueItem = Playlist::where('playing', true)->first();
        if ($queueItem) {
            $this->info("{$queueItem->track->info}");
        } else {
            $this->info('No song is currently playing.');
        }
    }
}
