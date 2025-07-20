<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodWasteReport extends Model
{
    use HasFactory;

    // Déclare les colonnes de la table qui peuvent être remplies massivement
    protected $fillable = [
        'user_id',
        'category',
        'quantity',
        'description',
        'latitude',
        'longitude',
        'reported_at',
    ];

    // Si tu utilises des timestamps personnalisés (création/édition)
    public $timestamps = true;

    // Relation avec le modèle User (si tu as un modèle User qui est lié aux rapports)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
