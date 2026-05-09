<?php

use App\Models\Coloc;
use App\Models\Roommate;

beforeEach(function () {
    $this->coloc = Coloc::factory()->create();
    Roommate::factory()->count(3)->create(['coloc_id' => $this->coloc->id]);
    $this->coloc->createDefaultTasks();
    $this->coloc->tasks()->update(['enabled' => true]);
});

it('loads the stats page', function () {
    $this->get(route('coloc.stats', $this->coloc))
        ->assertOk()
        ->assertSee('Statistiques')
        ->assertSee('Classement des colocs');
});

it('shows task distribution section', function () {
    $this->get(route('coloc.stats', $this->coloc))
        ->assertOk()
        ->assertSee('partition par t', false);
});
