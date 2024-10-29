<?php

namespace App\Custom;

class Status
{
    const ACTIVE = 'active';
    const DEACTIVATED = 'deactivated';
    const LOCKED = 'locked';
    const BLOCKED = 'blocked';
    const SUSPENDED = 'suspended';
    const BLACKLISTED = 'blacklisted';
    const SUCCESS = 'success';
    const FAILED = 'failed';

    const NOT_STARTED = 'not started';
    const PENDING = 'pending';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';

    const ID_VERIFICATION_STATUSES = [
        self::PENDING,
        self::APPROVED,
        self::REJECTED,
        self::NOT_STARTED
    ];

    const KYC_REJECTION_REASONS = [
        'incorrect ghana card number',
        'blurry ghana card',
        'blurry selfie',
        'wrong ghana card image'
    ];
}
