<?php

namespace App\Models;

use App\ValueObjects\TrackInfo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Track extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'tracks';
    /**
     * @var array
     */
    protected $with = ['artist'];

    /**
     * @return BelongsTo
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    /**
     * Return full track data
     */
    protected function info(): Attribute
    {
        return Attribute::make(
            get: fn() => TrackInfo::fromTrack($this),
        );
    }

}
