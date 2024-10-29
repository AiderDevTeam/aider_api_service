<?php

namespace App\Http\Controllers;

use App\Actions\Payment\PaymentCollectionAction;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function collect(Request $request, Booking $booking, PaymentCollectionAction $action): JsonResponse
    {
        return $action->handle($request, $booking);
    }
}
