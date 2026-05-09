<?php

namespace App\Models;

use Database\Factories\ColocFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Coloc extends Model
{
    /** @use HasFactory<ColocFactory> */
    use HasFactory;

    protected $fillable = ['name', 'share_code'];

    /** @var array<string, array{name: string, icon_slug: string}> */
    public const DEFAULT_TASKS = [
        ['name' => 'Vaisselle', 'icon_slug' => 'vaisselle'],
        ['name' => 'Poubelle', 'icon_slug' => 'poubelle'],
        ['name' => 'Aspirateur', 'icon_slug' => 'aspirateur'],
        ['name' => 'Courses', 'icon_slug' => 'courses'],
        ['name' => 'Cuisine', 'icon_slug' => 'cuisine'],
        ['name' => 'Lessive', 'icon_slug' => 'lessive'],
        ['name' => 'Salle de bain', 'icon_slug' => 'salle-de-bain'],
        ['name' => 'Toilettes', 'icon_slug' => 'toilettes'],
        ['name' => 'Salon', 'icon_slug' => 'salon'],
        ['name' => 'Serpilliere', 'icon_slug' => 'serpilliere'],
    ];

    protected static function booted(): void
    {
        static::creating(function (Coloc $coloc): void {
            if (empty($coloc->share_code)) {
                $coloc->share_code = static::generateUniqueShareCode();
            }
        });
    }

    public static function generateUniqueShareCode(): string
    {
        do {
            $code = Str::lower(Str::random(8));
        } while (static::where('share_code', $code)->exists());

        return $code;
    }

    public function getRouteKeyName(): string
    {
        return 'share_code';
    }

    /** @return HasMany<Task, $this> */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)->orderBy('order');
    }

    /** @return HasMany<Roommate, $this> */
    public function roommates(): HasMany
    {
        return $this->hasMany(Roommate::class)->orderBy('order');
    }

    /** @return HasMany<TaskCompletion, $this> */
    public function taskCompletions(): HasMany
    {
        return $this->hasMany(TaskCompletion::class);
    }

    public function createDefaultTasks(): void
    {
        foreach (self::DEFAULT_TASKS as $index => $task) {
            $this->tasks()->create([
                'name' => $task['name'],
                'icon_slug' => $task['icon_slug'],
                'enabled' => true,
                'order' => $index,
            ]);
        }
    }
}
