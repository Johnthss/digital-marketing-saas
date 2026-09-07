<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataDeletionRequest extends Model
{
    protected $fillable = ['user_id', 'reason', 'status', 'scheduled_at', 'processed_at'];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
