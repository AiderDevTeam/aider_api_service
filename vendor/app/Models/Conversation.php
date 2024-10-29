<?php

namespace App\Models;

use App\Http\Resources\ConversationResource;
use AppierSign\RealtimeModel\Traits\RealtimeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasFactory, SoftDeletes, RealtimeModel;

    protected $fillable = [
        'external_id',
        'user_id',
        'vendor_id',
        'last_message_id',
        'is_on_going'
    ];

    protected $casts = [
        'is_on_going' => 'boolean'
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessageSent(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function userUnreadMessagesCount(): int
    {
        return $this->user->messages()->whereNull('read_at')->count();
    }

    public function vendorUnreadMessagesCount(): int
    {
        return $this->vendor->messages()->whereNull('read_at')->count();
    }

    public function end(): void
    {
        $this->update(['is_on_going' => false]);
    }

    public function isOnGoing()
    {
        return $this->is_on_going;
    }

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function getSyncKey(): string
    {
        return 'external_id';
    }

    public function toRealtimeData(): ConversationResource
    {
        return new ConversationResource($this->load(['user', 'vendor']));
    }
}
