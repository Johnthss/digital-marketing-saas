<?php

namespace App\Models;

use App\Enums\AiActionType;
use App\Enums\AiContentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiContentLog extends Model
{
    protected $fillable = [
        'agency_id',
        'provider',
        'model',
        'action',
        'content_type',
        'prompt',
        'response',
        'total_tokens',
        'prompt_tokens',
        'completion_tokens',
        'cost_usd',
        'status',
        'error_message',
    ];

    protected $casts = [
        'total_tokens' => 'integer',
        'prompt_tokens' => 'integer',
        'completion_tokens' => 'integer',
        'cost_usd' => 'decimal:4',
        'prompt' => 'array',
        'response' => 'array',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function scopeByProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);
    }

    public function getTotalCostAttribute(): float
    {
        return (float) $this->cost_usd;
    }
}
