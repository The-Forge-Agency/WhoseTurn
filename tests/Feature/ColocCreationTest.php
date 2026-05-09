<?php

use App\Models\Coloc;

test('landing page loads', function () {
    $this->get('/')->assertOk();
});

test('can create a coloc', function () {
    $response = $this->post('/', ['name' => 'Appart du 42']);

    $coloc = Coloc::first();
    expect($coloc)->not->toBeNull();
    expect($coloc->name)->toBe('Appart du 42');
    expect(strlen($coloc->share_code))->toBe(8);
    expect($coloc->tasks)->toHaveCount(10);

    $response->assertRedirect(route('coloc.setup', $coloc));
});

test('coloc name is required', function () {
    $this->post('/', ['name' => ''])->assertSessionHasErrors('name');
});

test('coloc name max 50 chars', function () {
    $this->post('/', ['name' => str_repeat('a', 51)])->assertSessionHasErrors('name');
});
