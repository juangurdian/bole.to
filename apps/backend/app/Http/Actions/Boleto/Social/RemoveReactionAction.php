<?php

declare(strict_types=1);

namespace HiEvents\Http\Actions\Boleto\Social;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Boleto\Social\EventFeedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RemoveReactionAction extends BaseAction
{
    public function __construct(
        private readonly EventFeedService $feedService,
    )
    {
    }

    public function __invoke(Request $request, int $eventId, int $postId): JsonResponse
    {
        $removed = $this->feedService->removeReaction(
            $postId,
            $this->getAuthenticatedUser()->getId()
        );

        return $this->jsonResponse([
            'message' => $removed ? __('Reaction removed') : __('No reaction to remove')
        ]);
    }
}