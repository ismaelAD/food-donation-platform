<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Sponsorship extends Model
{
    protected $fillable = [
        'partner_id',
        'tier_id',
        'admin_id',
        'start_at',
        'end_at',
        'status',
        'images',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
        'images' => 'array',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function tier()
    {
        return $this->belongsTo(SponsorshipTier::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
    public function isExpired(): bool
{
    return $this->end_at && $this->end_at->lt(now());
}   
public function images()
{
    return $this->hasMany(SponsorshipImage::class);
}

}
