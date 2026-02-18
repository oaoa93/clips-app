<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clip\StoreClipRequest;
use App\Http\Requests\Clip\UpdateClipRequest;
use App\Http\Resources\ClipResource;
use App\Models\Clip;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class ClipController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        $clips = Clip::query()
            ->when(
                isset($validated['status']),
                fn ($query) => $query->where('status', $validated['status'])
            )
            ->latest('id')
            ->get();

        return ClipResource::collection($clips);
    }

    public function store(StoreClipRequest $request): JsonResponse
    {
        $clip = Clip::create($request->validated());

        return ClipResource::make($clip)
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateClipRequest $request, Clip $clip): ClipResource
    {
        $clip->update($request->validated());

        return ClipResource::make($clip);
    }

    public function destroy(Clip $clip): Response
    {
        $clip->delete();

        return response()->noContent();
    }
}
