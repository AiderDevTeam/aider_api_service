<?php

namespace App\Custom;

class Status
{
    const PENDING = 'pending';
    const DELIVERY_STATUS = [
        'PENDING' => 'pending',
        'ASSIGNED' => 'assigned',
        'TO_PICKUP' => 'to pickup',
        'PICKED_UP' => 'picked up',
        'TO_RECIPIENT' => 'on it\'s way',
        'DISPATCHED' => 'dispatched',
        'PAID' => 'success',
        'CANCELED' => 'canceled',
        'REJECTED' => 'rejected by recipient',
        'RESCHEDULED' => 'rescheduled',
        'RETURNED' => 'returned',
        'FAILED' => 'failed'
    ];
}
