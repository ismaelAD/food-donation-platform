<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FoodRequestDonation;

class FoodRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'title',
        'description',
        'quantity',
        'target_amount',
        'needed_before',
        'paypal_link',

    ];

    public function organization()
    {
        return $this->belongsTo(User::class, 'organization_id');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }


    public function food_request_donations() {
        return $this->hasMany(FoodRequestDonation::class);
    }

    public function foodRequestDonations()
{
    return $this->hasMany(FoodRequestDonation::class);
}

}
