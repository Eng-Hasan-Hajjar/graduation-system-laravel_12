<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = ['college_id', 'name_ar', 'name_en', 'code', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : ($this->name_en ?? $this->name_ar);
    }

    public function college(): BelongsTo    { return $this->belongsTo(College::class); }
    public function users(): HasMany        { return $this->hasMany(User::class); }
    public function projectIdeas(): HasMany { return $this->hasMany(ProjectIdea::class); }
    public function projects(): HasMany     { return $this->hasMany(Project::class); }
}