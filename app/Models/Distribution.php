<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Distribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_id',
        'user_id',
        'quantity_distributed',
        'distribution_date',
        'status',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }
}

