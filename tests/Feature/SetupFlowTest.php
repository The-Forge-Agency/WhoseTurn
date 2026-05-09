<?php

use App\Models\Coloc;
use App\Models\Roommate;

beforeEach(function () {
    $this->coloc = Coloc::factory()->create();
    $this->coloc->createDefaultTasks();
});

test('setup page loads', function () {
    $this->get(route('coloc.setup', $this->coloc))->assertOk();
});

test('can add a roommate', function () {
    $this->post(route('coloc.setup.roommate.store', $this->coloc), [
        'first_name' => 'Lea',
        'avatar_slug' => 'personnage-01',
    ])->assertRedirect();

    expect($this->coloc->roommates)->toHaveCount(1);
    expect($this->coloc->roommates->first()->first_name)->toBe('Lea');
});

test('cannot add roommate with taken avatar', function () {
    Roommate::factory()->create([
        'coloc_id' => $this->coloc->id,
        'avatar_slug' => 'personnage-01',
    ]);

    $this->post(route('coloc.setup.roommate.store', $this->coloc), [
        'first_name' => 'Hugo',
        'avatar_slug' => 'personnage-01',
    ])->assertSessionHasErrors('avatar_slug');
});

test('can delete a roommate', function () {
    $roommate = Roommate::factory()->create(['coloc_id' => $this->coloc->id]);

    $this->delete(route('coloc.setup.roommate.destroy', [$this->coloc, $roommate]))->assertRedirect();

    expect(Roommate::find($roommate->id))->toBeNull();
});

test('can save tasks and redirect to dashboard', function () {
    Roommate::factory()->count(2)->create(['coloc_id' => $this->coloc->id]);

    $taskIds = $this->coloc->tasks->take(5)->pluck('id')->toArray();

    $this->post(route('coloc.setup.tasks.store', $this->coloc), [
        'tasks' => $taskIds,
    ])->assertRedirect(route('coloc.dashboard', $this->coloc));

    expect($this->coloc->tasks()->where('enabled', true)->count())->toBe(5);
});

test('cannot save tasks with fewer than 2 roommates', function () {
    Roommate::factory()->create(['coloc_id' => $this->coloc->id]);

    $this->post(route('coloc.setup.tasks.store', $this->coloc), [
        'tasks' => [$this->coloc->tasks->first()->id],
    ])->assertSessionHasErrors('tasks');
});
