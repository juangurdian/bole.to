<?php

declare(strict_types=1);

namespace HiEvents\Http\Actions\Boleto\Analytics;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Boleto\Analytics\EngagementAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetRealTimeMetricsAction extends BaseAction
{
    public function __construct(
        private readonly EngagementAnalyticsService $analyticsService,
    )
    {
    }

    public function __invoke(Request $request, int $eventId): JsonResponse
    {
        $metrics = $this->analyticsService->getRealTimeMetrics($eventId);

        return $this->jsonResponse([
            'data' => $metrics
        ]);
    }
}