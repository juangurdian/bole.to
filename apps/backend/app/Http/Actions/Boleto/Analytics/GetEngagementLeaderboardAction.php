<?php

declare(strict_types=1);

namespace HiEvents\Http\Actions\Boleto\Analytics;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Boleto\Analytics\EngagementAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetEngagementLeaderboardAction extends BaseAction
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
            'days' => 'nullable|integer|min:1|max:365'
        ]);

        $leaderboard = $this->analyticsService->getEngagementLeaderboard(
            $eventId,
            $validated['days'] ?? 7
        );

        return $this->jsonResponse([
            'data' => $leaderboard
        ]);
    }
}