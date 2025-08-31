<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'address', 'google_maps_link', 'contact_info', 'availability'
    ];

    // Le propriétaire du lieu
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Les photos attachées au lieu
    public function photos()
    {
        return $this->hasMany(PlacePhoto::class);
        
    }

    // Les demandes faites pour ce lieu
    public function requests()
    {
        return $this->hasMany(PlaceRequest::class);
    }
}
