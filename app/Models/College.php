<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class College extends Model
{
    protected $fillable = ['university_id', 'name_ar', 'name_en', 'code', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : ($this->name_en ?? $this->name_ar);
    }

    public function university(): BelongsTo { return $this->belongsTo(University::class); }
    public function departments(): HasMany  { return $this->hasMany(Department::class); }
    public function users(): HasMany        { return $this->hasMany(User::class); }
}