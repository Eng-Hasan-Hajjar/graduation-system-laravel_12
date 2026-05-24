<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    protected $fillable = [
        'academic_year_id', 'name_ar', 'name_en', 'type',
        'start_date', 'end_date',
        'project_registration_start', 'project_registration_end',
        'project_submission_start', 'project_submission_end',
        'is_current', 'is_active',
    ];

    protected $casts = [
        'start_date'                 => 'date',
        'end_date'                   => 'date',
        'project_registration_start' => 'date',
        'project_registration_end'   => 'date',
        'project_submission_start'   => 'date',
        'project_submission_end'     => 'date',
        'is_current'                 => 'boolean',
        'is_active'                  => 'boolean',
    ];

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : ($this->name_en ?? $this->name_ar);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'first'  => __('semester.first'),
            'second' => __('semester.second'),
            'summer' => __('semester.summer'),
            default  => $this->type,
        };
    }

    public function isRegistrationOpen(): bool
    {
        $now = now()->toDateString();
        return $this->project_registration_start <= $now
            && $this->project_registration_end >= $now;
    }

    public function isSubmissionOpen(): bool
    {
        $now = now()->toDateString();
        return $this->project_submission_start <= $now
            && $this->project_submission_end >= $now;
    }

    public function academicYear(): BelongsTo { return $this->belongsTo(AcademicYear::class); }
    public function projects(): HasMany        { return $this->hasMany(Project::class); }
    public function projectIdeas(): HasMany    { return $this->hasMany(ProjectIdea::class); }

    public static function current(): ?self
    {
        return static::where('is_current', true)->first();
    }
}