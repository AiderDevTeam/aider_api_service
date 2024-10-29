<?php

namespace App\Actions;

use App\Http\Requests\GenerateShareLinkRequest;
use App\Http\Services\GoogleDynamicLinksService;
use App\Models\Product;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\Response;

class GenerateShareLinkAction
{

    const PRODUCT = 'product';
    const VENDOR = 'vendor';

    public function handle(GenerateShareLinkRequest $request)
    {
        try {
            logger('### GENERATING SHARE LINK ###');
            logger($request = $request->validated());

            if (!$model = (new (formatModelName($request['type'])))::findWithExternalId($request['externalId']))
                return errorJsonResponse(errors: [$request['type'] . ' does not exist'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

            if (!$model->setShareLink())
                return errorJsonResponse(errors: ['could not generate link. try again after sometime'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

            return successfulJsonResponse(['shareLink' => $model->refresh()->share_link]);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
