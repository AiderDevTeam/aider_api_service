<?php

namespace App\Enum;

enum Status: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case STARTED =  'started';
    case COLLECTED = 'collected';
    case REVERSED = 'reversed';
    case COMPLETED = 'completed';
    case REVERSING = 'reversing';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case ACTIVATED = 'activated';
    case DEACTIVATED = 'deactivated';
    case EXPIRED = 'expired';
}
