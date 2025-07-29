<?php

declare(strict_types=1);

namespace HiEvents\Services\Boleto\Analytics;

use HiEvents\Models\Event;
use HiEvents\Models\EventPost;
use HiEvents\Models\PostReaction;
use HiEvents\Models\EventPhoto;
use HiEvents\Models\EventPoll;
use HiEvents\Models\PollResponse;
use HiEvents\Models\EventEngagementAnalytic;
use HiEvents\Models\Attendee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EngagementAnalyticsService
{
    public function __construct()
    {
    }

    /**
     * Update analytics for a specific event and date
     */
    public function updateEventAnalytics(int $eventId, Carbon $date): EventEngagementAnalytic
    {
        $event = Event::findOrFail($eventId);
        
        // Calculate metrics for the given date
        $totalPosts = EventPost::where('event_id', $eventId)
            ->whereDate('created_at', $date)
            ->count();

        $totalReactions = PostReaction::whereIn('post_id', function ($query) use ($eventId) {
                $query->select('id')
                    ->from('event_posts')
                    ->where('event_id', $eventId);
            })
            ->whereDate('created_at', $date)
            ->count();

        $totalPhotos = EventPhoto::where('event_id', $eventId)
            ->whereDate('created_at', $date)
            ->count();

        // Calculate unique engaged users (posted, reacted, or uploaded photos)
        $uniqueEngagedUsers = $this->calculateUniqueEngagedUsers($eventId, $date);

        // Calculate poll participation rate
        $pollParticipationRate = $this->calculatePollParticipationRate($eventId, $date);

        // Update or create analytics record
        return EventEngagementAnalytic::updateForEvent($eventId, $date, [
            'total_posts' => $totalPosts,
            'total_reactions' => $totalReactions,
            'total_photos' => $totalPhotos,
            'unique_engaged_users' => $uniqueEngagedUsers,
            'poll_participation_rate' => $pollParticipationRate
        ]);
    }

    /**
     * Get engagement analytics for an event over a date range
     */
    public function getEventAnalytics(
        int $eventId, 
        Carbon $startDate, 
        Carbon $endDate
    ): array {
        $analytics = EventEngagementAnalytic::where('event_id', $eventId)
            ->dateRange($startDate, $endDate)
            ->orderBy('date')
            ->get();

        return [
            'analytics' => $analytics,
            'summary' => $this->calculateAnalyticsSummary($analytics),
            'trends' => $this->calculateTrends($analytics)
        ];
    }

    /**
     * Get real-time engagement metrics for an event
     */
    public function getRealTimeMetrics(int $eventId): array
    {
        $today = now();
        
        // Today's metrics
        $todayPosts = EventPost::where('event_id', $eventId)
            ->whereDate('created_at', $today)
            ->count();

        $todayReactions = PostReaction::whereIn('post_id', function ($query) use ($eventId) {
                $query->select('id')
                    ->from('event_posts')
                    ->where('event_id', $eventId);
            })
            ->whereDate('created_at', $today)
            ->count();

        $todayPhotos = EventPhoto::where('event_id', $eventId)
            ->whereDate('created_at', $today)
            ->count();

        // Last hour metrics
        $lastHourPosts = EventPost::where('event_id', $eventId)
            ->where('created_at', '>=', $today->copy()->subHour())
            ->count();

        $lastHourReactions = PostReaction::whereIn('post_id', function ($query) use ($eventId) {
                $query->select('id')
                    ->from('event_posts')
                    ->where('event_id', $eventId);
            })
            ->where('created_at', '>=', $today->copy()->subHour())
            ->count();

        // Active users (posted or reacted in last hour)
        $activeUsers = $this->getActiveUsers($eventId, $today->copy()->subHour());

        return [
            'today' => [
                'posts' => $todayPosts,
                'reactions' => $todayReactions,
                'photos' => $todayPhotos
            ],
            'last_hour' => [
                'posts' => $lastHourPosts,
                'reactions' => $lastHourReactions,
                'active_users' => count($activeUsers)
            ],
            'active_users' => $activeUsers
        ];
    }

    /**
     * Get leaderboard of most engaged users
     */
    public function getEngagementLeaderboard(int $eventId, int $days = 7): array
    {
        $since = now()->subDays($days);

        // Get user engagement scores
        $userEngagement = DB::table('users')
            ->select([
                'users.id',
                'users.first_name',
                'users.last_name',
                DB::raw('COUNT(DISTINCT event_posts.id) + COUNT(DISTINCT post_reactions.id) + COUNT(DISTINCT event_photos.id) as engagement_score'),
                DB::raw('COUNT(DISTINCT event_posts.id) as posts_count'),
                DB::raw('COUNT(DISTINCT post_reactions.id) as reactions_count'),
                DB::raw('COUNT(DISTINCT event_photos.id) as photos_count')
            ])
            ->leftJoin('event_posts', function ($join) use ($eventId, $since) {
                $join->on('users.id', '=', 'event_posts.user_id')
                    ->where('event_posts.event_id', $eventId)
                    ->where('event_posts.created_at', '>=', $since);
            })
            ->leftJoin('post_reactions', function ($join) use ($eventId, $since) {
                $join->on('users.id', '=', 'post_reactions.user_id')
                    ->whereIn('post_reactions.post_id', function ($query) use ($eventId) {
                        $query->select('id')
                            ->from('event_posts')
                            ->where('event_id', $eventId);
                    })
                    ->where('post_reactions.created_at', '>=', $since);
            })
            ->leftJoin('event_photos', function ($join) use ($eventId, $since) {
                $join->on('users.id', '=', 'attendees.user_id')
                    ->join('attendees', 'attendees.id', '=', 'event_photos.attendee_id')
                    ->where('event_photos.event_id', $eventId)
                    ->where('event_photos.created_at', '>=', $since);
            })
            ->whereIn('users.id', function ($query) use ($eventId) {
                $query->select('user_id')
                    ->from('attendees')
                    ->where('event_id', $eventId);
            })
            ->groupBy('users.id', 'users.first_name', 'users.last_name')
            ->having('engagement_score', '>', 0)
            ->orderByDesc('engagement_score')
            ->limit(10)
            ->get();

        return $userEngagement->toArray();
    }

    /**
     * Calculate unique engaged users for a specific date
     */
    private function calculateUniqueEngagedUsers(int $eventId, Carbon $date): int
    {
        $userIds = collect();

        // Users who posted
        $posters = EventPost::where('event_id', $eventId)
            ->whereDate('created_at', $date)
            ->pluck('user_id');
        $userIds = $userIds->merge($posters);

        // Users who reacted
        $reactors = PostReaction::whereIn('post_id', function ($query) use ($eventId) {
                $query->select('id')
                    ->from('event_posts')
                    ->where('event_id', $eventId);
            })
            ->whereDate('created_at', $date)
            ->pluck('user_id');
        $userIds = $userIds->merge($reactors);

        // Users who uploaded photos
        $photographers = DB::table('event_photos')
            ->join('attendees', 'attendees.id', '=', 'event_photos.attendee_id')
            ->where('event_photos.event_id', $eventId)
            ->whereDate('event_photos.created_at', $date)
            ->pluck('attendees.user_id');
        $userIds = $userIds->merge($photographers);

        return $userIds->unique()->count();
    }

    /**
     * Calculate poll participation rate for a specific date
     */
    private function calculatePollParticipationRate(int $eventId, Carbon $date): ?float
    {
        $totalAttendees = Attendee::where('event_id', $eventId)->count();
        
        if ($totalAttendees === 0) {
            return null;
        }

        $pollResponses = PollResponse::whereIn('poll_id', function ($query) use ($eventId) {
                $query->select('id')
                    ->from('event_polls')
                    ->where('event_id', $eventId);
            })
            ->whereDate('created_at', $date)
            ->distinct('user_id')
            ->count();

        return round(($pollResponses / $totalAttendees) * 100, 2);
    }

    /**
     * Calculate summary statistics for analytics
     */
    private function calculateAnalyticsSummary($analytics): array
    {
        return [
            'total_posts' => $analytics->sum('total_posts'),
            'total_reactions' => $analytics->sum('total_reactions'),
            'total_photos' => $analytics->sum('total_photos'),
            'avg_daily_posts' => $analytics->avg('total_posts'),
            'avg_daily_reactions' => $analytics->avg('total_reactions'),
            'avg_daily_photos' => $analytics->avg('total_photos'),
            'peak_engagement_day' => $analytics->sortByDesc(function ($item) {
                return $item->getTotalActivity();
            })->first()
        ];
    }

    /**
     * Calculate engagement trends
     */
    private function calculateTrends($analytics): array
    {
        if ($analytics->count() < 2) {
            return [];
        }

        $recent = $analytics->take(-7)->avg(function ($item) {
            return $item->getTotalActivity();
        });

        $previous = $analytics->take(-14)->skip(-7)->avg(function ($item) {
            return $item->getTotalActivity();
        });

        $trend = $previous > 0 ? (($recent - $previous) / $previous) * 100 : 0;

        return [
            'trend_percentage' => round($trend, 1),
            'trend_direction' => $trend > 0 ? 'up' : ($trend < 0 ? 'down' : 'stable'),
            'recent_avg' => round($recent, 1),
            'previous_avg' => round($previous, 1)
        ];
    }

    /**
     * Get active users in a time period
     */
    private function getActiveUsers(int $eventId, Carbon $since): array
    {
        return DB::table('users')
            ->select(['users.id', 'users.first_name', 'users.last_name'])
            ->where(function ($query) use ($eventId, $since) {
                $query->whereExists(function ($query) use ($eventId, $since) {
                    $query->select(DB::raw(1))
                        ->from('event_posts')
                        ->whereRaw('event_posts.user_id = users.id')
                        ->where('event_posts.event_id', $eventId)
                        ->where('event_posts.created_at', '>=', $since);
                })
                ->orWhereExists(function ($query) use ($eventId, $since) {
                    $query->select(DB::raw(1))
                        ->from('post_reactions')
                        ->join('event_posts', 'event_posts.id', '=', 'post_reactions.post_id')
                        ->whereRaw('post_reactions.user_id = users.id')
                        ->where('event_posts.event_id', $eventId)
                        ->where('post_reactions.created_at', '>=', $since);
                });
            })
            ->get()
            ->toArray();
    }
}