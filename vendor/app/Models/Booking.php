<?php

namespace App\Models;

use App\Custom\BookingStatus;
use App\Traits\PickupAndDropOffStatus;
use App\Traits\RunCustomQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Booking extends Model
{
    use HasFactory, RunCustomQueries;

    protected $fillable = [
        'external_id',
        'user_id',
        'vendor_id',
        'status',
        'collection_amount',
        'collection_status',
        'disbursement_amount',
        'disbursement_status',
        'services_fee',
        'reversal_status',
        'booking_acceptance_status',
        'vendor_pickup_status',
        'user_pickup_status',
        'vendor_drop_off_status',
        'user_drop_off_status',
        'booking_number',
    ];


    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function bookedProduct(): HasOne
    {
        return $this->hasOne(BookingProduct::class);
    }

    public function bookingDates(): HasManyThrough
    {
        return $this->hasManyThrough(BookingProductDate::class, BookingProduct::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function userHasReported(): bool
    {
        return $this->user->hasReportedBooking($this);
    }

    public function vendorHasReported(): bool
    {
        return $this->vendor->hasReportedBooking($this);
    }

    public function message(): HasOne
    {
        return $this->hasOne(Message::class, 'sender_message', 'id');
    }

//    public function conversation(): HasOneThrough
//    {
//        return $this->hasOneThrough(
//            Conversation::class,
//            Message::class,
//            'id',
//            'id');
//    }

    public function isAccepted(): bool
    {
        return $this->booking_acceptance_status === BookingStatus::ACCEPTED;
    }

    public function successful(): void
    {
        $this->updateQuietly(['status' => BookingStatus::SUCCESS]);
    }

    public function collectionSuccessful(): bool
    {
        return $this->collection_status === BookingStatus::COLLECTION['SUCCESS'];
    }

    public function disbursementSuccessful(): bool
    {
        return $this->disbursement_status === BookingStatus::SUCCESS;
    }

    public function collectionFailed(): bool
    {
        return $this->collection_status === BookingStatus::COLLECTION['FAILED'];
    }

    public function pickupSuccessful(): bool
    {
        return ($this->vendor_pickup_status === BookingStatus::SUCCESS
            && $this->user_pickup_status === BookingStatus::SUCCESS);
    }

    public function dropOffSuccessful(): bool
    {
        return ($this->vendor_drop_off_status === BookingStatus::SUCCESS
            && $this->user_drop_off_status === BookingStatus::SUCCESS);
    }

    public function isCompleted(): bool
    {
        return $this->isAccepted() && $this->collectionSuccessful() &&
            $this->pickupSuccessful() && $this->dropOffSuccessful();
    }

    public function fail(): void
    {
        $this->update(['status' => BookingStatus::FAILED]);
    }

    public function failed(): bool
    {
        return $this->status == BookingStatus::FAILED;
    }

    public function wasNotAccepted(): bool
    {
        return in_array($this->booking_acceptance_status, [BookingStatus::CANCELED, BookingStatus::REJECTED]);
    }

    public function userPickupIsSuccessful(): bool
    {
        return $this->user_pickup_status === BookingStatus::SUCCESS;
    }

    public function vendorPickupIsSuccessful(): bool
    {
        return $this->vendor_pickup_status === BookingStatus::SUCCESS;
    }

    public function userDropOffIsSuccessful(): bool
    {
        return $this->user_drop_off_status === BookingStatus::SUCCESS;
    }

    public function vendorDropOffIsSuccessful(): bool
    {
        return $this->vendor_drop_off_status === BookingStatus::SUCCESS;
    }
}
