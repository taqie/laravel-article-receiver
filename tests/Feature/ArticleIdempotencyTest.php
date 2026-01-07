<?php

declare(strict_types=1);

it('prevents duplicate article creation with idempotency key', function (): void {
    $payload = [
        'title' => 'Idempotent Article',
        'lead' => 'Lead text',
        'meta_description' => 'Meta description',
        'body' => '<p>Body</p>',
    ];

    $headers = ['X-Idempotency-Key' => 'test-key-123'];

    $first = $this->withHeaders($headers)->postJson('/api/articles', $payload);
    $first->assertCreated();

    $second = $this->withHeaders($headers)->postJson('/api/articles', $payload);
    $second->assertStatus(409);
});
