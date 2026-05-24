<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'university_id', 'college_id', 'department_id',
        'name_ar', 'name_en', 'email', 'phone',
        'national_id', 'student_id', 'employee_id',
        'role', 'academic_rank', 'specialization_ar', 'specialization_en',
        'academic_year', 'avatar', 'password', 'status',
        'lang_preference', 'theme_preference', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'password'          => 'hashed',
    ];

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : ($this->name_en ?? $this->name_ar);
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name_ar) . '&background=4f46e5&color=fff';
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin'            => __('roles.admin'),
            'supervisor'       => __('roles.supervisor'),
            'coordinator'      => __('roles.coordinator'),
            'committee_member' => __('roles.committee_member'),
            'student'          => __('roles.student'),
            default            => $this->role,
        };
    }

    // ─── Role Helpers ─────────────────────────────────────────────────────────

    public function isAdmin(): bool           { return $this->role === 'admin'; }
    public function isSupervisor(): bool      { return $this->role === 'supervisor'; }
    public function isCoordinator(): bool     { return $this->role === 'coordinator'; }
    public function isCommitteeMember(): bool { return $this->role === 'committee_member'; }
    public function isStudent(): bool         { return $this->role === 'student'; }
    public function isStaff(): bool           { return in_array($this->role, ['admin','supervisor','coordinator','committee_member']); }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function university(): BelongsTo { return $this->belongsTo(University::class); }
    public function college(): BelongsTo    { return $this->belongsTo(College::class); }
    public function department(): BelongsTo { return $this->belongsTo(Department::class); }

    // المشاريع التي يشرف عليها
    public function supervisedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'supervisor_id');
    }

    // المشاريع التي هو مشرف مشارك فيها
    public function coSupervisedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'co_supervisor_id');
    }

    // مشاريع الطالب
    public function studentProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_students', 'student_id', 'project_id')
                    ->withPivot(['role', 'joined_at', 'status', 'individual_grade'])
                    ->withTimestamps();
    }

    // أفكار المشاريع التي اقترحها
    public function proposedIdeas(): HasMany
    {
        return $this->hasMany(ProjectIdea::class, 'proposed_by');
    }

    // اللجان التي هو عضو فيها
    public function committees(): BelongsToMany
    {
        return $this->belongsToMany(Committee::class, 'committee_members', 'user_id', 'committee_id')
                    ->withPivot(['role', 'attended', 'grade_given', 'feedback'])
                    ->withTimestamps();
    }

    // التقارير
    public function submittedReports(): HasMany
    {
        return $this->hasMany(ProjectReport::class, 'submitted_by');
    }
}