<?php

use App\Models\Coloc;
use App\Models\Roommate;
use App\Models\TaskCompletion;
use App\Services\RotationService;

beforeEach(function () {
    $this->service = new RotationService;
    $this->coloc = Coloc::factory()->create();
    $this->coloc->createDefaultTasks();
    $this->coloc->tasks()->update(['enabled' => true]);
});

test('simple rotation distributes tasks', function () {
    Roommate::factory()->count(3)->create(['coloc_id' => $this->coloc->id]);

    $assignments = $this->service->getAssignments($this->coloc, 1, 2026);

    expect($assignments)->toHaveCount(10);

    $assignedIds = $assignments->pluck('roommate.id')->unique();
    expect($assignedIds)->toHaveCount(3);
});

test('rotation is deterministic', function () {
    Roommate::factory()->count(3)->create(['coloc_id' => $this->coloc->id]);

    $first = $this->service->getAssignments($this->coloc, 5, 2026);
    $second = $this->service->getAssignments($this->coloc, 5, 2026);

    expect($first->pluck('roommate.id')->toArray())
        ->toBe($second->pluck('roommate.id')->toArray());
});

test('different weeks produce different assignments', function () {
    Roommate::factory()->count(3)->create(['coloc_id' => $this->coloc->id]);

    $week1 = $this->service->getAssignments($this->coloc, 1, 2026);
    $week2 = $this->service->getAssignments($this->coloc, 2, 2026);

    expect($week1->pluck('roommate.id')->toArray())
        ->not->toBe($week2->pluck('roommate.id')->toArray());
});

test('score computation: not done gives -1', function () {
    $roommate = Roommate::factory()->create(['coloc_id' => $this->coloc->id]);

    TaskCompletion::factory()->create([
        'coloc_id' => $this->coloc->id,
        'task_id' => $this->coloc->tasks->first()->id,
        'assigned_roommate_id' => $roommate->id,
        'status' => 'not_done',
        'week' => 5,
        'year' => 2026,
    ]);

    $scores = $this->service->computeScores($this->coloc, 5, 2026);

    expect($scores[$roommate->id])->toBe(-1);
});

test('score computation: done by other gives -1 and +1', function () {
    $assigned = Roommate::factory()->create(['coloc_id' => $this->coloc->id]);
    $helper = Roommate::factory()->create(['coloc_id' => $this->coloc->id]);

    TaskCompletion::factory()->create([
        'coloc_id' => $this->coloc->id,
        'task_id' => $this->coloc->tasks->first()->id,
        'assigned_roommate_id' => $assigned->id,
        'actual_roommate_id' => $helper->id,
        'status' => 'done_by_other',
        'week' => 5,
        'year' => 2026,
    ]);

    $scores = $this->service->computeScores($this->coloc, 5, 2026);

    expect($scores[$assigned->id])->toBe(-1);
    expect($scores[$helper->id])->toBe(1);
});

test('week info returns valid data', function () {
    $info = $this->service->getActiveWeekAndYear();

    expect($info)->toHaveKeys(['week', 'year', 'is_weekend']);
    expect($info['week'])->toBeGreaterThan(0)->toBeLessThanOrEqual(53);
    expect($info['year'])->toBeGreaterThanOrEqual(2026);
});
