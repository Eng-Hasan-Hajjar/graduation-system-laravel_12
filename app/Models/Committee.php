<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Committee extends Model
{
    protected $fillable = [
        'project_id', 'name_ar', 'name_en',
        'scheduled_at', 'actual_start_at', 'actual_end_at',
        'location', 'room', 'is_completed', 'completed_at',
        'notes_ar', 'notes_en',
    ];

    protected $casts = [
        'scheduled_at'    => 'datetime',
        'actual_start_at' => 'datetime',
        'actual_end_at'   => 'datetime',
        'completed_at'    => 'datetime',
        'is_completed'    => 'boolean',
    ];

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'committee_members', 'committee_id', 'user_id')
                    ->withPivot(['role', 'attended', 'grade_given', 'feedback'])
                    ->withTimestamps();
    }

    public function defenseSchedule(): HasMany { return $this->hasMany(DefenseSchedule::class); }
}