<?php

declare(strict_types=1);

namespace HiEvents\Http\Actions\Boleto\Camera;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Boleto\Camera\DisposableCameraService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetEventPhotosAction extends BaseAction
{
    public function __construct(
        private readonly DisposableCameraService $cameraService,
    )
    {
    }

    public function __invoke(Request $request, int $eventId): JsonResponse
    {
        $photos = $this->cameraService->getEventPhotos(
            $eventId,
            $request->get('page', 1),
            $request->get('per_page', 20),
            $request->boolean('revealed_only', true)
        );

        return $this->jsonResponse([
            'data' => $photos->items(),
            'meta' => [
                'current_page' => $photos->currentPage(),
                'last_page' => $photos->lastPage(),
                'per_page' => $photos->perPage(),
                'total' => $photos->total(),
                'from' => $photos->firstItem(),
                'to' => $photos->lastItem()
            ]
        ]);
    }
}