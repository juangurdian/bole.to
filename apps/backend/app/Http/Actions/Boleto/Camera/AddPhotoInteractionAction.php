<?php

declare(strict_types=1);

namespace HiEvents\Http\Actions\Boleto\Camera;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Boleto\Camera\DisposableCameraService;
use HiEvents\Models\PhotoInteraction;
use HiEvents\Exceptions\CannotCheckInException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AddPhotoInteractionAction extends BaseAction
{
    public function __construct(
        private readonly DisposableCameraService $cameraService,
    )
    {
    }

    public function __invoke(Request $request, int $eventId, int $photoId): JsonResponse
    {
        // Validate the request
        $validated = $request->validate([
            'type' => [
                'required',
                'string',
                Rule::in([PhotoInteraction::TYPE_REACTION, PhotoInteraction::TYPE_COMMENT])
            ],
            'content' => 'required_if:type,comment|nullable|string|max:500',
            'reaction_type' => [
                'required_if:type,reaction',
                'nullable',
                'string',
                Rule::in(array_keys(PhotoInteraction::AVAILABLE_REACTIONS))
            ]
        ]);

        try {
            $interaction = $this->cameraService->addPhotoInteraction([
                'photo_id' => $photoId,
                'user_id' => $this->getAuthenticatedUser()->getId(),
                'type' => $validated['type'],
                'content' => $validated['content'] ?? null,
                'reaction_type' => $validated['reaction_type'] ?? null
            ]);

            return $this->jsonResponse([
                'data' => $interaction,
                'message' => $validated['type'] === PhotoInteraction::TYPE_REACTION 
                    ? __('Reaction added') 
                    : __('Comment added')
            ], 201);

        } catch (CannotCheckInException $e) {
            return $this->errorResponse($e->getMessage(), 403);
        }
    }
}