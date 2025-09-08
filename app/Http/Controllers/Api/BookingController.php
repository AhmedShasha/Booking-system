<?php

namespace App\Http\Controllers\Api;

use App\Enums\BookingStatus;
use App\Http\Requests\GetAvailableSlotsRequest;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\AvailableSlotResource;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Service;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class BookingController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Booking::class);

        $user = $request->user();
        $bookings = $this->bookingService->getUserBookings($user);

        return response()->json([
            'data' => BookingResource::collection($bookings)
        ]);
    }

    public function show(Booking $booking): JsonResponse
    {
        Gate::authorize('view', $booking);

        return response()->json([
            'data' => new BookingResource($booking->load(['service', 'user', 'service.provider']))
        ]);
    }

    public function availableSlots(Service $service, GetAvailableSlotsRequest $request): JsonResponse
    {
        $slots = $this->bookingService->getAvailableSlots($service, $request->date);

        return response()->json([
            'data' => AvailableSlotResource::collection(collect($slots))
        ]);
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        Gate::authorize('create', Booking::class);

        $service = Service::findOrFail($request->service_id);
        
        $booking = $this->bookingService->createBooking($request->start_time, $service, $request->user());

        return response()->json([
            'message' => 'Booking created successfully',
            'data' => new BookingResource($booking)
        ], 201);
    }

    public function confirm(Booking $booking): JsonResponse
    {
        Gate::authorize('confirm', $booking);

        $this->bookingService->updateStatus($booking, BookingStatus::CONFIRMED);
        $booking->fresh();

        return response()->json([
            'message' => 'Booking confirmed successfully',
            'data' => new BookingResource($booking)
        ]);
    }

    public function cancel(Booking $booking): JsonResponse
    {
        Gate::authorize('cancel', $booking);

        $this->bookingService->updateStatus($booking, BookingStatus::CANCELLED);
        $booking->fresh();

        return response()->json([
            'message' => 'Booking cancelled successfully',
            'data' => new BookingResource($booking)
        ]);
    }
}