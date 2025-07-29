<?php

declare(strict_types=1);

namespace HiEvents\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhotoInteraction extends BaseModel
{
    protected function getFillableFields(): array
    {
        return [
            'photo_id',
            'user_id',
            'account_id',
            'type',
            'content',
            'reaction_type'
        ];
    }

    protected function getCastMap(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime'
        ];
    }

    // Define interaction types
    const TYPE_REACTION = 'reaction';
    const TYPE_COMMENT = 'comment';

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
     * Get the photo this interaction belongs to
     */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(EventPhoto::class);
    }

    /**
     * Get the user who made this interaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the account this interaction belongs to
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Scope for reactions only
     */
    public function scopeReactions($query)
    {
        return $query->where('type', self::TYPE_REACTION);
    }

    /**
     * Scope for comments only
     */
    public function scopeComments($query)
    {
        return $query->where('type', self::TYPE_COMMENT);
    }

    /**
     * Get the emoji for this reaction type (if it's a reaction)
     */
    public function getEmojiAttribute(): ?string
    {
        if ($this->type !== self::TYPE_REACTION || !$this->reaction_type) {
            return null;
        }

        return self::AVAILABLE_REACTIONS[$this->reaction_type] ?? '👍';
    }
}