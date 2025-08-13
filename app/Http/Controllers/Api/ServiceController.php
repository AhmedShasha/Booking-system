<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $services = Service::published()
            ->with('provider')
            ->when($request->category, function($query) use ($request) {
                $query->where('category', $request->category);
            })
            ->when($request->search, function($query) use ($request) {
                $query->where('name', 'like', '%'.$request->search.'%');
            })
            ->paginate(10);

        return response()->json([
            'data' => ServiceResource::collection($services)
        ]);
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        $service = Service::create([
            'provider_id' => $request->user()->id,
            ...$request->validated()
        ]);

        return response()->json([
            'message' => 'Service created successfully',
            'data' => new ServiceResource($service)
        ], 201);
    }

    public function show(Service $service): JsonResponse
    {
        if (!$service->is_published && !Gate::allows('view', $service)) {
            return response()->json([
                'message' => 'Service not available'
            ], 403);
        }

        $service->load('provider');

        return response()->json([
            'data' => new ServiceResource($service)
        ]);
    }

    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $service->update($request->validated());

        return response()->json([
            'message' => 'Service updated successfully',
            'data' => new ServiceResource($service)
        ]);
    }

    public function destroy(Service $service): JsonResponse
    {
        Gate::authorize('delete', $service);

        // Prevent deletion if there are future bookings
        if ($service->bookings()->where('start_time', '>', now())->exists()) {
            return response()->json([
                'message' => 'Cannot delete service with future bookings'
            ], 422);
        }

        $service->delete();

        return response()->json([
            'message' => 'Service deleted successfully'
        ]);
    }

    public function publish(Service $service): JsonResponse
    {
        Gate::authorize('publish', $service);

        $service->update(['is_published' => true]);

        return response()->json([
            'message' => 'Service published successfully',
            'data' => new ServiceResource($service)
        ]);
    }

    public function unpublish(Service $service): JsonResponse
    {
        Gate::authorize('publish', $service);

        $service->update(['is_published' => false]);

        return response()->json([
            'message' => 'Service unpublished successfully',
            'data' => new ServiceResource($service)
        ]);
    }
}