<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'partner_id',
        'admin_id',
        'note',
        'status',
        'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime'
    ];

    /**
     * Relation avec l'utilisateur (si la demande concerne un utilisateur)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le partenaire (si la demande concerne un partenaire)
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Relation avec l'admin qui a fait la demande
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Scope pour les demandes en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour les demandes complétées
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}