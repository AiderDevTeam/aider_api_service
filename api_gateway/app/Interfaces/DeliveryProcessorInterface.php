<?php

namespace App\Interfaces;

use App\Models\Delivery;

interface DeliveryProcessorInterface
{
    public function processDelivery(Delivery $delivery): void;
}
