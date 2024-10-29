<?php

namespace App\Http\Controllers;

use App\Actions\Section\DeleteSectionAction;
use App\Actions\Section\StoreSectionAction;
use App\Actions\Section\UpdateSectionsPositionAction;
use App\Actions\Section\UpdateSectionUpdateAction;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use App\Http\Requests\UpdateSectionsPositionRequest;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return successfulJsonResponse(
            SectionResource::collection(Section::query()->simplePaginate(10))
        );
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSectionRequest $request, StoreSectionAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Section $section): JsonResponse
    {
        return successfulJsonResponse(
            new SectionResource($section)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSectionRequest $request, Section $section, UpdateSectionUpdateAction $action): JsonResponse
    {
        return $action->handle($request, $section);
    }

    public function updateSectionPosition(UpdateSectionsPositionRequest $request, UpdateSectionsPositionAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function destroy(Section $section, DeleteSectionAction $action): JsonResponse
    {
        return $action->handle($section);
    }
}
