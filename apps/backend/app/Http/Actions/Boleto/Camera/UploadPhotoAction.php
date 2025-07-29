<?php

declare(strict_types=1);

namespace HiEvents\Http\Actions\Boleto\Camera;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Boleto\Camera\DisposableCameraService;
use HiEvents\Exceptions\CannotCheckInException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UploadPhotoAction extends BaseAction
{
    public function __construct(
        private readonly DisposableCameraService $cameraService,
    )
    {
    }

    public function __invoke(Request $request, int $eventId): JsonResponse
    {
        // Validate the request
        $validated = $request->validate([
            'photo_url' => 'required|url',
            'thumbnail_url' => 'nullable|url',
            'blur_hash' => 'nullable|string|max:50',
            'taken_at' => 'nullable|date'
        ]);

        try {
            $photo = $this->cameraService->uploadPhoto([
                'event_id' => $eventId,
                'user_id' => $this->getAuthenticatedUser()->getId(),
                'photo_url' => $validated['photo_url'],
                'thumbnail_url' => $validated['thumbnail_url'] ?? null,
                'blur_hash' => $validated['blur_hash'] ?? null,
                'taken_at' => isset($validated['taken_at']) ? Carbon::parse($validated['taken_at']) : now()
            ]);

            return $this->jsonResponse([
                'data' => $photo,
                'message' => __('Photo uploaded successfully')
            ], 201);

        } catch (CannotCheckInException $e) {
            return $this->errorResponse($e->getMessage(), 403);
        }
    }
}