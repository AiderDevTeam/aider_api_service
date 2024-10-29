<?php

namespace App\Models;

use App\Http\Resources\MessageResource;
use AppierSign\RealtimeModel\Traits\RealtimeModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes, RealtimeModel;

    const TYPES = ['NUDGE' => 'nudge', 'TEXT' => 'text', 'BOOKING' => 'booking',];

    protected $fillable = [
        'external_id',
        'conversation_id',
        'sender_id',
        'type',
        'sender_message',
        'receiver_message',
        'read_at',
        'conversation_on_going'
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'conversation_on_going' => 'boolean'
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function getCreatedAtAttribute(): string
    {
        return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function isBooking(): bool
    {
        return $this->type === self::TYPES['BOOKING'];
    }

    public function bookingData(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'sender_message', 'id');
    }

    public function toRealtimeData(): MessageResource
    {
        return new MessageResource($this->load(['bookingData.user','bookingData.vendor', 'bookingData.bookedProduct.product.photos', 'bookingData.bookedProduct.product.vendor', 'bookingData.bookedProduct.exchangeSchedule', 'sender']));
    }

}
