<?php

namespace App\Custom;

enum BookingStatus: string
{
    const PENDING = 'pending';
    const ACCEPTED = 'accepted';
    const REJECTED = 'rejected';
    const ABANDONED = 'abandoned';

    const SUCCESS = 'success';
    const FAILED = 'failed';

    const CANCELED = 'canceled';

    const BOOKING_CONFIRMATION = [
        self::ACCEPTED,
        self::REJECTED,
        self::PENDING,
        self::CANCELED,
    ];

    const COLLECTION = [
        'SUCCESS' => self::SUCCESS,
        'PENDING' => self::PENDING,
        'FAILED' => self::FAILED,
        'ABANDONED' => self::ABANDONED,
    ];
}
