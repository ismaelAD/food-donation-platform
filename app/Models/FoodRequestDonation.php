<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodRequestDonation extends Model
{
    use HasFactory;

    protected $fillable = [
        'food_request_id',
        'user_id',
        'quantity',
        'unit',
        'available_until',
    ];

    public function request()
    {
        return $this->belongsTo(FoodRequest::class, 'food_request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

        public function donations()
    {
        return $this->hasMany(Donation::class);
    }

        public function organization()
    {
        return $this->belongsTo(User::class, 'organization_id');
    }

        public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    public function foodRequest() {
        return $this->belongsTo(FoodRequest::class);
    }


}
