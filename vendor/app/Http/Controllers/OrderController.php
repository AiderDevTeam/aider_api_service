<?php

namespace App\Http\Controllers;

use App\Actions\DeliveryFee\CreateDeliveryFeeAction;
use App\Actions\DeliveryFee\GetDeliveryFeeAction;
use App\Actions\DeliveryFee\UpdateDeliveryFeeAction;
use App\Actions\Order\AcceptOrderAction;
use App\Actions\Order\CheckOrderCollectionStatusAction;
use App\Actions\Order\GetExpressDeliveryFeeAction;
use App\Actions\Order\GetOrdersAction;
use App\Actions\Order\MakeOrderAction;
use App\Actions\Order\ManualOrderUpdateAction;
use App\Actions\Order\UpdateOrderAction;
use App\Actions\Order\UpdateOrderStatusAction;
use App\Actions\WebActions\WebMakeOrderAction;
use App\Http\Requests\AcceptOrderRequest;
use App\Http\Requests\CheckOrderCollectionStatusRequest;
use App\Http\Requests\CreateDeliveryFeeRequest;
use App\Http\Requests\ExpressDeliveryFeeRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Http\Requests\UpdateDeliveryFeeRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\DeliveryFee;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Actions\DeliveryFee\CheckoutDeliveryAction;

class OrderController extends Controller
{
    public function store(Request $request, MakeOrderAction $action, OrderRequest $orderRequest): JsonResponse
    {
        return $action->handle($request, $orderRequest);
    }
    public function webStore(Request $request, WebMakeOrderAction $action, OrderRequest $orderRequest): JsonResponse
    {
        return $action->handle($request, $orderRequest);
    }

    public function checkOrderCollectionStatus(CheckOrderCollectionStatusRequest $request, CheckOrderCollectionStatusAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function update(UpdateOrderRequest $orderRequest, UpdateOrderAction $action): JsonResponse
    {
        return $action->handle($orderRequest);
    }

    public function delivery(UpdateOrderStatusRequest $orderRequest, UpdateOrderStatusAction $action): JsonResponse
    {
        return $action->handle($orderRequest);
    }

    public function getWegooExpressDeliveryFee(ExpressDeliveryFeeRequest $request, GetExpressDeliveryFeeAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function acceptOrder(AcceptOrderRequest $request, AcceptOrderAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function createDeliveryFee(CreateDeliveryFeeAction $action, CreateDeliveryFeeRequest $createDeliveryFeeRequest): JsonResponse
    {
        return $action->handle($createDeliveryFeeRequest);
    }

    public function getDeliveryFee(GetDeliveryFeeAction $action): JsonResponse
    {
        return $action->handle();
    }

    public function updateDeliveryFee(DeliveryFee $deliveryFee, UpdateDeliveryFeeAction $action, UpdateDeliveryFeeRequest $request): JsonResponse
    {
        return $action->handle($request, $deliveryFee);
    }

    public function manualOrderUpdate(OrderUpdateRequest $request, ManualOrderUpdateAction $action, Order $order): JsonResponse
    {
        return $action->handle($request, $order);
    }

    public function getOrders(Request $request, string $externalId, GetOrdersAction $action): JsonResponse
    {
        return $action->handle($request, $externalId);
    }

    public function checkoutDelivery(CheckoutDeliveryAction $action): JsonResponse
    {
        return $action->handle();
    }
}
