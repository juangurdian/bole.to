<?php

declare(strict_types=1);

namespace HiEvents\Http\Actions\Boleto\Camera;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Boleto\Camera\DisposableCameraService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetPhotoStatsAction extends BaseAction
{
    public function __construct(
        private readonly DisposableCameraService $cameraService,
    )
    {
    }

    public function __invoke(Request $request, int $eventId): JsonResponse
    {
        $stats = $this->cameraService->getPhotoStats($eventId);

        return $this->jsonResponse([
            'data' => $stats
        ]);
    }
}