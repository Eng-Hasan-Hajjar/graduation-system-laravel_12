<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    protected $fillable = [
        'university_id', 'key', 'value', 'type', 'group', 'label_ar', 'label_en',
    ];

    public function university(): BelongsTo { return $this->belongsTo(University::class); }

    public function getValueAttribute($val)
    {
        return match($this->type) {
            'boolean' => (bool) $val,
            'integer' => (int) $val,
            'json'    => json_decode($val, true),
            default   => $val,
        };
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
}