<?php

use App\Models\Coloc;
use App\Models\Roommate;
use App\Models\TaskCompletion;

beforeEach(function () {
    $this->coloc = Coloc::factory()->create();
    $this->coloc->createDefaultTasks();
    $this->roommates = Roommate::factory()->count(3)->create(['coloc_id' => $this->coloc->id]);
});

test('can complete a task as done', function () {
    $task = $this->coloc->tasks->first();
    $roommate = $this->roommates->first();

    $this->postJson(route('coloc.complete', $this->coloc), [
        'task_id' => $task->id,
        'assigned_roommate_id' => $roommate->id,
        'status' => 'done',
    ])->assertOk()->assertJson(['success' => true]);

    expect(TaskCompletion::where('task_id', $task->id)->first()->status)->toBe('done');
});

test('can complete as not done', function () {
    $task = $this->coloc->tasks->first();
    $roommate = $this->roommates->first();

    $this->postJson(route('coloc.complete', $this->coloc), [
        'task_id' => $task->id,
        'assigned_roommate_id' => $roommate->id,
        'status' => 'not_done',
    ])->assertOk();

    expect(TaskCompletion::where('task_id', $task->id)->first()->status)->toBe('not_done');
});

test('can complete as done by other', function () {
    $task = $this->coloc->tasks->first();
    $assigned = $this->roommates->first();
    $helper = $this->roommates->last();

    $this->postJson(route('coloc.complete', $this->coloc), [
        'task_id' => $task->id,
        'assigned_roommate_id' => $assigned->id,
        'status' => 'done_by_other',
        'actual_roommate_id' => $helper->id,
    ])->assertOk();

    $completion = TaskCompletion::where('task_id', $task->id)->first();
    expect($completion->status)->toBe('done_by_other');
    expect($completion->actual_roommate_id)->toBe($helper->id);
});

test('done by other requires actual roommate id', function () {
    $task = $this->coloc->tasks->first();
    $roommate = $this->roommates->first();

    $this->postJson(route('coloc.complete', $this->coloc), [
        'task_id' => $task->id,
        'assigned_roommate_id' => $roommate->id,
        'status' => 'done_by_other',
    ])->assertUnprocessable();
});
