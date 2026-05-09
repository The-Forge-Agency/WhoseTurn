<?php

use App\Models\Coloc;
use App\Models\Roommate;

beforeEach(function () {
    $this->coloc = Coloc::factory()->create();
    Roommate::factory()->count(2)->create(['coloc_id' => $this->coloc->id]);
    $this->coloc->createDefaultTasks();
    $this->coloc->tasks()->update(['enabled' => true]);
});

it('loads the history page', function () {
    $this->get(route('coloc.history', $this->coloc))
        ->assertOk()
        ->assertSee('Historique');
});

it('shows 4 weeks of history', function () {
    $this->get(route('coloc.history', $this->coloc))
        ->assertOk()
        ->assertSee('en cours');
});
