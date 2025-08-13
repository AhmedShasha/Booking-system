<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkUpdateAvailabilityRequest;
use App\Http\Requests\StoreAvailabilityRequest;
use App\Http\Requests\UpdateAvailabilityRequest;
use App\Http\Resources\AvailabilityResource;
use App\Models\Availability;
use App\Services\AvailabilityService;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class AvailabilityController extends Controller
{
    protected $availabilityService;
    protected $user;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->middleware('auth:sanctum');
        $this->availabilityService = $availabilityService;
        $this->user = Auth::user();
    }

    public function index(): JsonResponse
    {
        $availabilities = $this->user->availabilities;

        return response()->json([
            'data' => AvailabilityResource::collection($availabilities)
        ]);
    }

    public function store(StoreAvailabilityRequest $request): JsonResponse
    {
        $availability = $this->user->availabilities()->create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Availability created successfully',
            'data' => new AvailabilityResource($availability)
        ], 201);
    }

    public function show(Availability $availability): JsonResponse
    {
        Gate::authorize('view', $availability);

        return response()->json([
            'data' => new AvailabilityResource($availability)
        ]);
    }

    public function update(UpdateAvailabilityRequest $request, Availability $availability): JsonResponse
    {
        $availability->update($request->validated());

        return response()->json([
            'message' => 'Availability updated successfully',
            'data' => new AvailabilityResource($availability)
        ]);
    }

    public function destroy(Availability $availability): JsonResponse
    {
        Gate::authorize('delete', $availability);

        // Check for future bookings before deleting
        if ($this->availabilityService->hasFutureBookings($availability)) {
            return response()->json([
                'message' => 'Cannot delete availability with future bookings'
            ], 422);
        }

        $availability->delete();

        return response()->json([
            'message' => 'Availability deleted successfully'
        ]);
    }


    public function bulkUpdate(BulkUpdateAvailabilityRequest $request): JsonResponse
    {
        $this->user->availabilities()->delete();

        // Create new ones
        $availabilities = collect($request->validated()['availabilities'])
            ->map(function ($item) {
                return $this->user->availabilities()->create($item);
            });

        return response()->json([
            'message' => 'Availabilities updated successfully',
            'data' => AvailabilityResource::collection($availabilities)
        ]);
    }
}
