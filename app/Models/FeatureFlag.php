<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureFlag extends Model
{
    protected $fillable = [
        'feature_key',
        'feature_name',
        'description',
        'enabled',
        'required_plan',
        'minimum_version',
        'allowed_roles',
        'settings',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'allowed_roles' => 'array',
        'settings' => 'array',
        'required_plan' => 'integer',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public function scopeByAgency($query, int $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }
}