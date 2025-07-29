<?php

declare(strict_types=1);

namespace HiEvents\Services\Boleto\Camera;

use HiEvents\Models\Event;
use HiEvents\Models\EventPhoto;
use HiEvents\Models\PhotoInteraction;
use HiEvents\Models\Attendee;
use HiEvents\Exceptions\CannotCheckInException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class DisposableCameraService
{
    public function __construct()
    {
    }

    /**
     * Get paginated photos for an event
     */
    public function getEventPhotos(
        int $eventId, 
        int $page = 1, 
        int $perPage = 20,
        bool $revealedOnly = true
    ): LengthAwarePaginator {
        $query = EventPhoto::where('event_id', $eventId)
            ->with(['attendee.user', 'interactions' => function ($query) {
                $query->latest()->limit(5);
            }])
            ->withCount(['reactions', 'comments'])
            ->orderByDesc('created_at');

        if ($revealedOnly) {
            $query->revealed();
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Upload a new photo to the disposable camera
     */
    public function uploadPhoto(array $data): EventPhoto
    {
        return DB::transaction(function () use ($data) {
            // Verify user is attendee of the event
            $attendee = Attendee::where('event_id', $data['event_id'])
                ->where('user_id', $data['user_id'])
                ->first();

            if (!$attendee) {
                throw new CannotCheckInException(
                    __('You must have a ticket to upload photos to this event')
                );
            }

            // Get event to get account_id and calculate reveal time
            $event = Event::findOrFail($data['event_id']);
            
            // Calculate reveal time (e.g., photos reveal after event ends or after 24 hours, whichever is sooner)
            $revealAt = $this->calculateRevealTime($event);

            $photo = EventPhoto::create([
                'event_id' => $data['event_id'],
                'attendee_id' => $attendee->id,
                'account_id' => $event->account_id,
                'photo_url' => $data['photo_url'],
                'thumbnail_url' => $data['thumbnail_url'] ?? null,
                'blur_hash' => $data['blur_hash'] ?? null,
                'taken_at' => $data['taken_at'] ?? now(),
                'reveal_at' => $revealAt,
                'is_revealed' => false,
                'view_count' => 0
            ]);

            // TODO: Trigger event for real-time updates
            // event(new \HiEvents\Events\Boleto\PhotoUploadedEvent($photo));

            return $photo->load(['attendee']);
        });
    }

    /**
     * Add interaction (reaction or comment) to a photo
     */
    public function addPhotoInteraction(array $data): PhotoInteraction
    {
        return DB::transaction(function () use ($data) {
            $photo = EventPhoto::findOrFail($data['photo_id']);
            
            // Verify user is attendee of the event
            $attendee = Attendee::where('event_id', $photo->event_id)
                ->where('user_id', $data['user_id'])
                ->first();

            if (!$attendee) {
                throw new CannotCheckInException(
                    __('You must have a ticket to interact with photos')
                );
            }

            // For reactions, remove existing reaction first
            if ($data['type'] === PhotoInteraction::TYPE_REACTION) {
                PhotoInteraction::where('photo_id', $data['photo_id'])
                    ->where('user_id', $data['user_id'])
                    ->where('type', PhotoInteraction::TYPE_REACTION)
                    ->delete();
            }

            $interaction = PhotoInteraction::create([
                'photo_id' => $data['photo_id'],
                'user_id' => $data['user_id'],
                'account_id' => $photo->account_id,
                'type' => $data['type'],
                'content' => $data['content'] ?? null,
                'reaction_type' => $data['reaction_type'] ?? null
            ]);

            // TODO: Trigger event for real-time updates
            // event(new \HiEvents\Events\Boleto\PhotoInteractionEvent($interaction));

            return $interaction;
        });
    }

    /**
     * Remove photo interaction
     */
    public function removePhotoInteraction(int $photoId, int $userId, string $type = PhotoInteraction::TYPE_REACTION): bool
    {
        $deleted = PhotoInteraction::where('photo_id', $photoId)
            ->where('user_id', $userId)
            ->where('type', $type)
            ->delete();

        return $deleted > 0;
    }

    /**
     * Manually reveal a photo (organizer only)
     */
    public function revealPhoto(int $photoId, int $userId): EventPhoto
    {
        $photo = EventPhoto::findOrFail($photoId);
        
        if (!$this->isUserOrganizer($userId, $photo->event_id)) {
            throw new CannotCheckInException(
                __('Only organizers can manually reveal photos')
            );
        }

        $photo->reveal();
        
        return $photo;
    }

    /**
     * Get photo statistics for an event
     */
    public function getPhotoStats(int $eventId): array
    {
        $totalPhotos = EventPhoto::where('event_id', $eventId)->count();
        $revealedPhotos = EventPhoto::where('event_id', $eventId)->revealed()->count();
        $pendingPhotos = $totalPhotos - $revealedPhotos;
        
        $mostViewedPhoto = EventPhoto::where('event_id', $eventId)
            ->revealed()
            ->orderByDesc('view_count')
            ->first();

        $totalViews = EventPhoto::where('event_id', $eventId)
            ->revealed()
            ->sum('view_count');

        return [
            'total_photos' => $totalPhotos,
            'revealed_photos' => $revealedPhotos,
            'pending_photos' => $pendingPhotos,
            'total_views' => $totalViews,
            'most_viewed_photo' => $mostViewedPhoto,
            'reveal_percentage' => $totalPhotos > 0 ? round(($revealedPhotos / $totalPhotos) * 100, 1) : 0
        ];
    }

    /**
     * Calculate when photos should be revealed
     */
    private function calculateRevealTime(Event $event): Carbon
    {
        $eventEndTime = $event->end_date;
        $maxWaitTime = now()->addHours(24); // Max 24 hours after upload
        
        // Reveal at event end time or 24 hours from now, whichever is sooner
        if ($eventEndTime && $eventEndTime->lt($maxWaitTime)) {
            return $eventEndTime;
        }
        
        return $maxWaitTime;
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