<?php

use App\Models\Coloc;
use App\Models\Roommate;

test('dashboard loads with assignments', function () {
    $coloc = Coloc::factory()->create();
    $coloc->createDefaultTasks();
    Roommate::factory()->count(3)->create(['coloc_id' => $coloc->id]);

    $this->get(route('coloc.dashboard', $coloc))
        ->assertOk()
        ->assertSee('Qui fait quoi ?');
});

test('dashboard redirects to setup when no roommates', function () {
    $coloc = Coloc::factory()->create();
    $coloc->createDefaultTasks();

    $this->get(route('coloc.dashboard', $coloc))
        ->assertRedirect(route('coloc.setup', $coloc));
});

test('invalid share code returns 404', function () {
    $this->get('/zzzzzzzz')->assertNotFound();
});
