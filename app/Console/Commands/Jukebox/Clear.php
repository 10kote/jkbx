<?php

namespace App\Console\Commands\Jukebox;

use App\Models\Playlist;
use Illuminate\Console\Command;

class Clear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jukebox:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the playlist';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Playlist::query()->truncate();
        $this->info('The playlist has been cleared.');
    }
}
