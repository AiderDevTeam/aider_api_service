<?php

namespace App\Http\Controllers;

use App\Http\Actions\FileUploadAction;
use App\Http\Requests\FileUploadRequest;
use Illuminate\Http\JsonResponse;

class FileUploadController extends Controller
{
    public function __invoke(FileUploadRequest $request, FileUploadAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
