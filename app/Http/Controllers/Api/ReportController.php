<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\ReportingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportingService;

    public function __construct(ReportingService $reportingService)
    {
        $this->middleware('auth:sanctum');
        $this->reportingService = $reportingService;
    }

    public function bookings(Request $request): JsonResponse
    {
        $filters = $request->only(['provider_id', 'service_id', 'start_date', 'end_date']);
        $bookings = $this->reportingService->getBookingsReport($filters);

        return response()->json([
            'data' => $bookings
        ]);
    }

    public function cancellationRates(Request $request): JsonResponse
    {
        $filters = $request->only(['provider_id', 'service_id', 'start_date', 'end_date']);
        $rates = $this->reportingService->getCancellationRates($filters);

        return response()->json([
            'data' => $rates
        ]);
    }

    public function peakHours(Request $request): JsonResponse
    {
        $filters = $request->only(['provider_id', 'service_id', 'start_date', 'end_date']);
        $peakHours = $this->reportingService->getPeakHours($filters);

        return response()->json([
            'data' => $peakHours
        ]);
    }

    public function averageDuration(Request $request): JsonResponse
    {
        $filters = $request->only(['provider_id', 'service_id', 'start_date', 'end_date']);
        $averageDuration = $this->reportingService->getAverageDuration($filters);

        return response()->json([
            'data' => $averageDuration
        ]);
    }

    public function exportBookings(Request $request): JsonResponse
    {
        $filters = $request->only(['provider_id', 'service_id', 'start_date', 'end_date']);
        $bookings = $this->reportingService->getBookingsReport($filters);

        // In a real implementation, you would generate and return a CSV/Excel file
        // This is just a placeholder response
        return response()->json([
            'message' => 'Export functionality would be implemented here',
            'data' => $bookings
        ]);
    }
}
