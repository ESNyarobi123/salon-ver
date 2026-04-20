<?php

use App\Models\User;

test('global waiter number generation uses eight hex characters', function () {
    $code = User::generateGlobalWaiterNumber();

    expect($code)->toMatch('/^[0-9A-F]{8}$/')
        ->and(strlen($code))->toBe(8);
});

test('global waiter number generation avoids collisions with reserved list', function () {
    $a = User::generateGlobalWaiterNumber();
    $b = User::generateGlobalWaiterNumber([$a]);

    expect($b)->not->toBe($a);
});
