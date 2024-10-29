<?php

namespace App\Models;

use App\Custom\Status;
use App\Http\Services\Delivery\WegooService;
use App\Interfaces\DeliveryProcessorInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WegooDelivery extends Model implements DeliveryProcessorInterface
{
    use HasFactory;

    protected $table = 'wegoo_delivery_logs';

    protected $fillable = [
        'delivery_id',
        'request_payload',
        'response_payload'
    ];

    public function processDelivery(Delivery $delivery): void
    {
        $response = (new WegooService($delivery))->deliver();

        if ($response && isset($response['data'][0]['status']) && isset($response['data'][0]['tracking_number'])) {
            $deliveryStatus = Status::DELIVERY_STATUS[$response['data'][0]['status']] ?? Status::DELIVERY_STATUS['PENDING'];
            $delivery->update([
                'status' => $deliveryStatus,
                'tracking_number' => $response['data'][0]['tracking_number']
            ]);
        } else
            $delivery->update(['status' => Status::DELIVERY_STATUS['FAILED']]);
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }
}
