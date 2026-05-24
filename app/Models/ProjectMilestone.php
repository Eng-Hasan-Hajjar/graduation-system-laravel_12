<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMilestone extends Model
{
    protected $fillable = [
        'project_id', 'title_ar', 'title_en',
        'description_ar', 'description_en',
        'due_date', 'completed_date', 'status',
        'order', 'weight_percentage',
    ];

    protected $casts = [
        'due_date'       => 'date',
        'completed_date' => 'date',
    ];

    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : ($this->title_en ?? $this->title_ar);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed'   => 'success',
            'in_progress' => 'primary',
            'overdue'     => 'danger',
            default       => 'warning',
        };
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'completed' && $this->due_date->isPast();
    }

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
}