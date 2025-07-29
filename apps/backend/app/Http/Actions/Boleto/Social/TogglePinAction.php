<?php

declare(strict_types=1);

namespace HiEvents\Http\Actions\Boleto\Social;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Boleto\Social\EventFeedService;
use HiEvents\Exceptions\CannotCheckInException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TogglePinAction extends BaseAction
{
    public function __construct(
        private readonly EventFeedService $feedService,
    )
    {
    }

    public function __invoke(Request $request, int $eventId, int $postId): JsonResponse
    {
        try {
            $post = $this->feedService->togglePin(
                $postId,
                $this->getAuthenticatedUser()->getId()
            );

            return $this->jsonResponse([
                'data' => $post,
                'message' => $post->is_pinned ? __('Post pinned') : __('Post unpinned')
            ]);

        } catch (CannotCheckInException $e) {
            return $this->errorResponse($e->getMessage(), 403);
        }
    }
}