<?php

namespace App\Custom;

enum Identification
{
    const STATUS = [
        'PENDING' => 'pending',
        'ACCEPTED' => 'accepted',
        'REJECTED' => 'rejected',
    ];

    const TYPES = [
        'BVN' => 'bvn',
        'NIN' => 'nin',
        'DRIVER_LICENSE' => 'driver license',
        'PASSPORT' => 'passport',
    ];

    const DOCUMENT_TYPES = [
        'DRIVER_LICENSE' => 'driver license',
        'PASSPORT' => 'passport',
    ];

    const DOCUMENT_WITHOUT_SELFIE_TYPES = [
        'DRIVER_LICENSE' => 'driver license',
        'PASSPORT' => 'passport',
    ];

}
