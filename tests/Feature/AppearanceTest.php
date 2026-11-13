<?php

declare(strict_types=1);

use App\Models\User;

it('renders appearance edit page', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('appearance.edit'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('appearance/edit'));
});

it('redirects guests to login', function (): void {
    $response = $this->get(route('appearance.edit'));

    $response->assertRedirectToRoute('login');
});
