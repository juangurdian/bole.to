<?php

declare(strict_types=1);

namespace HiEvents\Http\Actions\Boleto\Social;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Boleto\Social\EventFeedService;
use HiEvents\Exceptions\CannotCheckInException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CreatePostAction extends BaseAction
{
    public function __construct(
        private readonly EventFeedService $feedService,
    )
    {
    }

    public function __invoke(Request $request, int $eventId): JsonResponse
    {
        // Validate the request
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'type' => 'in:text,image,announcement',
            'media_url' => 'nullable|url',
            'reply_to_id' => 'nullable|integer|exists:event_posts,id'
        ]);

        try {
            $post = $this->feedService->createPost([
                'event_id' => $eventId,
                'user_id' => $this->getAuthenticatedUser()->getId(),
                'content' => $validated['content'],
                'type' => $validated['type'] ?? 'text',
                'media_url' => $validated['media_url'] ?? null,
                'reply_to_id' => $validated['reply_to_id'] ?? null
            ]);

            return $this->jsonResponse([
                'data' => $post,
                'message' => __('Post created successfully')
            ], 201);

        } catch (CannotCheckInException $e) {
            return $this->errorResponse($e->getMessage(), 403);
        }
    }
}