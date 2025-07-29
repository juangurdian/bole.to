<?php

declare(strict_types=1);

namespace HiEvents\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PollResponse extends BaseModel
{
    protected function getFillableFields(): array
    {
        return [
            'poll_id',
            'user_id',
            'attendee_id',
            'account_id',
            'selected_options'
        ];
    }

    protected function getCastMap(): array
    {
        return [
            'selected_options' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime'
        ];
    }

    /**
     * Get the poll this response belongs to
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(EventPoll::class);
    }

    /**
     * Get the user who made this response
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attendee associated with this response
     */
    public function attendee(): BelongsTo
    {
        return $this->belongsTo(Attendee::class);
    }

    /**
     * Get the account this response belongs to
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Check if response contains a specific option
     */
    public function hasSelectedOption($optionId): bool
    {
        if (!is_array($this->selected_options)) {
            return false;
        }

        return in_array($optionId, $this->selected_options);
    }

    /**
     * Get formatted response text for display
     */
    public function getFormattedResponseAttribute(): string
    {
        if (!is_array($this->selected_options)) {
            return '';
        }

        $poll = $this->poll;
        
        if ($poll->type === EventPoll::TYPE_MULTIPLE_CHOICE) {
            $selectedTexts = [];
            foreach ($this->selected_options as $optionId) {
                $option = collect($poll->options)->firstWhere('id', $optionId);
                if ($option) {
                    $selectedTexts[] = $option['text'];
                }
            }
            return implode(', ', $selectedTexts);
        }

        if ($poll->type === EventPoll::TYPE_RATING) {
            return implode(', ', $this->selected_options);
        }

        if ($poll->type === EventPoll::TYPE_OPEN_ENDED) {
            return implode(' | ', $this->selected_options);
        }

        return '';
    }
}