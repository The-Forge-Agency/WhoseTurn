<?php

use App\Models\Coloc;
use App\Models\Roommate;
use App\Models\Task;

beforeEach(function () {
    $this->coloc = Coloc::factory()->create();
    Roommate::factory()->create(['coloc_id' => $this->coloc->id, 'avatar_slug' => 'personnage-01', 'order' => 0]);
    Roommate::factory()->create(['coloc_id' => $this->coloc->id, 'avatar_slug' => 'personnage-02', 'order' => 1]);
    $this->coloc->createDefaultTasks();
    $this->coloc->tasks()->update(['enabled' => true]);
});

it('loads the qr index page', function () {
    $this->get(route('coloc.qr', $this->coloc))
        ->assertOk()
        ->assertSee('QR Codes');
});

it('generates coloc qr svg', function () {
    $response = $this->get(route('coloc.qr.coloc', $this->coloc));
    $response->assertOk();
    $response->assertHeader('Content-Type', 'image/svg+xml');
    expect($response->getContent())->toContain('<svg');
});

it('generates task qr svg', function () {
    $task = $this->coloc->tasks()->first();
    $response = $this->get(route('coloc.qr.task', [$this->coloc, $task]));
    $response->assertOk();
    $response->assertHeader('Content-Type', 'image/svg+xml');
    expect($response->getContent())->toContain('<svg');
});

it('shows task page with assigned roommate', function () {
    $task = $this->coloc->tasks()->first();
    $this->get(route('coloc.task', [$this->coloc, $task]))
        ->assertOk()
        ->assertSee($task->name)
        ->assertSee('C\'est le tour de', false);
});

it('shows qr index with all enabled tasks', function () {
    $this->get(route('coloc.qr', $this->coloc))
        ->assertOk()
        ->assertSee('Vaisselle')
        ->assertSee('Imprimer cette page');
});
