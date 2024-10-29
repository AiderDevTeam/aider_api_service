<?php

namespace App\Http\Controllers;

use App\Http\Actions\GetVerificationDataAction;
use App\Http\Actions\IdVerificationAction;
use App\Http\Actions\Verification\IdNumberAndFaceVerificationAction;
use App\Http\Actions\Verification\DocumentVerificationAction;
use App\Http\Requests\IdNumberAndFaceVerificationRequest;
use App\Http\Requests\DocumentVerificationRequest;
use App\Http\Requests\IdVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IdVerificationController extends Controller
{
    public function __invoke(IdVerificationRequest $request, IdVerificationAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function getVerificationData(Request $request, GetVerificationDataAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function verifyDocument(DocumentVerificationRequest $request, DocumentVerificationAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function verifyWithIdNumberAndFace(IdNumberAndFaceVerificationRequest $request, IdNumberAndFaceVerificationAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
