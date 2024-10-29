<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdVerificationLog extends Model
{
    use HasFactory;

    protected $fillable=['external_id', 'user_id', 'status', 'response'];

    public function user():BelongsTo{
        return $this->belongsTo(User::class);
    }
}
