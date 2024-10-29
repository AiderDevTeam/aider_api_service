<?php

namespace App\Models;

use App\Custom\Identification;
use App\Custom\Status;
use App\Http\Resources\UserResource;
use App\Http\Services\API\IdVerificationService;
use App\Traits\RunCustomQueries;
use AppierSign\RealtimeModel\Traits\RealtimeModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, RunCustomQueries, RealtimeModel;

    const MALE = 'male';
    const FEMALE = 'female';
    const GENDERS = [self::MALE, self::FEMALE, 'other'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',
        'first_name',
        'last_name',
        'email',
        'display_name',
        'birthday',
        'gender',
        'phone',
        'status',
        'calling_code',
        'profile_photo_url',
        'push_notification_token',
        'referral_code',
        'referral_url',
        'device_os',
        'terms_and_conditions',
        'password',
        'voted',
        'id_number',
        'id_type',
        'id_verified',
        'id_verified_at',
        'id_re_verification_status',
        'photo_on_id_url',
        'id_photo_url',
        'selfie_url',
        'signature_url',
        'deactivated_at',
        'has_payout_wallet',
        'id_verification_status',
        'can_see_explainer',
        'can_receive_email_updates',
        'can_receive_push_notifications',
        'can_receive_sms',
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
        'password' => 'hashed',
        'can_receive_email_updates' => 'boolean',
        'can_receive_push_notifications' => 'boolean',
        'can_receive_sms' => 'boolean',
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

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function identifications(): HasMany
    {
        return $this->hasMany(UserIdentification::class);
    }

    public function bvnIdentification(): HasMany
    {
        return $this->identifications()->where('type', Identification::TYPES['BVN']);
    }

    public function documentIdentification(): HasMany
    {
        return $this->identifications()->whereIn('type', Identification::DOCUMENT_TYPES);
    }

    public function hasCompletedBVNIdentification(): bool
    {
        return $this->bvnIdentification()->where('status', Identification::STATUS['ACCEPTED'])->exists();
    }

    public function hasCompletedDocumentIdentification(): bool
    {
        return $this->documentIdentification()->where('status', Identification::STATUS['ACCEPTED'])->exists();
    }

    public function hasCompletedKYC(): bool
    {
        return $this->identifications()->where('status', Identification::STATUS['ACCEPTED'])->exists();
    }

    public function setFirstNameAttribute(string $firstName): void
    {
        $this->attributes['first_name'] = strtolower($firstName);
    }

    public function setLastNameAttribute(string $lastName): void
    {
        $this->attributes['last_name'] = strtolower($lastName);
    }

    public function getFirstNameAttribute(): string
    {
        return ucwords($this->attributes['first_name']);
    }

    public function getLastNameAttribute(string $lastName): string
    {
        return ucwords($this->attributes['last_name']);
    }


    public function getBirthdayAttribute(): ?string
    {
        return !is_null($this->attributes['birthday']) ? Carbon::parse($this->attributes['birthday'])->format('d/m/Y') : null;
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function campaignAccesses(): HasMany
    {
        return $this->hasMany(UserCampaignAccess::class);
    }

    public function hasCampaignAccess(string $campaignType): bool
    {
        return $this->campaignAccesses()->where('campaign_type', $campaignType)->exists();
    }

    public function getTermsAndConditionsAttribute(): bool
    {
        return (boolean)$this->attributes['terms_and_conditions'];
    }

    public function userTypes(): BelongsToMany
    {
        return $this->belongsToMany(UserType::class, 'user_usertype');
    }

    public function setUserType(int $userTypeId): void
    {
        $this->userTypes()->sync([$userTypeId]);
    }

    public function isVendor(): bool
    {
        return $this->userTypes()->where('type', UserType::UserTypes['VENDOR'])->exists();
    }

    public function isRenter(): bool
    {
        return $this->userTypes()->where('type', UserType::UserTypes['RENTER'])->exists();
    }

    public function deactivate(): bool
    {
        return $this->update(['status' => Status::DEACTIVATED]);
    }

    public function isActive(): bool
    {
        return $this->status === Status::ACTIVE;
    }

    public function accountDeactivationLogs(): HasMany
    {
        return $this->hasMany(AccountDeactivationLog::class);
    }

    public function generateExternalId(): string
    {
        return strtoupper(substr($this->first_name, 0, 1)) .
            $this->phone .
            strtoupper(substr($this->last_name, 0, 1));
    }

    public function idVerificationLogs(): HasMany
    {
        return $this->hasMany(IdVerificationLog::class);
    }

    public function getIdVerificationData(): array
    {
        if (!is_null($this->id_number) && $this->id_verification_status === Status::PENDING) {
            if ($verificationData = IdVerificationService::getIdVerificationData($this->id_number)) {
                return [
                    'firstName' => $verificationData['firstName'],
                    'lastName' => $verificationData['lastName'],
                    'birthDate' => $verificationData['birthDate'],
                    'gender' => $verificationData['gender']
                ];
            }
        }
        return [];
    }

    public function getKYCRejectionReason()
    {
        return $this->id_verification_status === Status::REJECTED ?
            $this->idVerificationLogs()->latest()?->first()?->response :
            null;
    }

    public function getSyncKey(): string
    {
        return 'external_id';
    }

    public function toRealtimeData(): UserResource
    {
        return new UserResource($this->load('addresses', 'identifications.rejectionReasons'));
    }

    public function grantCampaignAccess(string $campaignType): void
    {
        if (!$this->hasCampaignAccess($campaignType)) {
            $this->campaignAccesses()->create([
                'external_id' => uniqid('CA'),
                'campaign_type' => $campaignType
            ]);
        }
        manuallySyncModels([$this]);
    }

    public function canReceiveEmailUpdates(): bool
    {
        return $this->can_receive_email_updates;
    }

    public function canReceivePushNotifications(): bool
    {
        return $this->can_receive_push_notifications;
    }

    public function canReceiveSMSNotifications(): bool
    {
        return $this->can_receive_sms;
    }

}
