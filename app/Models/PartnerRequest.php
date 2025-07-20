<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerRequest extends Model
{
    use HasFactory;

    protected $fillable = ['partner_id', 'user_id', 'status']; // Définis les champs à remplir

    // Si tu veux que la demande de partenariat appartienne à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Si tu veux que la demande de partenariat appartienne à un partenaire
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
