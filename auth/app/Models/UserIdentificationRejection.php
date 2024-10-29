<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserIdentificationRejection extends Model
{
    use HasFactory;

    protected $table = 'user_identification_rejections';

    protected $fillable = [
        'user_identification_id',
        'reason'
    ];

    public function userIdentification(): BelongsTo
    {
        return $this->belongsTo(UserIdentification::class);
    }
}
