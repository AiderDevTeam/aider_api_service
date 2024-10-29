<?php

namespace App\Actions\Webhook;

use App\Models\Incentive;
use Exception;
use Illuminate\Http\Request;

class IncentiveDisbursementWebhookAction
{
    public function handle(Request $request)
    {
        logger('### INCENTIVE CASH DISBURSEMENT WEBHOOK ACTION ###');
        logger($data = $request->all());
        try {

            if (!isset($data['transactionId']) || !isset($data['status'])) {
                logger('### EXPECTED REQUEST PARAMETER NOT SET ###');
                return errorJsonResponse(message: 'expected request parameters not set', statusCode: 422);
            }

            if (!$incentive = Incentive::where('external_id', $data['transactionId'])->first()) {
                logger('### INCENTIVE RECORD NOT FOUND FOR GIVEN EXTERNAL ID ###');
                return errorJsonResponse(message: 'incentive record not found', statusCode: 422);
            }

            logger('### UPDATING INCENTIVE STATUS ###');
            $incentive->update(['status' => $data['status']]);

            return successfulJsonResponse([]);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
