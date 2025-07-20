<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Donation;
use App\Models\UserPreference;
use App\Models\partner;
use App\Controller\PartnerController;
use App\Controller\ProfileController;
use Illuminate\Database\Eloquent\Relations\HasOne;







class User extends Authenticatable
{


    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function preference()
    {
        return $this->hasOne(UserPreference::class);
    }


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function partnerProfile(): HasOne
    {
        return $this->hasOne(Partner::class, 'user_id');
    }

    public function volunteerProfile()
    {
        return $this->hasOne(Volunteer::class, 'user_id');
    }

    public function partner()
{
    return $this->hasOne(Partner::class, 'user_id', 'id');
}

    public function foodRequests()
    {
        return $this->hasMany(FoodRequest::class, 'organization_id');
    }

        public function contributions()
    {
        return $this->hasMany(Contribution::class, 'partner_id');
    }

    public function partnerRequestsSent()
{
    return $this->hasMany(PartnerRequest::class, 'user_id')
                ->where('status', 'approved');
}

}
