<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// ─── Project Report ──────────────────────────────────────────────────────────
class ProjectReport extends Model
{
    protected $fillable = [
        'project_id', 'submitted_by', 'title_ar', 'title_en',
        'content_ar', 'content_en', 'report_type', 'week_number',
        'report_date', 'status', 'supervisor_feedback',
        'reviewed_by', 'reviewed_at', 'grade',
    ];

    protected $casts = [
        'report_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : ($this->title_en ?? $this->title_ar);
    }

    public function project(): BelongsTo    { return $this->belongsTo(Project::class); }
    public function submittedBy(): BelongsTo{ return $this->belongsTo(User::class, 'submitted_by'); }
    public function reviewedBy(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function files(): HasMany        { return $this->hasMany(ProjectFile::class, 'report_id'); }

    public function scopePending($q)   { return $q->where('status', 'submitted'); }
    public function scopeApproved($q)  { return $q->where('status', 'approved'); }
}

// ─── Project File ─────────────────────────────────────────────────────────────
class ProjectFile extends Model
{
    protected $fillable = [
        'project_id', 'uploaded_by', 'report_id',
        'file_name', 'original_name', 'file_path',
        'file_type', 'file_size', 'category',
        'description', 'is_final', 'version',
    ];

    protected $casts = ['is_final' => 'boolean'];

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)       return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    public function project(): BelongsTo    { return $this->belongsTo(Project::class); }
    public function uploadedBy(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function report(): BelongsTo     { return $this->belongsTo(ProjectReport::class); }
}

// ─── Defense Schedule ─────────────────────────────────────────────────────────
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
        'scheduled_date'      => 'date',
        'new_scheduled_date'  => 'date',
        'notification_sent_at'=> 'datetime',
        'notified_students'   => 'boolean',
        'notified_supervisors'=> 'boolean',
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

// ─── Supervisor Evaluation ────────────────────────────────────────────────────
class SupervisorEvaluation extends Model
{
    protected $fillable = [
        'project_id', 'student_id', 'supervisor_id',
        'commitment_grade', 'technical_grade', 'presentation_grade',
        'report_grade', 'total_grade', 'notes', 'evaluation_date',
    ];

    protected $casts = ['evaluation_date' => 'date'];

    public function project(): BelongsTo    { return $this->belongsTo(Project::class); }
    public function student(): BelongsTo    { return $this->belongsTo(User::class, 'student_id'); }
    public function supervisor(): BelongsTo { return $this->belongsTo(User::class, 'supervisor_id'); }
}