<?php

namespace App\Models;

use Database\Factories\UrgentTodoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UrgentTodo extends Model
{
    /** @use HasFactory<UrgentTodoFactory> */
    use HasFactory;

    protected $fillable = ['coloc_id', 'created_by_roommate_id', 'done_by_roommate_id', 'title', 'done'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'done' => 'boolean',
        ];
    }

    /** @return BelongsTo<Coloc, $this> */
    public function coloc(): BelongsTo
    {
        return $this->belongsTo(Coloc::class);
    }

    /** @return BelongsTo<Roommate, $this> */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Roommate::class, 'created_by_roommate_id');
    }

    /** @return BelongsTo<Roommate, $this> */
    public function doneBy(): BelongsTo
    {
        return $this->belongsTo(Roommate::class, 'done_by_roommate_id');
    }
}
