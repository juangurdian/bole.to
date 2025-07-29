<?php

declare(strict_types=1);

namespace HiEvents\Http\Actions\Boleto\Camera;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Boleto\Camera\DisposableCameraService;
use HiEvents\Exceptions\CannotCheckInException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RevealPhotoAction extends BaseAction
{
    public function __construct(
        private readonly DisposableCameraService $cameraService,
    )
    {
    }

    public function __invoke(Request $request, int $eventId, int $photoId): JsonResponse
    {
        try {
            $photo = $this->cameraService->revealPhoto(
                $photoId,
                $this->getAuthenticatedUser()->getId()
            );

            return $this->jsonResponse([
                'data' => $photo,
                'message' => __('Photo revealed successfully')
            ]);

        } catch (CannotCheckInException $e) {
            return $this->errorResponse($e->getMessage(), 403);
        }
    }
}