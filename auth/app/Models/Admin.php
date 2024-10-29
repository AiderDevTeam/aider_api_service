<?php

namespace App\Models;

use App\Traits\RunCustomQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
    use HasFactory, RunCustomQueries, HasRoles;

    const MALE = 'male';
    const FEMALE = 'female';
    const GENDERS = [self::MALE, self::FEMALE];

    const ROLES = [
        'ADMIN' => 'admin',
        'FINANCE' => 'finance',
        'CUSTOMER_SERVICE' => 'customer service',
        'MARKETING' => 'marketing',
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $fillable = [
        'external_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'gender',
        'birthday',
        'status'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'externalId' => 'exID'
        ];
    }

    public function generateExternalId(): string
    {
        return 'SA' . $this->phone;
    }

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }
}
