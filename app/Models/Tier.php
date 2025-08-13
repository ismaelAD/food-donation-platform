<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tier extends Model
{
    protected $fillable = ['name', 'price', 'duration_days'];

    public function sponsorships()
    {
        return $this->hasMany(Sponsorship::class);
    }

    public static function levelsOrder()
    {
        return ['Platinum', 'Gold', 'Silver', 'Bronze'];
    }
}
    