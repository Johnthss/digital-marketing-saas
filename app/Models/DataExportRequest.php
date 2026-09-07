<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataExportRequest extends Model
{
    protected $fillable = ['user_id', 'export_types', 'status', 'file_path', 'expires_at'];

    protected $casts = [
        'export_types' => 'array',
        'expires_at' => 'datetime',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
