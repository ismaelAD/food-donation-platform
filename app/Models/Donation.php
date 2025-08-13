<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FoodRequest;



class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'quantity',
        'unit',
        'latitude',
        'longitude',
        'available_until',
        'category',
        'min_quantity',
        'donor_type',
        'available_from',
        'available_to',
        'city',
        'postal_code',
        'expiration_date',
        'partner_id',
        'need_volunteers',
        'volunteer_note',
        'status',
    ];

       protected $casts = [
        'available_until' => 'datetime',
        'available_from' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];
    // Relation vers l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeNotExpired($query)
    {
        return $query->where(function($q){
            $q->whereNull('expiration_date')
            ->orWhere('expiration_date', '>=', now());
        });
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function volunteers()
{
    return $this->belongsToMany(
        Volunteer::class,
        'donation_volunteer',
        'donation_id',
        'volunteer_id'
    )->withPivot('volunteered_at');
}
public function foodRequest()
{
    return $this->belongsTo(FoodRequest::class);
}

// Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
                    ->where('available_until', '>', now());
    }

    public function scopeUrgent($query)
    {
        return $query->where('available_until', '<=', now()->addHours(24));
    }

    // Accesseurs
    public function getIsUrgentAttribute()
    {
        return $this->available_until <= now()->addHours(24);
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
