<?php

declare(strict_types=1);

namespace HiEvents\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPushToken extends BaseModel
{
    protected function getFillableFields(): array
    {
        return [
            'user_id',
            'account_id',
            'token',
            'platform',
            'device_id',
            'is_active'
        ];
    }

    protected function getCastMap(): array
    {
        return [
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime'
        ];
    }

    // Define platform types
    const PLATFORM_IOS = 'ios';
    const PLATFORM_ANDROID = 'android';
    const PLATFORM_WEB = 'web';

    const AVAILABLE_PLATFORMS = [
        self::PLATFORM_IOS,
        self::PLATFORM_ANDROID,
        self::PLATFORM_WEB
    ];

    /**
     * Get the user this token belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the account this token belongs to
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Scope for active tokens
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive tokens
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope by platform
     */
    public function scopePlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Deactivate this token
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Activate this token
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Check if token is valid for the given platform
     */
    public function isValidForPlatform(string $platform): bool
    {
        return $this->platform === $platform && $this->is_active;
    }

    /**
     * Get display name for platform
     */
    public function getPlatformDisplayNameAttribute(): string
    {
        $displayNames = [
            self::PLATFORM_IOS => 'iOS',
            self::PLATFORM_ANDROID => 'Android',
            self::PLATFORM_WEB => 'Web'
        ];

        return $displayNames[$this->platform] ?? ucfirst($this->platform);
    }
}