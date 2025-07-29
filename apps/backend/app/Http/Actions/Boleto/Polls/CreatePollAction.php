<?php

declare(strict_types=1);

namespace HiEvents\Http\Actions\Boleto\Polls;

use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Boleto\Polls\PollService;
use HiEvents\Models\EventPoll;
use HiEvents\Exceptions\CannotCheckInException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CreatePollAction extends BaseAction
{
    public function __construct(
        private readonly PollService $pollService,
    )
    {
    }

    public function __invoke(Request $request, int $eventId): JsonResponse
    {
        // Validate the request
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'type' => [
                'required',
                'string',
                Rule::in(EventPoll::AVAILABLE_TYPES)
            ],
            'options' => 'required_if:type,multiple_choice|array|min:2|max:10',
            'options.*.text' => 'required_with:options|string|max:200',
            'options.*.color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'allows_multiple' => 'boolean',
            'is_anonymous' => 'boolean',
            'closes_at' => 'nullable|date|after:now'
        ]);

        try {
            $poll = $this->pollService->createPoll([
                'event_id' => $eventId,
                'user_id' => $this->getAuthenticatedUser()->getId(),
                'question' => $validated['question'],
                'type' => $validated['type'],
                'options' => $validated['options'] ?? null,
                'allows_multiple' => $validated['allows_multiple'] ?? false,
                'is_anonymous' => $validated['is_anonymous'] ?? true,
                'closes_at' => $validated['closes_at'] ?? null
            ]);

            return $this->jsonResponse([
                'data' => $poll,
                'message' => __('Poll created successfully')
            ], 201);

        } catch (CannotCheckInException $e) {
            return $this->errorResponse($e->getMessage(), 403);
        }
    }
}