<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectFile extends Model
{
    protected $fillable = [
        'project_id', 'uploaded_by', 'report_id',
        'file_name', 'original_name', 'file_path',
        'file_type', 'file_size', 'category',
        'description', 'is_final', 'version',
    ];

    protected $casts = ['is_final' => 'boolean'];

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)       return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    public function project(): BelongsTo    { return $this->belongsTo(Project::class); }
    public function uploadedBy(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function report(): BelongsTo     { return $this->belongsTo(ProjectReport::class); }
}