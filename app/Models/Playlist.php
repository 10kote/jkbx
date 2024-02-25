<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Playlist extends Model
{
    use HasFactory;

    /**
     * @var bool
     */
    public $timestamps = false;
    /**
     * @var string
     */
    protected $table = 'playlist';

    /**
     * @var array
     */
    protected $with = ['track'];
    /**
     * @var string[]
     */
    protected $fillable = ['track_id', 'artist_id', 'position'];

    /**
     * @param Track $track
     * Start playing track
     *
     * @return bool
     */
    public static function startPlaying(Track $track): bool
    {
        $result = false;
        DB::beginTransaction();
        try {
            $playlistItem = self::where(['track_id' => $track->id])->first();
            if (!$playlistItem) {
                throw new \Exception('Track not found in the queue.');
            }

            //Need to stop playing all tracks before starting new one
            self::stopPlaying();

            $playlistItem->playing = true;
            $result = $playlistItem->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
        return $result;
    }

    /**
     * Stop playing tracks
     *
     * @return bool
     */
    public static function stopPlaying(): bool
    {
        return self::query()->update(['playing' => 0]);
    }

    /**
     * Add track to the playlist
     * Find highest position for the artists and add track to the end of the artists sublist
     * @param Track $track
     *
     * @return bool
     */
    public static function addTrack(Track $track): bool
    {
        $maxPositionByArtists = self::select('artist_id', DB::raw('MAX(position) as position_max'))
                                    ->groupBy('artist_id')
                                    ->orderByDesc('position_max')
                                    ->get()->pluck('position_max', 'artist_id')->toArray();
        $position = 0;
        if (count($maxPositionByArtists)) {
            $position = $maxPositionByArtists[$track->artist_id] ?? current($maxPositionByArtists);
        }
        
        DB::beginTransaction();
        try {
            // Increment position for all tracks after the added one
            $position++;
            Playlist::where('position', '>=', $position)->where('track_id', '<>', $track->id)->increment('position');
            if (!self::create([
                                  'track_id'  => $track->id,
                                  'artist_id' => $track->artist_id,
                                  'position'  => $position,
                              ])) {
                throw new \Exception('Failed to add track to the queue.');
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            DB::rollBack();
        }
        return false;
    }

    /**
     * @return self|null
     */
    public static function getPlayingTrack(): ?self
    {
        return self::where('playing', 1)->first();
    }

    /**
     * @return BelongsTo
     */
    public function track()
    {
        return $this->belongsTo(Track::class);
    }

}
