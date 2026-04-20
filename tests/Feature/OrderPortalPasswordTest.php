<?php

use App\Models\OrderPortalPassword;
use App\Models\Restaurant;
use App\Models\User;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->restaurant = Restaurant::create(['name' => 'Test Restaurant', 'location' => 'Dar', 'is_active' => true]);
    $this->manager = User::factory()->create(['restaurant_id' => $this->restaurant->id]);
    $this->manager->assignRole('manager');
    $this->waiter = User::factory()->create([
        'restaurant_id' => $this->restaurant->id,
        'global_waiter_number' => '0042',
    ]);
    $this->waiter->assignRole('waiter');
});

test('order portal PIN generator yields unique four digit codes', function () {
    for ($i = 0; $i < 20; $i++) {
        expect(OrderPortalPassword::generateRandomPassword())->toMatch('/^\d{4}$/');
    }
});

test('order portal PIN generator skips pins already in use', function () {
    $otherWaiter = User::factory()->create([
        'restaurant_id' => $this->restaurant->id,
        'global_waiter_number' => '0100',
    ]);
    $otherWaiter->assignRole('waiter');

    OrderPortalPassword::create([
        'restaurant_id' => $this->restaurant->id,
        'user_id' => $otherWaiter->id,
        'password' => '1111',
        'generated_at' => now(),
    ]);

    for ($i = 0; $i < 30; $i++) {
        expect(OrderPortalPassword::generateRandomPassword())->not->toBe('1111');
    }
});

test('manager generate order portal password flashes four digit PIN', function () {
    $response = $this->actingAs($this->manager)
        ->post(route('manager.waiters.generate-order-portal-password', $this->waiter));

    $response->assertRedirect();
    $response->assertSessionHas('order_portal_password_generated');
    expect(session('order_portal_password_generated'))->toMatch('/^\d{4}$/');
});

test('waiter can log into order portal with manager generated PIN', function () {
    $this->actingAs($this->manager)
        ->post(route('manager.waiters.generate-order-portal-password', $this->waiter));

    $pin = session('order_portal_password_generated');
    expect($pin)->toMatch('/^\d{4}$/');

    $login = $this->post(route('order-portal.login'), ['password' => $pin]);

    $login->assertRedirect(route('order-portal.orders'));
});
