<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'preferred_categories',
        'min_quantity',
        'max_distance',
        'available_from',
        'available_until',
    ];

    protected $casts = [
        'preferred_categories' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
