<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_idea_id', 'department_id', 'semester_id',
        'supervisor_id', 'co_supervisor_id', 'created_by',
        'project_number', 'title_ar', 'title_en',
        'description_ar', 'description_en',
        'objectives_ar', 'objectives_en',
        'expected_outcomes_ar', 'expected_outcomes_en',
        'methodology_ar', 'methodology_en',
        'project_type', 'academic_year_level',
        'tools', 'technologies',
        'registration_date', 'start_date', 'expected_end_date',
        'submission_date', 'defense_date', 'actual_defense_date',
        'defense_time', 'defense_location', 'defense_room',
        'status', 'rejection_reason', 'approved_by', 'approved_at',
        'is_discussed', 'discussed_at',
        'final_grade', 'grade_letter',
        'committee_notes_ar', 'committee_notes_en',
        'is_archived', 'archived_at', 'archive_notes',
        'supervisor_notes', 'progress_percentage',
    ];

    protected $casts = [
        'tools'              => 'array',
        'technologies'       => 'array',
        'registration_date'  => 'date',
        'start_date'         => 'date',
        'expected_end_date'  => 'date',
        'submission_date'    => 'date',
        'defense_date'       => 'date',
        'actual_defense_date'=> 'date',
        'approved_at'        => 'datetime',
        'discussed_at'       => 'datetime',
        'archived_at'        => 'datetime',
        'is_discussed'       => 'boolean',
        'is_archived'        => 'boolean',
    ];

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : ($this->title_en ?? $this->title_ar);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'      => __('status.pending'),
            'approved'     => __('status.approved'),
            'rejected'     => __('status.rejected'),
            'in_progress'  => __('status.in_progress'),
            'submitted'    => __('status.submitted'),
            'under_review' => __('status.under_review'),
            'defended'     => __('status.defended'),
            'archived'     => __('status.archived'),
            'cancelled'    => __('status.cancelled'),
            default        => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'approved'     => 'success',
            'rejected'     => 'danger',
            'cancelled'    => 'danger',
            'in_progress'  => 'primary',
            'submitted'    => 'info',
            'under_review' => 'warning',
            'defended'     => 'success',
            'archived'     => 'secondary',
            default        => 'warning',
        };
    }

    public function getProjectTypeLabelAttribute(): string
    {
        return match($this->project_type) {
            'graduation' => __('project_type.graduation'),
            'semester'   => __('project_type.semester'),
            'year4'      => __('project_type.year4'),
            'research'   => __('project_type.research'),
            default      => $this->project_type,
        };
    }

    public function isDefensePending(): bool
    {
        return $this->defense_date !== null && !$this->is_discussed;
    }

    public function isOverdue(): bool
    {
        return $this->expected_end_date && $this->expected_end_date->isPast()
            && !in_array($this->status, ['defended', 'archived', 'cancelled']);
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function idea(): BelongsTo       { return $this->belongsTo(ProjectIdea::class, 'project_idea_id'); }
    public function department(): BelongsTo { return $this->belongsTo(Department::class); }
    public function semester(): BelongsTo   { return $this->belongsTo(Semester::class); }
    public function supervisor(): BelongsTo { return $this->belongsTo(User::class, 'supervisor_id'); }
    public function coSupervisor(): BelongsTo { return $this->belongsTo(User::class, 'co_supervisor_id'); }
    public function creator(): BelongsTo    { return $this->belongsTo(User::class, 'created_by'); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_students', 'project_id', 'student_id')
                    ->withPivot(['role', 'joined_at', 'status', 'individual_grade'])
                    ->withTimestamps();
    }

    public function committees(): HasMany     { return $this->hasMany(Committee::class); }
    public function milestones(): HasMany     { return $this->hasMany(ProjectMilestone::class)->orderBy('order'); }
    public function reports(): HasMany        { return $this->hasMany(ProjectReport::class); }
    public function files(): HasMany          { return $this->hasMany(ProjectFile::class); }
    public function defenseSchedules(): HasMany { return $this->hasMany(DefenseSchedule::class); }
    public function evaluations(): HasMany    { return $this->hasMany(SupervisorEvaluation::class); }

    public function latestSchedule()
    {
        return $this->hasOne(DefenseSchedule::class)->latest();
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeApproved($q)    { return $q->where('status', 'approved'); }
    public function scopeDiscussed($q)   { return $q->where('is_discussed', true); }
    public function scopeArchived($q)    { return $q->where('is_archived', true); }
    public function scopeNotDiscussed($q){ return $q->where('is_discussed', false); }
    public function scopeByType($q, $type){ return $q->where('project_type', $type); }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public static function generateProjectNumber(string $type = 'graduation'): string
    {
        $prefix = match($type) {
            'graduation' => 'GRAD',
            'semester'   => 'SEM',
            'year4'      => 'Y4',
            'research'   => 'RES',
            default      => 'PROJ',
        };
        $year  = date('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;
        return sprintf('%s-%s-%04d', $prefix, $year, $count);
    }
}