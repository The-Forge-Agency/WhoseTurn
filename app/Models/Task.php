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

    protected $fillable = ['coloc_id', 'name', 'icon_slug', 'icon_url', 'enabled', 'frequency', 'order'];

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

    public function getIconSrcAttribute(): string
    {
        if ($this->icon_url) {
            return asset('storage/'.$this->icon_url);
        }

        if ($this->icon_slug) {
            return asset('images/tasks/'.$this->icon_slug.'.svg');
        }

        return asset('images/icon.svg');
    }

    public function isDueOnWeek(int $week): bool
    {
        return match ($this->frequency) {
            'biweekly' => ($week + $this->order) % 2 === 0,
            'monthly' => ($week + $this->order) % 4 === 0,
            default => true,
        };
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
