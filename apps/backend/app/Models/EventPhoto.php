<?php

declare(strict_types=1);

namespace HiEvents\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventPhoto extends BaseModel
{
    use SoftDeletes;

    protected function getFillableFields(): array
    {
        return [
            'event_id',
            'attendee_id',
            'account_id',
            'photo_url',
            'thumbnail_url',
            'blur_hash',
            'taken_at',
            'reveal_at',
            'is_revealed',
            'view_count'
        ];
    }

    protected function getCastMap(): array
    {
        return [
            'is_revealed' => 'boolean',
            'view_count' => 'integer',
            'taken_at' => 'datetime',
            'reveal_at' => 'datetime',
            'created_at' => 'datetime'
        ];
    }

    protected $appends = ['is_viewable'];

    /**
     * Get the event this photo belongs to
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the attendee who took this photo
     */
    public function attendee(): BelongsTo
    {
        return $this->belongsTo(Attendee::class);
    }

    /**
     * Get the account this photo belongs to
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get interactions for this photo
     */
    public function interactions(): HasMany
    {
        return $this->hasMany(PhotoInteraction::class, 'photo_id');
    }

    /**
     * Get only reactions (not comments)
     */
    public function reactions(): HasMany
    {
        return $this->interactions()->where('type', 'reaction');
    }

    /**
     * Get only comments
     */
    public function comments(): HasMany
    {
        return $this->interactions()->where('type', 'comment');
    }

    /**
     * Check if photo is viewable (revealed)
     */
    public function getIsViewableAttribute(): bool
    {
        return $this->is_revealed || now()->gte($this->reveal_at);
    }

    /**
     * Scope for revealed photos
     */
    public function scopeRevealed($query)
    {
        return $query->where(function ($q) {
            $q->where('is_revealed', true)
              ->orWhere('reveal_at', '<=', now());
        });
    }

    /**
     * Increment view count
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Mark photo as revealed
     */
    public function reveal(): bool
    {
        return $this->update(['is_revealed' => true]);
    }
}