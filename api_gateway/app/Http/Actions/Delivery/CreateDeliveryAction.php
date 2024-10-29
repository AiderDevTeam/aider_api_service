<?php

namespace App\Http\Actions\Delivery;

use App\Custom\Status;
use App\Events\ProcessDeliveryEvent;
use App\Http\Requests\CreateDeliveryRequest;
use App\Models\Delivery;
use App\Models\DeliveryProcessor;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CreateDeliveryAction
{
    public function handle(CreateDeliveryRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            logger()->info('### CREATING DELIVERY ###');
            logger($data = $request->validated());

            $processor = match ($data['deliveryOption']) {
                Delivery::EXPRESS_DELIVERY => DeliveryProcessor::expressDelivery(), //shaq express
                default => DeliveryProcessor::nextDayDelivery() //wegoo
            };

            logger('Delivery Processor:::' . $processor?->name);

            if (!isset($processor)) {
                logger('### NO ACTIVE DELIVERY PROCESSOR FOUND ###');
                return errorJsonResponse(errors: ['no active delivery processor found'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $delivery = $processor->deliveries()->create([
                'external_id' => $data['deliveryExternalId'],
                'status' => Status::DELIVERY_STATUS['PENDING'],
                'tracking_number' => null,
                'service_webhook' => $data['callbackUrl'],
                'currency' => $data['currency'],
                'delivery_option' => $data['deliveryOption'],
                'is_pickup' => $data['isPickup'],
                'is_fulfillment_delivery' => $data['isFulfillmentDelivery'],
                'amount_to_collect' => $data['amountToCollect'],
                'service' => $data['serviceType'],
                'is_prepaid_delivery' => $data['isPrepaidDelivery'],
                'pick_up_at' => $data['pickUpAt']
            ]);

            $delivery->origin()->create([
                'origin_name' => $data['origin']['name'],
                'city' => $data['origin']['city'],
                'state' => $data['origin']['state'],
                'country' => $data['origin']['country'],
                'country_code' => $data['origin']['countryCode'],
                'latitude' => $data['origin']['latitude'],
                'longitude' => $data['origin']['longitude'],
            ]);

            $delivery->destination()->create([
                'destination_name' => $data['destination']['name'],
                'city' => $data['destination']['city'],
                'state' => $data['destination']['state'],
                'country' => $data['destination']['country'],
                'country_code' => $data['destination']['countryCode'],
                'latitude' => $data['destination']['latitude'],
                'longitude' => $data['destination']['longitude'],
            ]);

            $delivery->recipient()->create([
                'name' => $data['recipient']['name'],
                'phone' => $data['recipient']['phone']
            ]);

            $delivery->sender()->create([
                'name' => $data['sender']['name'],
                'phone' => $data['sender']['phone']
            ]);

            $itemsToInsert = collect($data['items'])->map(function ($itemData) {
                return [
                    'name' => $itemData['name'],
                    'type' => $itemData['type'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                    'weight' => $itemData['weight'],
                ];
            })->toArray();

            $delivery->items()->createMany($itemsToInsert);

            event(new ProcessDeliveryEvent($delivery->refresh()));

            DB::commit();

            return successfulJsonResponse(message: 'Delivery created and pending processing', statusCode: 201);
        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
        }
        return errorJsonResponse();
    }
}
