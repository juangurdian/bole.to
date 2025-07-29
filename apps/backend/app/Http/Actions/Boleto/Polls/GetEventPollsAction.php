<?php

declare(strict_types=1);

namespace HiEvents\Http\Actions\Boleto\Polls;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Boleto\Polls\PollService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetEventPollsAction extends BaseAction
{
    public function __construct(
        private readonly PollService $pollService,
    )
    {
    }

    public function __invoke(Request $request, int $eventId): JsonResponse
    {
        $polls = $this->pollService->getEventPolls(
            $eventId,
            $request->get('page', 1),
            $request->get('per_page', 20),
            $request->boolean('open_only', false)
        );

        return $this->jsonResponse([
            'data' => $polls->items(),
            'meta' => [
                'current_page' => $polls->currentPage(),
                'last_page' => $polls->lastPage(),
                'per_page' => $polls->perPage(),
                'total' => $polls->total(),
                'from' => $polls->firstItem(),
                'to' => $polls->lastItem()
            ]
        ]);
    }
}