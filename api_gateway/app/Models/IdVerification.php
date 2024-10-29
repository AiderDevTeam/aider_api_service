<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdVerification extends Model
{
    use HasFactory;

    const HOME_TOWN = 'hometown';
    const RESIDENCE = 'residence';

    protected $fillable =
        [
            'id_number',
            'type',
            'details',
            'verified',
            'card_id',
            'card_valid_from',
            'card_valid_to',
            'surname',
            'forenames',
            'nationality',
            'birth_date',
            'gender',
            'email',
            'phone_number',
            'birth_country',
            'birth_district',
            'birth_region',
            'birth_town',
            'home_town',
            'home_town_country',
            'home_town_district',
            'home_town_region',
            'residence',
            'residence_street',
            'residence_district',
            'residence_postal_code',
            'residence_region',
            'residence_digital_address',
            'longitude',
            'latitude',
            'occupation',
            'signature_url',
            'photo_on_id_url',
        ];

    public function getRouteKeyName(): string
    {
        return 'id_number';
    }
}
