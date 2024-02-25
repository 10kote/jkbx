<?php

namespace App\Console\Commands\Jukebox;

use App\Models\Playlist;
use App\Models\Track;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class Play extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jukebox:play {trackId* : trackID from the list of tracks in the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Play a song or add them into playlist';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $trackIds = array_map('intval', $this->argument('trackId'));
        $tracks = Track::find($trackIds);

        if ($tracks->isEmpty()) {
            $this->error('No tracks found.');
            return;
        }

        $retrievedTrackIds = $tracks->modelKeys();

        $notFound = array_diff($trackIds, $retrievedTrackIds);
        if (!empty($notFound)) {
            $this->error('The following track IDs were not found: ' . implode(', ', $notFound));
        }

        // Tracks in the requested order. There is another way to keep order, but no need to use it here
        $tracksToAddIds = array_intersect($trackIds, $retrievedTrackIds);
        $trackToPlay = $tracks->find(current($tracksToAddIds));

        $tracksInQueue = Playlist::whereIn('track_id', $tracksToAddIds)->get();
        foreach ($tracksInQueue as $trackInQueue) {
            $this->warn("{$trackInQueue->track->info} is already in the queue.");
        }

        $existingTrackIds = $tracksInQueue->pluck('track_id')->toArray();
        $tracksToAddIds = array_diff($tracksToAddIds, $existingTrackIds);

        foreach ($tracks->only($tracksToAddIds) as $track) {
            if (Playlist::addTrack($track)) {
                $this->info("{$track->info} - has been added to the queue.");
            } else {
                $this->error("{$track->info} - unable to add to the queue.");
            }
        }

        if (Playlist::startPlaying($trackToPlay)) {
            $this->info("{$trackToPlay->info} - is now playing.");
        } else {
            $this->error("{$trackToPlay->info} - unable to play.");
        }
    }

}
