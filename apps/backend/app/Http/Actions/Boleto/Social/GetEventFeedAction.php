<?php

declare(strict_types=1);

namespace HiEvents\Http\Actions\Boleto\Social;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Boleto\Social\EventFeedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetEventFeedAction extends BaseAction
{
    public function __construct(
        private readonly EventFeedService $feedService,
    )
    {
    }

    public function __invoke(Request $request, int $eventId): JsonResponse
    {
        // Check if user is authenticated (optional for viewing feed)
        $userId = $this->isUserAuthenticated() ? $this->getAuthenticatedUser()->getId() : null;

        $feed = $this->feedService->getEventFeed(
            $eventId,
            $request->get('page', 1),
            $request->get('per_page', 20),
            $userId
        );

        return $this->jsonResponse([
            'data' => $feed->items(),
            'meta' => [
                'current_page' => $feed->currentPage(),
                'last_page' => $feed->lastPage(),
                'per_page' => $feed->perPage(),
                'total' => $feed->total(),
                'from' => $feed->firstItem(),
                'to' => $feed->lastItem()
            ]
        ]);
    }
}