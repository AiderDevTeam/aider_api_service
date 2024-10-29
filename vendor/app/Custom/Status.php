<?php

namespace App\Custom;

class Status
{
    const SUCCESS = 'success';
    const PENDING = 'pending';
    const FAILED = 'failed';
    const ACTIVE = 'active';
    const DEACTIVATED = 'deactivated';
    const INACTIVE = 'inactive';
    const ACCEPTED = 'accepted';
    const DECLINED = 'declined';
    const DELIVERY_STATUS = [
        'PENDING' => 'pending',
        'ASSIGNED' => 'assigned',
        'TO_PICKUP' => 'to pickup',
        'PICKED_UP' => 'picked up',
        'TO_RECIPIENT' => 'on it\'s way',
        'DISPATCHED' => 'dispatched',
        'SUCCESS' => 'success',
        'CANCELED' => 'canceled',
        'REJECTED' => 'rejected by recipient',
        'RESCHEDULED' => 'rescheduled',
        'RETURNED' => 'returned',
        'FAILED' => 'failed'
    ];
}
