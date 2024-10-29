<?php

namespace App\Models;

use App\Custom\Status;
use App\Http\Services\Delivery\ShaqExpressService;
use App\Interfaces\DeliveryProcessorInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShaqExpressDelivery extends Model implements DeliveryProcessorInterface
{
    use HasFactory;

    const CARS = 'cars';
    const MOTOR_BIKES = 'motor bikes';

    const VEHICLE_TYPES = [
        self::CARS,
        self::MOTOR_BIKES
    ];

    protected $table = 'shaq_express_delivery_logs';

    protected $fillable = [
        'delivery_id',
        'request_payload',
        'response_payload'
    ];

    public function processDelivery(Delivery $delivery): void
    {
        $response = (new ShaqExpressService($delivery))->deliver();
        if ($response && isset($response['data']['booking_id'])) {
            $delivery->update([
                'tracking_number' => $response['data']['booking_id']
            ]);
        } else
            $delivery->update(['status' => Status::DELIVERY_STATUS['FAILED']]);
    }

    public static function getVehicleType(string $vehicleType): ?int
    {
        return match ($vehicleType) {
            ShaqExpressDelivery::MOTOR_BIKES => 1,
            ShaqExpressDelivery::CARS => 3,
            default => null
        };
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }
}
