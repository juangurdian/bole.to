<?php

declare(strict_types=1);

namespace HiEvents\Services\Boleto\Social;

use HiEvents\Models\Event;
use HiEvents\Models\EventPost;
use HiEvents\Models\PostReaction;
use HiEvents\Models\User;
use HiEvents\Models\Attendee;
use HiEvents\Models\Account;
use HiEvents\Exceptions\CannotCheckInException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class EventFeedService
{
    public function __construct()
    {
    }

    /**
     * Get paginated feed for an event
     */
    public function getEventFeed(
        int $eventId, 
        int $page = 1, 
        int $perPage = 20,
        ?int $userId = null
    ): LengthAwarePaginator {
        $posts = EventPost::where('event_id', $eventId)
            ->mainPosts() // Only get main posts, not replies
            ->with(['replies' => function ($query) {
                $query->latest()->limit(3);
            }])
            ->withCount('replies')
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page);

        // Add user reaction status if user is logged in
        if ($userId) {
            $posts->each(function ($post) use ($userId) {
                $post->user_reactions = $post->reactions()
                    ->where('user_id', $userId)
                    ->pluck('reaction_type')
                    ->toArray();
            });
        }

        return $posts;
    }

    /**
     * Create a new post in the event feed
     */
    public function createPost(array $data): EventPost
    {
        return DB::transaction(function () use ($data) {
            // Verify user is attendee of the event
            if (!$this->canUserPostInEvent($data['user_id'], $data['event_id'])) {
                throw new CannotCheckInException(
                    __('You must have a ticket to post in this event')
                );
            }

            // Get attendee record
            $attendee = Attendee::where('event_id', $data['event_id'])
                ->where('user_id', $data['user_id'])
                ->first();

            // Get event to get account_id
            $event = Event::findOrFail($data['event_id']);

            $post = EventPost::create([
                'event_id' => $data['event_id'],
                'user_id' => $data['user_id'],
                'attendee_id' => $attendee->id ?? null,
                'account_id' => $event->account_id,
                'content' => $data['content'],
                'type' => $data['type'] ?? 'text',
                'media_url' => $data['media_url'] ?? null,
                'is_organizer_post' => $this->isUserOrganizer($data['user_id'], $data['event_id']),
                'reply_to_id' => $data['reply_to_id'] ?? null
            ]);

            // TODO: Trigger event for real-time updates
            // event(new \HiEvents\Events\Boleto\PostCreatedEvent($post));

            return $post->load(['user', 'attendee']);
        });
    }

    /**
     * Add reaction to a post
     */
    public function addReaction(int $postId, int $userId, string $reactionType): PostReaction
    {
        return DB::transaction(function () use ($postId, $userId, $reactionType) {
            $post = EventPost::findOrFail($postId);
            
            // Verify user can react (must be attendee)
            if (!$this->canUserPostInEvent($userId, $post->event_id)) {
                throw new CannotCheckInException(
                    __('You must have a ticket to react to posts')
                );
            }

            // Get attendee record
            $attendee = Attendee::where('event_id', $post->event_id)
                ->where('user_id', $userId)
                ->first();

            // Remove existing reaction if any
            PostReaction::where('post_id', $postId)
                ->where('user_id', $userId)
                ->delete();

            // Add new reaction
            $reaction = PostReaction::create([
                'post_id' => $postId,
                'user_id' => $userId,
                'attendee_id' => $attendee->id ?? null,
                'account_id' => $post->account_id,
                'reaction_type' => $reactionType
            ]);

            // TODO: Trigger event for real-time updates
            // event(new \HiEvents\Events\Boleto\ReactionAddedEvent($reaction));

            return $reaction;
        });
    }

    /**
     * Remove reaction from a post
     */
    public function removeReaction(int $postId, int $userId): bool
    {
        $deleted = PostReaction::where('post_id', $postId)
            ->where('user_id', $userId)
            ->delete();

        if ($deleted) {
            // TODO: Trigger event for real-time updates
            // event(new \HiEvents\Events\Boleto\ReactionRemovedEvent($postId, $userId));
        }

        return $deleted > 0;
    }

    /**
     * Pin/unpin a post (organizer only)
     */
    public function togglePin(int $postId, int $userId): EventPost
    {
        $post = EventPost::findOrFail($postId);
        
        if (!$this->isUserOrganizer($userId, $post->event_id)) {
            throw new CannotCheckInException(
                __('Only organizers can pin posts')
            );
        }

        $post->update(['is_pinned' => !$post->is_pinned]);
        
        return $post;
    }

    /**
     * Check if user can post in event (must be attendee)
     */
    private function canUserPostInEvent(int $userId, int $eventId): bool
    {
        return Attendee::where('event_id', $eventId)
            ->where('user_id', $userId)
            ->exists();
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