<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = [
        'teacher_id',
        'start_date',
        'end_date',
        'base_salary',
        'bonus',
        'deduction',
        'total_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'base_salary' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deduction' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
