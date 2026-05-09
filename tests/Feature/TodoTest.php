<?php

use App\Models\Coloc;
use App\Models\Roommate;
use App\Models\UrgentTodo;

beforeEach(function () {
    $this->coloc = Coloc::factory()->create();
    Roommate::factory()->count(2)->create(['coloc_id' => $this->coloc->id]);
    $this->coloc->createDefaultTasks();
    $this->coloc->tasks()->update(['enabled' => true]);
});

it('creates an urgent todo', function () {
    $this->post(route('coloc.todo.store', $this->coloc), ['title' => 'Sortir les poubelles'])
        ->assertRedirect();

    $this->assertDatabaseHas('urgent_todos', [
        'coloc_id' => $this->coloc->id,
        'title' => 'Sortir les poubelles',
        'done' => false,
    ]);
});

it('completes an urgent todo', function () {
    $todo = UrgentTodo::factory()->create(['coloc_id' => $this->coloc->id]);

    $this->post(route('coloc.todo.complete', [$this->coloc, $todo]))
        ->assertRedirect();

    expect($todo->fresh()->done)->toBeTrue();
});

it('deletes an urgent todo', function () {
    $todo = UrgentTodo::factory()->create(['coloc_id' => $this->coloc->id]);

    $this->delete(route('coloc.todo.destroy', [$this->coloc, $todo]))
        ->assertRedirect();

    $this->assertDatabaseMissing('urgent_todos', ['id' => $todo->id]);
});

it('shows urgent todos on dashboard', function () {
    UrgentTodo::factory()->create(['coloc_id' => $this->coloc->id, 'title' => 'Acheter du PQ']);

    $this->get(route('coloc.dashboard', $this->coloc))
        ->assertOk()
        ->assertSee('Acheter du PQ');
});
