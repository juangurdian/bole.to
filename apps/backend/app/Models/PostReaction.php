<?php

declare(strict_types=1);

namespace HiEvents\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostReaction extends BaseModel
{
    protected function getFillableFields(): array
    {
        return [
            'post_id',
            'user_id',
            'attendee_id',
            'account_id',
            'reaction_type'
        ];
    }

    protected function getCastMap(): array
    {
        return [
            'created_at' => 'datetime'
        ];
    }

    // Define available reaction types
    const REACTION_LIKE = 'like';
    const REACTION_FIRE = 'fire';
    const REACTION_LAUGH = 'laugh';
    const REACTION_CELEBRATE = 'celebrate';

    const AVAILABLE_REACTIONS = [
        self::REACTION_LIKE => '👍',
        self::REACTION_FIRE => '🔥',
        self::REACTION_LAUGH => '😂',
        self::REACTION_CELEBRATE => '🎉'
    ];

    /**
     * Get the post this reaction belongs to
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(EventPost::class);
    }

    /**
     * Get the user who made this reaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attendee associated with this reaction
     */
    public function attendee(): BelongsTo
    {
        return $this->belongsTo(Attendee::class);
    }

    /**
     * Get the account this reaction belongs to
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the emoji for this reaction type
     */
    public function getEmojiAttribute(): string
    {
        return self::AVAILABLE_REACTIONS[$this->reaction_type] ?? '👍';
    }
}