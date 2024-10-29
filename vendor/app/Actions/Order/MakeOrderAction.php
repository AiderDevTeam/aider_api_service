<?php

namespace App\Actions\Order;

use App\Custom\Status;
use App\Events\OrderPaymentInitializationEvent;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Services\Api\DeliveryPaymentService;
use App\Http\Services\GetAuthUserService;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\User;
use App\Models\Vendor;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class MakeOrderAction
{
    public function handle(Request $request, OrderRequest $orderRequest): JsonResponse
    {
        try {
            $user = User::authUser($request->user);

            logger('### ORDER REQUEST ###');
            logger($orderRequest);

            DB::beginTransaction();
            $cartItems = Cart::whereIn('external_id', $orderRequest['cartExternalIds'])->get();
            $vendorId = $cartItems->first()->vendor_id;

            foreach ($cartItems as $cartItem) {
                if ($cartItem->quantity < 1 || $cartItem->product->quantity < 1) {
                    return errorJsonResponse(errors: [ucwords($cartItem->product->name) . ' is out of stock. Update your cart'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }

            $totalItemAmount = $cartItems->sum(function ($item) {
                return $item->unit_price * $item->quantity;
            });
            $totalDiscountedAmount = $cartItems->sum(fn($item) => ($item->discounted_amount * $item->quantity));

            $vendor = Vendor::where('id', $vendorId)->first();
            $commissionSum = $vendor->commission + $vendor->insurance;

//            $wegoo = DeliveryFee::getWegooDeliveryFee(Delivery::SAME_DAY_DELIVERY);

            $order = Order::create([
                'user_id' => $user->id,
                'vendor_id' => $vendorId,
                'delivery_amount' => $orderRequest['deliveryAmount'],//$wegoo->fee + $wegoo->margin,
                'items_amount' => $totalItemAmount,
                'discounted_amount' => $totalDiscountedAmount,
                'destination' => $orderRequest['destination']['name'],
                'description' => null,
                'recipient_contact' => $orderRequest['recipientContact'] ?? '',
                'recipient_sort_code' => $orderRequest['recipientSortCode'] ?? '',
                'recipient_alternative_contact' => $orderRequest['recipientAlternativeContact'] ?? '',
                'disbursement_amount' => $totalDiscountedAmount * (1 - $commissionSum), //$totalItemAmount - (payout_commission)
                'payout_commission' => ($commissionSum) * $totalDiscountedAmount,
                'order_number' => generateOrderNumber(),
                'amount_paid' => ($totalDiscountedAmount ?? $totalItemAmount) + $orderRequest['deliveryAmount'],
                'pay_on_delivery' => $orderRequest['payOnDelivery'] ?? false
            ]);
            foreach ($cartItems as $cart) {
                $cart->update(['order_id' => $order->external_id, 'is_checked_out' => true]);
            }

            $order->decreaseProductsQuantity();

            logger('### CREATING DELIVERY RECORD ON VENDOR ###');
            $order->delivery()->create([
                'external_id' => uniqid('D'),
                'status' => Status::DELIVERY_STATUS['PENDING'],
                'tracking_number' => null,
                'currency' => "GHC",
                'delivery_option' => $orderRequest['deliveryOption'],
                'is_pickup' => true,
                'is_fulfillment_delivery' => false,
                'amount_to_collect' => null,
                'service' => "intracity",
                'is_prepaid_delivery' => true,
                'pick_up_at' => Carbon::now()
            ]);

            $vendorAddress = $cartItems->first()->vendor->address;

            $order->delivery->origin()->create([
                'origin_name' => $vendorAddress->origin_name ?? $vendorAddress->city,
                'city' => $vendorAddress->city,
                'state' => 'Greater Accra Region',
                'country' => 'Ghana',
                'country_code' => 'Gh',
                'latitude' => $vendorAddress->latitude,
                'longitude' => $vendorAddress->longitude,
            ]);

            $order->delivery->destination()->create([
                'destination_name' => $orderRequest['destination']['name'],
                'city' => $orderRequest['destination']['city'] ?? $orderRequest['destination']['name'],
                'state' => $orderRequest['destination']['state'],
                'country' => $orderRequest['destination']['country'],
                'country_code' => 'GH', //$orderRequest['destination']['countryCode'],
                'latitude' => $orderRequest['destination']['latitude'],
                'longitude' => $orderRequest['destination']['longitude'],
            ]);

            $order->delivery->recipient()->create([
                'name' => $orderRequest['recipient']['name'],
                'phone' => $orderRequest['recipient']['phone']
            ]);

            $order->delivery->sender()->create([
                'name' => $vendor->user->full_name,
                'phone' => $vendor->user->phone
            ]);

            event(new OrderPaymentInitializationEvent($request, $orderRequest, $order, $vendorId, $totalDiscountedAmount));

            logger('### Creating Order Logs ###');
            OrderLog::create([
                'order_number' => $order->order_number
            ]);

            DB::commit();
            return successfulJsonResponse(
                data: new OrderResource($order->load('orderCarts')),
                message: 'Order Created',
                statusCode: 201
            );

        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
        }
        return errorJsonResponse();
    }
}
