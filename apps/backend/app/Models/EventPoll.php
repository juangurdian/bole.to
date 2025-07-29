<?php

declare(strict_types=1);

namespace HiEvents\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventPoll extends BaseModel
{
    use SoftDeletes;

    protected function getFillableFields(): array
    {
        return [
            'event_id',
            'created_by',
            'account_id',
            'question',
            'type',
            'options',
            'allows_multiple',
            'is_anonymous',
            'closes_at'
        ];
    }

    protected function getCastMap(): array
    {
        return [
            'options' => 'array',
            'allows_multiple' => 'boolean',
            'is_anonymous' => 'boolean',
            'closes_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime'
        ];
    }

    // Define poll types
    const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    const TYPE_RATING = 'rating';
    const TYPE_OPEN_ENDED = 'open_ended';

    const AVAILABLE_TYPES = [
        self::TYPE_MULTIPLE_CHOICE,
        self::TYPE_RATING,
        self::TYPE_OPEN_ENDED
    ];

    /**
     * Get the event this poll belongs to
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who created this poll
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the account this poll belongs to
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get responses for this poll
     */
    public function responses(): HasMany
    {
        return $this->hasMany(PollResponse::class, 'poll_id');
    }

    /**
     * Check if poll is still open
     */
    public function getIsOpenAttribute(): bool
    {
        return $this->closes_at === null || now()->lt($this->closes_at);
    }

    /**
     * Check if poll is closed
     */
    public function getIsClosedAttribute(): bool
    {
        return !$this->is_open;
    }

    /**
     * Scope for open polls
     */
    public function scopeOpen($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('closes_at')
              ->orWhere('closes_at', '>', now());
        });
    }

    /**
     * Scope for closed polls
     */
    public function scopeClosed($query)
    {
        return $query->where('closes_at', '<=', now());
    }

    /**
     * Get response count for this poll
     */
    public function getResponseCountAttribute(): int
    {
        return $this->responses()->count();
    }

    /**
     * Check if user has responded to this poll
     */
    public function hasUserResponded(int $userId): bool
    {
        return $this->responses()->where('user_id', $userId)->exists();
    }

    /**
     * Get response statistics for multiple choice polls
     */
    public function getResponseStats(): array
    {
        if ($this->type !== self::TYPE_MULTIPLE_CHOICE) {
            return [];
        }

        $responses = $this->responses()->get();
        $stats = [];

        foreach ($this->options as $option) {
            $count = $responses->where('selected_options', 'like', '%"' . $option['id'] . '"%')->count();
            $stats[$option['id']] = [
                'text' => $option['text'],
                'count' => $count,
                'percentage' => $responses->count() > 0 ? round(($count / $responses->count()) * 100, 1) : 0
            ];
        }

        return $stats;
    }
}