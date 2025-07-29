<?php

declare(strict_types=1);

namespace HiEvents\Http\Actions\Boleto\Social;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Boleto\Social\EventFeedService;
use HiEvents\Models\PostReaction;
use HiEvents\Exceptions\CannotCheckInException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AddReactionAction extends BaseAction
{
    public function __construct(
        private readonly EventFeedService $feedService,
    )
    {
    }

    public function __invoke(Request $request, int $eventId, int $postId): JsonResponse
    {
        // Validate the request
        $validated = $request->validate([
            'reaction_type' => [
                'required',
                'string',
                Rule::in(array_keys(PostReaction::AVAILABLE_REACTIONS))
            ]
        ]);

        try {
            $reaction = $this->feedService->addReaction(
                $postId,
                $this->getAuthenticatedUser()->getId(),
                $validated['reaction_type']
            );

            return $this->jsonResponse([
                'data' => $reaction,
                'message' => __('Reaction added')
            ]);

        } catch (CannotCheckInException $e) {
            return $this->errorResponse($e->getMessage(), 403);
        }
    }
}