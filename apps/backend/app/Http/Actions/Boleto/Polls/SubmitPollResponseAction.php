<?php

declare(strict_types=1);

namespace HiEvents\Http\Actions\Boleto\Polls;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Boleto\Polls\PollService;
use HiEvents\Exceptions\CannotCheckInException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubmitPollResponseAction extends BaseAction
{
    public function __construct(
        private readonly PollService $pollService,
    )
    {
    }

    public function __invoke(Request $request, int $eventId, int $pollId): JsonResponse
    {
        // Validate the request
        $validated = $request->validate([
            'selected_options' => 'required|array|min:1',
            'selected_options.*' => 'required'
        ]);

        try {
            $response = $this->pollService->submitPollResponse([
                'poll_id' => $pollId,
                'user_id' => $this->getAuthenticatedUser()->getId(),
                'selected_options' => $validated['selected_options']
            ]);

            return $this->jsonResponse([
                'data' => $response,
                'message' => __('Response submitted successfully')
            ]);

        } catch (CannotCheckInException $e) {
            return $this->errorResponse($e->getMessage(), 403);
        }
    }
}