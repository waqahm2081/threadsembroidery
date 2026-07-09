<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company_name',
        'phone',
        'address_line1',
        'address_line2',
        'city',
        'postcode',
        'country',
        'avatar_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function brandingEnquiries()
    {
        return $this->hasMany(BrandingEnquiry::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar_path) {
            return asset('storage/'.$this->avatar_path);
        }

        return asset('assets/images/about1.jpg');
    }

    public function getAccountTypeLabelAttribute(): string
    {
        return $this->company_name ? 'Business' : 'Personal';
    }
}
