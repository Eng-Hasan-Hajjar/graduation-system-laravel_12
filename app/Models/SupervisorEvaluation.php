<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupervisorEvaluation extends Model
{
    protected $fillable = [
        'project_id', 'student_id', 'supervisor_id',
        'commitment_grade', 'technical_grade', 'presentation_grade',
        'report_grade', 'total_grade', 'notes', 'evaluation_date',
    ];

    protected $casts = ['evaluation_date' => 'date'];

    public function project(): BelongsTo    { return $this->belongsTo(Project::class); }
    public function student(): BelongsTo    { return $this->belongsTo(User::class, 'student_id'); }
    public function supervisor(): BelongsTo { return $this->belongsTo(User::class, 'supervisor_id'); }
}