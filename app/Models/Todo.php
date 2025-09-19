<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'assignee',
        'due_date',
        'time_tracked',
        'status',
        'priority',
    ];

    protected $casts = [
        'due_date' => 'date',
        'time_tracked' => 'integer',
    ];

    public const STATUS_OPTIONS = ['pending', 'open', 'in_progress', 'completed'];
    public const PRIORITY_OPTIONS = ['low', 'medium', 'high'];
}
