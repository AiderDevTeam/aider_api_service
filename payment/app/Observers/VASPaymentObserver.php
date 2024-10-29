<?php

namespace App\Observers;

use App\Models\VASPayment;

class VASPaymentObserver
{
    public function creating(VASPayment $vasPayment): void
    {
        $vasPayment->external_id = uniqid();
    }
}
