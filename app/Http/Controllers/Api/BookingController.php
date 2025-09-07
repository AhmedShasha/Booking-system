<?php

namespace App\Http\Controllers\Api;

use App\Enums\BookingStatus;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
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

    public function availableSlots(Service $service, Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today'
        ]);

        $slots = $this->bookingService->getAvailableSlots($service, $request->date);

        return response()->json(['data' => $slots]);
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        $service = Service::findOrFail($request->service_id);
        
        $booking = $this->bookingService->createBooking(
            $request->user(),
            $service,
            $request->start_time
        );

        return response()->json([
            'message' => 'Booking created successfully',
            'data' => new BookingResource($booking)
        ], 201);
    }

    public function confirm(Booking $booking): JsonResponse
    {
        Gate::authorize('update', $booking);

        $this->bookingService->updateStatus($booking, BookingStatus::CONFIRMED);
        $booking->fresh();

        return response()->json([
            'message' => 'Booking confirmed successfully',
            'data' => new BookingResource($booking)
        ]);
    }

    public function cancel(Booking $booking): JsonResponse
    {
        Gate::authorize('update', $booking);

        $this->bookingService->updateStatus($booking, BookingStatus::CANCELLED);
        $booking->fresh();

        return response()->json([
            'message' => 'Booking cancelled successfully',
            'data' => new BookingResource($booking)
        ]);
    }
}