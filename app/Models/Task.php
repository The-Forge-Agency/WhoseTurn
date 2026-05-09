<?php

namespace App\Models;

use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    protected $fillable = ['coloc_id', 'name', 'icon_slug', 'enabled', 'order'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }

    /** @param Builder<Task> $query */
    public function scopeEnabled(Builder $query): void
    {
        $query->where('enabled', true);
    }

    /** @return BelongsTo<Coloc, $this> */
    public function coloc(): BelongsTo
    {
        return $this->belongsTo(Coloc::class);
    }

    /** @return HasMany<TaskCompletion, $this> */
    public function completions(): HasMany
    {
        return $this->hasMany(TaskCompletion::class);
    }
}
