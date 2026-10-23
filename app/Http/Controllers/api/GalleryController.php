<?php

namespace App\Http\Controllers\api;

use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\ListGalleryRequest;
use App\Http\Resources\GalleryResource;
use App\Models\Gallery;
use Illuminate\Http\JsonResponse;

class GalleryController extends Controller
{
    public function index(ListGalleryRequest $request): JsonResponse
    {
        $galleries = Gallery::query()
            ->active()
            ->with('media')
            ->when(
                $request->validated('type'),
                fn ($query, string $type) => $query->where('type', $type)
            )
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15);

        return ApiResponder::loaded(GalleryResource::collection($galleries));
    }
}
