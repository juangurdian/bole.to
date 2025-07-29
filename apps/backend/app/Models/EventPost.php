<?php

declare(strict_types=1);

namespace HiEvents\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventPost extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected function getFillableFields(): array
    {
        return [
            'event_id',
            'user_id',
            'attendee_id',
            'account_id',
            'content',
            'type',
            'media_url',
            'is_pinned',
            'is_organizer_post',
            'reply_to_id'
        ];
    }

    protected function getCastMap(): array
    {
        return [
            'is_pinned' => 'boolean',
            'is_organizer_post' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime'
        ];
    }

    /**
     * Get the event this post belongs to
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who created this post
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attendee associated with this post
     */
    public function attendee(): BelongsTo
    {
        return $this->belongsTo(Attendee::class);
    }

    /**
     * Get the account this post belongs to
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the parent post if this is a reply
     */
    public function parentPost(): BelongsTo
    {
        return $this->belongsTo(EventPost::class, 'reply_to_id');
    }

    /**
     * Get replies to this post
     */
    public function replies(): HasMany
    {
        return $this->hasMany(EventPost::class, 'reply_to_id');
    }

    /**
     * Get reactions for this post
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(PostReaction::class, 'post_id');
    }

    /**
     * Scope for main posts (not replies)
     */
    public function scopeMainPosts($query)
    {
        return $query->whereNull('reply_to_id');
    }

    /**
     * Scope for pinned posts
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Check if the given user has reacted to this post
     */
    public function hasUserReacted($userId, $reactionType = null): bool
    {
        $query = $this->reactions()->where('user_id', $userId);
        
        if ($reactionType) {
            $query->where('reaction_type', $reactionType);
        }
        
        return $query->exists();
    }

    /**
     * Get reaction counts by type
     */
    public function getReactionCountsAttribute(): array
    {
        return $this->reactions()
            ->select('reaction_type', \DB::raw('count(*) as count'))
            ->groupBy('reaction_type')
            ->pluck('count', 'reaction_type')
            ->toArray();
    }
}