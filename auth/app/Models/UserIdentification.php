<?php

namespace App\Models;

use App\Custom\Identification;
use App\Jobs\IdentificationVerificationJob;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserIdentification extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'user_id',
        'id_number',
        'document_url',
        'selfie_url',
        'verification_details',
        'type',
        'status'
    ];

    protected $casts = [
        'verification_details' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function accept(): void
    {
        $this->update(['status' => Identification::STATUS['ACCEPTED']]);
    }

    public function setVerificationDetails(?array $data): void
    {
        $this->attributes['verification_details'] = json_encode($data);

    }

    public function getVerificationDetails()
    {
        return json_decode($this->attributes['verification_details'], true);

    }

    public function reject(): void
    {
        $this->update(['status' => Identification::STATUS['REJECTED']]);
    }

    public function rejectionReasons(): HasMany
    {
        return $this->hasMany(UserIdentificationRejection::class);
    }

    public function lastRejectionReason(): Model|HasMany|null
    {
        return $this->rejectionReasons()->latest()->first();
    }

    public function isIdNumberWithSelfieVerification(): bool
    {
        return in_array($this->type, [Identification::TYPES['BVN'], Identification::TYPES['NIN']]);
    }

    public function isDocumentIdentificationWithSelfie(): bool
    {
        return $this->type === Identification::TYPES['NIN'];
    }

    public function isDocumentIdentification(): bool
    {
        return in_array($this->type, array_values(Identification::DOCUMENT_TYPES));
    }

    public function formatType(): string
    {
        return match ($this->type) {
            Identification::TYPES['NIN'] => 'NIN',
            Identification::TYPES['BVN'] => 'BVN',
            Identification::TYPES['DRIVER_LICENSE'] => 'DL',
            Identification::TYPES['PASSPORT'] => 'PP'
        };
    }

    public function process(array $filePaths): void
    {
        IdentificationVerificationJob::dispatch(
            $this->refresh(),
            [
                ...$filePaths,
                'base64Selfie' => fileToBase64String($filePaths['selfie']),
                'base64DocumentImage' => fileToBase64String($filePaths['documentImage']),
            ]
        )->onQueue('high');
    }
}
