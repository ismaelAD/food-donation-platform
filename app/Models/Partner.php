<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Notifications\PartnerLevelUp;
use App\Models\Contribution; // Ensure this exists

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',         // link to users table
        'name',
        'type',
        'contact_email',
        'contact_phone',
        'address',
        'status',
        'level',
        'document_path',  
         'role', 
         'sponsor_level', 
    ];

    /**
     * A Partner belongs to a User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A Partner has many Donations.
     */
    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    /**
     * Accessor to get the public URL of the uploaded document.
     */
    public function getDocumentUrlAttribute(): ?string
    {
        return $this->document_path
            ? Storage::url($this->document_path)
            : null;
    }

    /**
     * Recalculate and update the partner's level based on donation count.
     * Notifies the user if the level changes.
     */
    public function updateLevel(): void
    {
        $count = $this->donations()->count();
        $newLevel = min(floor($count / 10) + 1, 10);

        if ($this->level === $newLevel) {
            return;
        }

        $this->level = $newLevel;
        $this->save();

        // Notify the user that their partner level increased
        $this->user->notify(new PartnerLevelUp($newLevel));
    }
    public function contributions()
{
    return $this->hasMany(Contribution::class, 'partner_id');
}
public function organization()
{
    return $this->belongsTo(Organization::class);
}
public function sponsorships()
{
    return $this->hasMany(\App\Models\Sponsorship::class);
}
public function sponsorship()
{
    return $this->hasOne(Sponsorship::class)->where('status', 'active');
}
public function currentSponsorship()
    {
        return $this->hasOne(\App\Models\Sponsorship::class)
                    ->where('status','active')
                    ->where('end_at','>', now());
    }
     public function getCurrentTier()
    {
        return $this->sponsorships()
                    ->where('status', 'approved')
                    ->latest()
                    ->first()?->tier;
    }   

}
