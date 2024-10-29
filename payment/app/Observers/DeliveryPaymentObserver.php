<?php

namespace App\Observers;

use App\Models\DeliveryPayment;

class DeliveryPaymentObserver
{
    public function creating(DeliveryPayment $deliveryPayment): void
    {
        $deliveryPayment->external_id = uniqid();
    }
}
