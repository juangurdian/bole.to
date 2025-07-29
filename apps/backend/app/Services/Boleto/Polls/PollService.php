<?php

declare(strict_types=1);

namespace HiEvents\Services\Boleto\Polls;

use HiEvents\Models\Event;
use HiEvents\Models\EventPoll;
use HiEvents\Models\PollResponse;
use HiEvents\Models\Attendee;
use HiEvents\Exceptions\CannotCheckInException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class PollService
{
    public function __construct()
    {
    }

    /**
     * Get paginated polls for an event
     */
    public function getEventPolls(
        int $eventId, 
        int $page = 1, 
        int $perPage = 20,
        bool $openOnly = false
    ): LengthAwarePaginator {
        $query = EventPoll::where('event_id', $eventId)
            ->with(['creator', 'responses' => function ($query) {
                $query->latest()->limit(10);
            }])
            ->withCount('responses')
            ->orderByDesc('created_at');

        if ($openOnly) {
            $query->open();
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Create a new poll
     */
    public function createPoll(array $data): EventPoll
    {
        return DB::transaction(function () use ($data) {
            // Verify user is organizer of the event
            if (!$this->isUserOrganizer($data['user_id'], $data['event_id'])) {
                throw new CannotCheckInException(
                    __('Only organizers can create polls')
                );
            }

            // Get event to get account_id
            $event = Event::findOrFail($data['event_id']);

            // Process options for multiple choice polls
            $options = null;
            if ($data['type'] === EventPoll::TYPE_MULTIPLE_CHOICE && isset($data['options'])) {
                $options = collect($data['options'])->map(function ($option, $index) {
                    return [
                        'id' => $index + 1,
                        'text' => $option['text'],
                        'color' => $option['color'] ?? '#3B82F6'
                    ];
                })->values()->toArray();
            }

            $poll = EventPoll::create([
                'event_id' => $data['event_id'],
                'created_by' => $data['user_id'],
                'account_id' => $event->account_id,
                'question' => $data['question'],
                'type' => $data['type'] ?? EventPoll::TYPE_MULTIPLE_CHOICE,
                'options' => $options,
                'allows_multiple' => $data['allows_multiple'] ?? false,
                'is_anonymous' => $data['is_anonymous'] ?? true,
                'closes_at' => isset($data['closes_at']) ? Carbon::parse($data['closes_at']) : null
            ]);

            // TODO: Trigger event for real-time updates
            // event(new \HiEvents\Events\Boleto\PollCreatedEvent($poll));

            return $poll->load(['creator']);
        });
    }

    /**
     * Submit a response to a poll
     */
    public function submitPollResponse(array $data): PollResponse
    {
        return DB::transaction(function () use ($data) {
            $poll = EventPoll::findOrFail($data['poll_id']);
            
            // Check if poll is still open
            if ($poll->is_closed) {
                throw new CannotCheckInException(
                    __('This poll is closed')
                );
            }

            // Verify user is attendee of the event
            $attendee = Attendee::where('event_id', $poll->event_id)
                ->where('user_id', $data['user_id'])
                ->first();

            if (!$attendee) {
                throw new CannotCheckInException(
                    __('You must have a ticket to respond to polls')
                );
            }

            // Check if user has already responded
            $existingResponse = PollResponse::where('poll_id', $data['poll_id'])
                ->where('user_id', $data['user_id'])
                ->first();

            if ($existingResponse) {
                // Update existing response
                $existingResponse->update([
                    'selected_options' => $data['selected_options']
                ]);
                return $existingResponse;
            }

            // Create new response
            $response = PollResponse::create([
                'poll_id' => $data['poll_id'],
                'user_id' => $data['user_id'],
                'attendee_id' => $attendee->id,
                'account_id' => $poll->account_id,
                'selected_options' => $data['selected_options']
            ]);

            // TODO: Trigger event for real-time updates
            // event(new \HiEvents\Events\Boleto\PollResponseEvent($response));

            return $response;
        });
    }

    /**
     * Get poll results with statistics
     */
    public function getPollResults(int $pollId, int $userId): array
    {
        $poll = EventPoll::findOrFail($pollId);
        
        // Check if user is organizer or poll is not anonymous
        $canViewResults = !$poll->is_anonymous || $this->isUserOrganizer($userId, $poll->event_id);
        
        if (!$canViewResults) {
            throw new CannotCheckInException(
                __('Results for this poll are anonymous')
            );
        }

        $responses = $poll->responses()->with(['user', 'attendee'])->get();
        $stats = $poll->getResponseStats();

        return [
            'poll' => $poll,
            'total_responses' => $responses->count(),
            'response_stats' => $stats,
            'responses' => $poll->is_anonymous ? [] : $responses,
            'user_has_responded' => $poll->hasUserResponded($userId)
        ];
    }

    /**
     * Close a poll (organizer only)
     */
    public function closePoll(int $pollId, int $userId): EventPoll
    {
        $poll = EventPoll::findOrFail($pollId);
        
        if (!$this->isUserOrganizer($userId, $poll->event_id)) {
            throw new CannotCheckInException(
                __('Only organizers can close polls')
            );
        }

        $poll->update(['closes_at' => now()]);
        
        return $poll;
    }

    /**
     * Delete a poll (organizer only)
     */
    public function deletePoll(int $pollId, int $userId): bool
    {
        $poll = EventPoll::findOrFail($pollId);
        
        if (!$this->isUserOrganizer($userId, $poll->event_id)) {
            throw new CannotCheckInException(
                __('Only organizers can delete polls')
            );
        }

        return $poll->delete();
    }

    /**
     * Get poll statistics for an event
     */
    public function getPollStats(int $eventId): array
    {
        $totalPolls = EventPoll::where('event_id', $eventId)->count();
        $openPolls = EventPoll::where('event_id', $eventId)->open()->count();
        $closedPolls = $totalPolls - $openPolls;
        
        $totalResponses = PollResponse::whereIn('poll_id', function ($query) use ($eventId) {
            $query->select('id')
                ->from('event_polls')
                ->where('event_id', $eventId);
        })->count();

        $avgResponsesPerPoll = $totalPolls > 0 ? round($totalResponses / $totalPolls, 1) : 0;

        $mostPopularPoll = EventPoll::where('event_id', $eventId)
            ->withCount('responses')
            ->orderByDesc('responses_count')
            ->first();

        return [
            'total_polls' => $totalPolls,
            'open_polls' => $openPolls,
            'closed_polls' => $closedPolls,
            'total_responses' => $totalResponses,
            'avg_responses_per_poll' => $avgResponsesPerPoll,
            'most_popular_poll' => $mostPopularPoll
        ];
    }

    /**
     * Check if user is organizer of event
     */
    private function isUserOrganizer(int $userId, int $eventId): bool
    {
        $event = Event::find($eventId);
        if (!$event) {
            return false;
        }

        // Check if user belongs to the same account as the event
        return $event->account->users()
            ->where('users.id', $userId)
            ->exists();
    }
}