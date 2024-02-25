<?php

namespace App\ValueObjects;

use App\Models\Track;

class TrackInfo
{
    /**
     * @param Track $track
     */
    private function __construct(private readonly Track $track)
    {
    }

    /**
     * @param Track $track
     *
     * @return TrackInfo
     */public static function fromTrack(Track $track): TrackInfo
    {
        return new self($track);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->track->id}: {$this->track->title} by {$this->track->artist->name}";
    }
}
