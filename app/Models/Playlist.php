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
        return self::query()->update(['playing' => false]);
    }

    /**
     * @return BelongsTo
     */
    public function track()
    {
        return $this->belongsTo(Track::class);
    }

}
