<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefenseSchedule extends Model
{
    protected $fillable = [
        'project_id', 'committee_id', 'scheduled_date', 'scheduled_time',
        'location', 'room', 'duration_minutes', 'status',
        'postpone_reason', 'new_scheduled_date', 'new_scheduled_time',
        'notified_students', 'notified_supervisors', 'notification_sent_at',
        'notes', 'created_by',
    ];

    protected $casts = [
        'scheduled_date'       => 'date',
        'new_scheduled_date'   => 'date',
        'notification_sent_at' => 'datetime',
        'notified_students'    => 'boolean',
        'notified_supervisors' => 'boolean',
    ];

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'scheduled'  => __('defense.scheduled'),
            'confirmed'  => __('defense.confirmed'),
            'postponed'  => __('defense.postponed'),
            'cancelled'  => __('defense.cancelled'),
            'completed'  => __('defense.completed'),
            default      => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'confirmed'  => 'success',
            'postponed'  => 'warning',
            'cancelled'  => 'danger',
            'completed'  => 'info',
            default      => 'secondary',
        };
    }

    public function project(): BelongsTo   { return $this->belongsTo(Project::class); }
    public function committee(): BelongsTo { return $this->belongsTo(Committee::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function scopeUpcoming($q)
    {
        return $q->where('scheduled_date', '>=', now()->toDateString())
                 ->whereIn('status', ['scheduled', 'confirmed'])
                 ->orderBy('scheduled_date')
                 ->orderBy('scheduled_time');
    }

    public function scopeToday($q)
    {
        return $q->where('scheduled_date', now()->toDateString())
                 ->whereIn('status', ['scheduled', 'confirmed']);
    }
}