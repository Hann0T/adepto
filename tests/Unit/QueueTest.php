<?php

namespace Tests\Feature;

use Adepto\Bus\Queue;

test('Can enqueue', function () {
    $queue = new Queue();
    expect($queue->len)->toBe(0);
    expect($queue->head)->toBe(null);
    expect($queue->tail)->toBe(null);

    $queue->enqueue(12);
    expect($queue->len)->toBe(1);
    expect($queue->head?->value)->toBe(12);
    expect($queue->tail?->value)->toBe(12);

    $queue->enqueue(13);
    expect($queue->len)->toBe(2);
    expect($queue->head?->value)->toBe(12);
    expect($queue->tail?->value)->toBe(13);

    $queue->enqueue(14);
    expect($queue->len)->toBe(3);
    expect($queue->head?->value)->toBe(12);
    expect($queue->tail?->value)->toBe(14);
});

test('Can dequeue', function () {
    $queue = new Queue();
    expect($queue->len)->toBe(0);
    expect($queue->head)->toBe(null);
    expect($queue->tail)->toBe(null);

    $queue->enqueue(12);
    expect($queue->len)->toBe(1);
    expect($queue->head?->value)->toBe(12);
    expect($queue->tail?->value)->toBe(12);

    $queue->enqueue(13);
    expect($queue->len)->toBe(2);
    expect($queue->head?->value)->toBe(12);
    expect($queue->tail?->value)->toBe(13);

    $value = $queue->dequeue();
    expect($queue->len)->toBe(1);
    expect($value)->toBe(12);

    expect($queue->head?->value)->toBe(13);
    expect($queue->tail?->value)->toBe(13);

    $value = $queue->dequeue();
    expect($queue->len)->toBe(0);
    expect($value)->toBe(13);

    expect($queue->head)->toBe(null);
    expect($queue->tail)->toBe(null);

    $value = $queue->dequeue();
    expect($value)->toBe(null);
});
