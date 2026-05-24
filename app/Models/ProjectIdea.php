<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectIdea extends Model
{
    protected $fillable = [
        'department_id', 'proposed_by', 'semester_id',
        'title_ar', 'title_en', 'description_ar', 'description_en',
        'objectives_ar', 'objectives_en',
        'expected_outcomes_ar', 'expected_outcomes_en',
        'project_type', 'category_ar', 'category_en',
        'tags', 'tools', 'technologies',
        'requirements_ar', 'requirements_en',
        'min_students', 'max_students',
        'status', 'rejection_reason', 'approved_by', 'approved_at',
        'priority', 'is_featured',
    ];

    protected $casts = [
        'tags'         => 'array',
        'tools'        => 'array',
        'technologies' => 'array',
        'approved_at'  => 'datetime',
        'is_featured'  => 'boolean',
    ];

    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : ($this->title_en ?? $this->title_ar);
    }

    public function getDescriptionAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : ($this->description_en ?? $this->description_ar);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'  => __('status.pending'),
            'approved' => __('status.approved'),
            'rejected' => __('status.rejected'),
            'taken'    => __('status.taken'),
            'archived' => __('status.archived'),
            default    => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'approved' => 'success',
            'rejected' => 'danger',
            'taken'    => 'info',
            'archived' => 'secondary',
            default    => 'warning',
        };
    }

    public function department(): BelongsTo  { return $this->belongsTo(Department::class); }
    public function proposedBy(): BelongsTo  { return $this->belongsTo(User::class, 'proposed_by'); }
    public function approvedBy(): BelongsTo  { return $this->belongsTo(User::class, 'approved_by'); }
    public function semester(): BelongsTo    { return $this->belongsTo(Semester::class); }
    public function projects(): HasMany      { return $this->hasMany(Project::class); }

    public function scopeApproved($q) { return $q->where('status', 'approved'); }
    public function scopePending($q)  { return $q->where('status', 'pending'); }
    public function scopeFeatured($q) { return $q->where('is_featured', true); }
}