<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class PlacePhoto extends Model
{
    protected $fillable = ['path', 'place_id'];

   public function getUrlAttribute()
    {
        return Storage::url($this->path);
    }

    public function place() {
        return $this->belongsTo(Place::class);
    }
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($photo) {
            if ($photo->path && Storage::exists($photo->path)) {
                Storage::delete($photo->path);
            }
        });
    }
}

