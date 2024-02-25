<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * In real life I would prefer the thin models approach, so I would move the logic from the playlist model to the playlist service provider.
 * But anyway for such a task not supposed to be extended it would be over-engineering.
 * Also the artist_id field is not the only solution and can cause some negative side effects - update/create model somewhere else without validation.
 * I tried to solve it with locked flag. It would be safer to have only track_id in the playlist.
 * But it would require more logic for position calculation.
 */

class Playlist extends Model
{
    use HasFactory;

    /**
     * @var false
     */
    private static bool $locked = true;

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
            self::$locked = false;
            //Need to stop playing all tracks before starting a new one
            self::stopPlaying();

            $playlistItem->playing = true;
            $result = $playlistItem->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
        self::$locked = true;
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
     * Find the highest position for the artists and add track to the end of the artists sublist or to the end of the playlist
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
        self::$locked = false;
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
            DB::rollBack();
        }
        self::$locked = true;
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
     * @return void
     * This flag is used to restrict the model from being updated or created outside
     * of this scope, to prevent incorrect position, playing or artist_id.
     * This is only one of the possible solutions, but it's enough for this task
     */
    protected static function boot(): void
    {
        parent::boot();

        static::updating(fn() => !self::$locked);

        static::creating(fn() => !self::$locked);
    }

    /**
     * @return BelongsTo
     */
    public function track()
    {
        return $this->belongsTo(Track::class);
    }

}
