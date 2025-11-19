<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    protected $fillable = [
        'title',
        'description',
        'level',
        'duration_minutes',
        'topics',
        'materials',
        'created_by',
        'is_active',
        'order',
    ];

    protected $casts = [
        'topics' => 'array',
        'materials' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
        'duration_minutes' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
