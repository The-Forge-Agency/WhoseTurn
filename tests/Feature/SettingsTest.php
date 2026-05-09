<?php

use App\Models\Coloc;
use App\Models\Roommate;

beforeEach(function () {
    $this->coloc = Coloc::factory()->create();
    $this->coloc->createDefaultTasks();
    $this->roommates = collect([
        Roommate::factory()->create(['coloc_id' => $this->coloc->id, 'avatar_slug' => 'personnage-01']),
        Roommate::factory()->create(['coloc_id' => $this->coloc->id, 'avatar_slug' => 'personnage-02']),
    ]);
});

test('settings page loads', function () {
    $this->get(route('coloc.settings', $this->coloc))
        ->assertOk()
        ->assertSee('Réglages');
});

test('can add roommate from settings', function () {
    $this->post(route('coloc.settings.roommate.store', $this->coloc), [
        'first_name' => 'Lucas',
        'avatar_slug' => 'personnage-16',
    ])->assertRedirect();

    expect($this->coloc->fresh()->roommates)->toHaveCount(3);
});

test('can remove roommate from settings', function () {
    $roommate = $this->roommates->first();

    $this->delete(route('coloc.settings.roommate.destroy', [$this->coloc, $roommate]))
        ->assertRedirect();

    expect(Roommate::find($roommate->id))->toBeNull();
});

test('can toggle tasks from settings', function () {
    $taskIds = $this->coloc->tasks->take(3)->pluck('id')->toArray();

    $this->post(route('coloc.settings.tasks.store', $this->coloc), [
        'tasks' => $taskIds,
    ])->assertRedirect();

    expect($this->coloc->tasks()->where('enabled', true)->count())->toBe(3);
});
