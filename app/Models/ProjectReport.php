<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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