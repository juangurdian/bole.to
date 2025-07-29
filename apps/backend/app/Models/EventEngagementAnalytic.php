<?php

declare(strict_types=1);

namespace HiEvents\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class EventEngagementAnalytic extends BaseModel
{
    protected function getFillableFields(): array
    {
        return [
            'event_id',
            'account_id',
            'date',
            'total_posts',
            'total_reactions',
            'total_photos',
            'unique_engaged_users',
            'poll_participation_rate'
        ];
    }

    protected function getCastMap(): array
    {
        return [
            'date' => 'date',
            'total_posts' => 'integer',
            'total_reactions' => 'integer',
            'total_photos' => 'integer',
            'unique_engaged_users' => 'integer',
            'poll_participation_rate' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime'
        ];
    }

    /**
     * Get the event this analytic belongs to
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the account this analytic belongs to
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Scope for specific date range
     */
    public function scopeDateRange($query, Carbon $startDate, Carbon $endDate)
    {
        return $query->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
    }

    /**
     * Scope for recent analytics (last N days)
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('date', '>=', now()->subDays($days)->format('Y-m-d'));
    }

    /**
     * Scope for today's analytics
     */
    public function scopeToday($query)
    {
        return $query->where('date', now()->format('Y-m-d'));
    }

    /**
     * Calculate engagement rate
     */
    public function getEngagementRateAttribute(): float
    {
        $totalInteractions = $this->total_posts + $this->total_reactions + $this->total_photos;
        
        if ($this->unique_engaged_users === 0) {
            return 0.0;
        }

        return round($totalInteractions / $this->unique_engaged_users, 2);
    }

    /**
     * Get total activity count
     */
    public function getTotalActivity(): int
    {
        return $this->total_posts + $this->total_reactions + $this->total_photos;
    }

    /**
     * Create or update analytics for a specific event and date
     */
    public static function updateForEvent(int $eventId, Carbon $date, array $data): self
    {
        return static::updateOrCreate(
            [
                'event_id' => $eventId,
                'date' => $date->format('Y-m-d')
            ],
            array_merge($data, [
                'account_id' => Event::find($eventId)->account_id ?? null
            ])
        );
    }

    /**
     * Get analytics summary for an event
     */
    public static function getEventSummary(int $eventId, int $days = 30): array
    {
        $analytics = static::where('event_id', $eventId)
            ->recent($days)
            ->get();

        return [
            'total_posts' => $analytics->sum('total_posts'),
            'total_reactions' => $analytics->sum('total_reactions'),
            'total_photos' => $analytics->sum('total_photos'),
            'avg_unique_engaged_users' => $analytics->avg('unique_engaged_users'),
            'avg_poll_participation_rate' => $analytics->whereNotNull('poll_participation_rate')
                ->avg('poll_participation_rate'),
            'peak_engagement_date' => $analytics->sortByDesc(function ($item) {
                return $item->getTotalActivity();
            })->first()?->date,
            'days_tracked' => $analytics->count()
        ];
    }
}