<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class University extends Model
{
    protected $fillable = [
        'name_ar', 'name_en', 'logo', 'website',
        'email', 'phone', 'address_ar', 'address_en', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : ($this->name_en ?? $this->name_ar);
    }

    public function colleges(): HasMany { return $this->hasMany(College::class); }
    public function users(): HasMany    { return $this->hasMany(User::class); }
    public function academicYears(): HasMany { return $this->hasMany(AcademicYear::class); }
}