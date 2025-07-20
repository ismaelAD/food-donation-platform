<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'food_request_id',
        'amount',
    ];

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function foodRequest()
    {
        return $this->belongsTo(FoodRequest::class);
    }
        public function user()
    {
        return $this->belongsTo(User::class);
    }
}
