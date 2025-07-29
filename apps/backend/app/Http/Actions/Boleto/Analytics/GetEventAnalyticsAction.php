<?php

declare(strict_types=1);

namespace HiEvents\Http\Actions\Boleto\Analytics;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Boleto\Analytics\EngagementAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GetEventAnalyticsAction extends BaseAction
{
    public function __construct(
        private readonly EngagementAnalyticsService $analyticsService,
    )
    {
    }

    public function __invoke(Request $request, int $eventId): JsonResponse
    {
        // Validate the request
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        $startDate = isset($validated['start_date']) 
            ? Carbon::parse($validated['start_date']) 
            : now()->subDays(30);
            
        $endDate = isset($validated['end_date']) 
            ? Carbon::parse($validated['end_date']) 
            : now();

        $analytics = $this->analyticsService->getEventAnalytics(
            $eventId,
            $startDate,
            $endDate
        );

        return $this->jsonResponse([
            'data' => $analytics
        ]);
    }
}