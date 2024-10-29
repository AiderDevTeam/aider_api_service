<?php

namespace App\Http\Actions;

use App\Http\Requests\FileUploadRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FileUploadAction
{
    public function handle(FileUploadRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $filename = Str::uuid();
            if ($request->input('image')) $filename = "$filename.jpg";

            $file = base64ToCloudStorage($request->validated('file'), $filename);

            DB::commit();

            return successfulJsonResponse(data: [
                'file' => (new FileResource($file))
            ],
                message: 'File processed successfully',
                statusCode: Response::HTTP_CREATED);

        } catch (Exception $exception) {
            report($exception);
            DB::rollBack();
        }
        return errorJsonResponse();
    }
}
