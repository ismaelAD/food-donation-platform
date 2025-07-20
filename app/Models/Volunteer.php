<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'skills',
        'availability',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function donations()
{
    return $this->belongsToMany(
        Donation::class,
        'donation_volunteer',
        'volunteer_id',
        'donation_id'
    )->withPivot('volunteered_at');
}

}
