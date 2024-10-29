<?php

namespace App\Actions\Order;

use App\Http\Resources\OrderResource;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GetOrdersAction
{
    public function handle(Request $request, string $externalId): JsonResponse
    {
        try {
            logger("### LOADING $request->filter [$request->orderType] ORDERS FOR [$externalId] FOR PERIOD [$request->orderDate] ###");

            if (!$request->has('orderType'))
                return errorJsonResponse(errors: ['order type required'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

            if (!in_array($request->orderType, ['vendor', 'user']))
                return errorJsonResponse(errors: ['order type must be vendor or user'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

            $orders = [];
            if ($user = User::findWithExternalId($externalId)) {
                $orders = $this->loadOrders($user, $request);
            }

            return successfulJsonResponse(OrderResource::collection($orders), 'Orders');

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

    private function loadOrders(Model $user, Request $request)
    {
        $orders = $request->orderType === 'user' ? $user->orders() : $user->vendorOrders();

        $ordersFilteredByStatus = $this->filterOrdersByStatus($orders, $request->filter);
        $ordersFilterByDate = $this->filterOrdersByDate($ordersFilteredByStatus, $request->orderDate);

        return $ordersFilterByDate->with('orderCarts.review.reviewable')->orderBy('created_at', 'DESC')->simplePaginate(10);
    }

    private function filterOrdersByStatus($orders, ?string $filterBy)
    {
        return match ($filterBy) {
            null => $orders,
            default => $orders->whereIn('status', explode(',', $filterBy))
        };
    }

    private function filterOrdersByDate($orders, ?string $filterByDate)
    {
        $pattern = '/^(0[1-9]|1[0-2])-(20\d{2})$/';
        if (!preg_match($pattern, $filterByDate)) {
            logger('### MONTH-YEAR IS IN WRONG FORMAT', [$filterByDate]);
            $filterByDate = null;
        }

        return match ($filterByDate) {
            null => $orders,
            default => $orders->whereYear(
                'orders.created_at',
                Carbon::createFromFormat('m-Y', $filterByDate)->year
            )->whereMonth(
                'orders.created_at',
                Carbon::createFromFormat('m-Y', $filterByDate)->month
            )
        };
    }
}
