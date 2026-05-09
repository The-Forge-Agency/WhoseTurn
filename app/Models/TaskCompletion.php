<?php

namespace App\Models;

use Database\Factories\TaskCompletionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskCompletion extends Model
{
    /** @use HasFactory<TaskCompletionFactory> */
    use HasFactory;

    protected $fillable = [
        'coloc_id',
        'task_id',
        'assigned_roommate_id',
        'actual_roommate_id',
        'status',
        'week',
        'year',
    ];

    /** @return BelongsTo<Coloc, $this> */
    public function coloc(): BelongsTo
    {
        return $this->belongsTo(Coloc::class);
    }

    /** @return BelongsTo<Task, $this> */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /** @return BelongsTo<Roommate, $this> */
    public function assignedRoommate(): BelongsTo
    {
        return $this->belongsTo(Roommate::class, 'assigned_roommate_id');
    }

    /** @return BelongsTo<Roommate, $this> */
    public function actualRoommate(): BelongsTo
    {
        return $this->belongsTo(Roommate::class, 'actual_roommate_id');
    }
}
