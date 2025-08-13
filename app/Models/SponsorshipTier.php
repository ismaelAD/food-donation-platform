<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SponsorshipTier extends Model
{
    protected $fillable = ['name','price','duration_days','features'];

    protected $casts = [
        'features' => 'array',
    ];

    public function sponsorships()
    {
        return $this->hasMany(Sponsorship::class, 'tier_id');
    }
}
    