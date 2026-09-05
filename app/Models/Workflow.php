<?php

namespace App\Models;

use App\Enums\WorkflowStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workflow extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agency_id',
        'name',
        'slug',
        'status',
        'trigger_type',
        'trigger_config',
        'actions',
        'conditions',
        'execution_count',
        'last_executed_at',
        'error_message',
        'is_system',
    ];

    protected $casts = [
        'trigger_config' => 'array',
        'actions' => 'array',
        'conditions' => 'array',
        'execution_count' => 'integer',
        'last_executed_at' => 'datetime',
        'is_system' => 'boolean',
    ];

    public function getStatusEnum()
    {
        return new WorkflowStatus($this->status);
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function executions(): HasMany
    {
        return $this->hasMany(WorkflowExecution::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', WorkflowStatus::ACTIVE->value);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', WorkflowStatus::DRAFT->value);
    }

    public function scopeByType($query, string $triggerType)
    {
        return $query->where('trigger_type', $triggerType);
    }

    public const TRIGGER_TYPES = [
        'new_post' => 'New Post Created',
        'post_published' => 'Post Published',
        'post_failed' => 'Post Failed',
        'comment_received' => 'Comment Received',
        'mention_received' => 'Mention Received',
        'message_received' => 'Direct Message Received',
        'schedule' => 'Scheduled Time',
        'cron' => 'Cron Schedule',
    ];

    public const ACTION_TYPES = [
        'send_notification' => 'Send Notification',
        'auto_reply' => 'Auto Reply',
        'create_post' => 'Create Post',
        'schedule_post' => 'Schedule Post',
        'ai_generate' => 'AI Generate Content',
        'ai_reply' => 'AI Reply',
        'tag_client' => 'Tag Client',
        'update_campaign' => 'Update Campaign',
        'send_email' => 'Send Email',
        'webhook' => 'Webhook Call',
        'sleep' => 'Wait / Delay',
    ];
}
