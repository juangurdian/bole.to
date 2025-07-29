<?php

declare(strict_types=1);

namespace HiEvents\Http\Actions\Boleto\Polls;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Boleto\Polls\PollService;
use HiEvents\Exceptions\CannotCheckInException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetPollResultsAction extends BaseAction
{
    public function __construct(
        private readonly PollService $pollService,
    )
    {
    }

    public function __invoke(Request $request, int $eventId, int $pollId): JsonResponse
    {
        try {
            $results = $this->pollService->getPollResults(
                $pollId,
                $this->getAuthenticatedUser()->getId()
            );

            return $this->jsonResponse([
                'data' => $results
            ]);

        } catch (CannotCheckInException $e) {
            return $this->errorResponse($e->getMessage(), 403);
        }
    }
}