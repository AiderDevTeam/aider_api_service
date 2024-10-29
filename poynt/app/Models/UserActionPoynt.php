<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActionPoynt extends Model
{
    use HasFactory;

    const ACTION_POYNT_TYPES = [
        'CREDIT' => 'credit',
        'DEBIT' => 'debit',
    ];

    protected $fillable=[
        'external_id',
        'user_id',
        'action_poynt_id',
        'poynt',
        'action_value',
        'type',
        'action_response_payload',
    ];

    public function setActionResponsePayloadAttribute(array $ActionResponsePayload): void
    {
        $this->attributes['action_response_payload'] = json_encode($ActionResponsePayload);
    }

    public function getActionResponsePayloadAttribute():string
    {
        return json_decode($this->attributes['action_response_payload']);
    }

    public function user():BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function actionPoynt(): BelongsTo
    {
        return $this->belongsTo(ActionPoynt::class);
    }

}
