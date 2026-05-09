<?php

namespace App\Services;

use App\Models\Coloc;
use App\Models\Roommate;
use App\Models\Task;
use App\Models\TaskCompletion;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RotationService
{
    /**
     * @return array{week: int, year: int, is_weekend: bool}
     */
    public function getActiveWeekAndYear(): array
    {
        $monday = $this->getActiveMonday();
        $isWeekend = in_array(now()->dayOfWeek, [0, 6], true);

        return [
            'week' => (int) $monday->format('W'),
            'year' => $monday->year,
            'is_weekend' => $isWeekend,
        ];
    }

    /**
     * @return array{start: Carbon, end: Carbon}
     */
    public function getWeekDateRange(): array
    {
        $start = $this->getActiveMonday();
        $end = $start->copy()->addDays(6);

        return ['start' => $start, 'end' => $end];
    }

    public function formatWeekRange(): string
    {
        $isWeekend = in_array(now()->dayOfWeek, [0, 6], true);
        $range = $this->getWeekDateRange();

        $fmt = fn (Carbon $date): string => $date->translatedFormat('j F');

        $prefix = $isWeekend ? 'Semaine prochaine' : 'Cette semaine';

        return "{$prefix} : du {$fmt($range['start'])} au {$fmt($range['end'])}";
    }

    public function formatWeekRangeFor(int $week, int $year): string
    {
        $start = Carbon::now()->setISODate($year, $week, 1)->startOfDay();
        $end = $start->copy()->addDays(6);
        $fmt = fn (Carbon $date): string => $date->translatedFormat('j F');

        return "Semaine {$week} : du {$fmt($start)} au {$fmt($end)}";
    }

    /**
     * @return Collection<int, array{task: Task, roommate: Roommate}>
     */
    public function getAssignments(Coloc $coloc, int $week, int $year): Collection
    {
        $roommates = $coloc->roommates()->orderBy('order')->get();
        $tasks = $coloc->tasks()->enabled()->orderBy('order')->get()
            ->filter(fn (Task $task): bool => $task->isDueOnWeek($week));

        if ($roommates->isEmpty() || $tasks->isEmpty()) {
            return collect();
        }

        $previousWeek = $week - 1;
        $previousYear = $year;
        if ($previousWeek < 1) {
            $previousWeek = 52;
            $previousYear--;
        }

        $scores = $this->computeScores($coloc, $previousWeek, $previousYear);
        $hasScores = collect($scores)->some(fn (int $v): bool => $v !== 0);

        if ($hasScores) {
            return $this->assignWithScores($tasks, $roommates, $week, $scores);
        }

        return $this->assignSimple($tasks, $roommates, $week);
    }

    /**
     * @param  Collection<int, Task>  $tasks
     * @param  Collection<int, Roommate>  $roommates
     * @return Collection<int, array{task: Task, roommate: Roommate}>
     */
    private function assignSimple(Collection $tasks, Collection $roommates, int $week): Collection
    {
        $n = $roommates->count();

        return $tasks->values()->map(fn (Task $task, int $index): array => [
            'task' => $task,
            'roommate' => $roommates->values()[($index + $week) % $n],
        ]);
    }

    /**
     * @param  Collection<int, Task>  $tasks
     * @param  Collection<int, Roommate>  $roommates
     * @param  array<int, int>  $scores
     * @return Collection<int, array{task: Task, roommate: Roommate}>
     */
    private function assignWithScores(Collection $tasks, Collection $roommates, int $week, array $scores): Collection
    {
        $n = $roommates->count();
        $taskCount = [];
        foreach ($roommates as $r) {
            $taskCount[$r->id] = 0;
        }

        $rotationOffset = $week % $n;
        $assignments = collect();

        foreach ($tasks as $task) {
            $best = null;
            $bestLoad = PHP_INT_MAX;
            $bestTiebreak = PHP_INT_MAX;

            foreach ($roommates->values() as $i => $r) {
                $currentCount = $taskCount[$r->id] ?? 0;
                $score = $scores[$r->id] ?? 0;
                $effectiveLoad = $currentCount + $score;
                $tiebreak = ($i - $rotationOffset + $n) % $n;

                if ($effectiveLoad < $bestLoad || ($effectiveLoad === $bestLoad && $tiebreak < $bestTiebreak)) {
                    $best = $r;
                    $bestLoad = $effectiveLoad;
                    $bestTiebreak = $tiebreak;
                }
            }

            if ($best) {
                $assignments->push(['task' => $task, 'roommate' => $best]);
                $taskCount[$best->id]++;
            }
        }

        return $assignments;
    }

    /**
     * @return array<int, int>
     */
    public function computeScores(Coloc $coloc, int $week, int $year): array
    {
        $completions = TaskCompletion::where('coloc_id', $coloc->id)
            ->where('week', $week)
            ->where('year', $year)
            ->get();

        $scores = [];

        foreach ($completions as $c) {
            if ($c->status === 'not_done') {
                $scores[$c->assigned_roommate_id] = ($scores[$c->assigned_roommate_id] ?? 0) - 1;
            } elseif ($c->status === 'done_by_other' && $c->actual_roommate_id) {
                $scores[$c->assigned_roommate_id] = ($scores[$c->assigned_roommate_id] ?? 0) - 1;
                $scores[$c->actual_roommate_id] = ($scores[$c->actual_roommate_id] ?? 0) + 1;
            }
        }

        return $scores;
    }

    private function getActiveMonday(): Carbon
    {
        $now = now();
        $day = $now->dayOfWeek;

        if ($day === Carbon::SUNDAY) {
            return $now->copy()->addDay()->startOfDay();
        }

        if ($day === Carbon::SATURDAY) {
            return $now->copy()->addDays(2)->startOfDay();
        }

        return $now->copy()->startOfWeek(Carbon::MONDAY);
    }
}
