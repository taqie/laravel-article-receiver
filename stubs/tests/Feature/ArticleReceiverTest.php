<?php

declare(strict_types=1);

use Tests\TestCase;

uses(TestCase::class);

it('health endpoint works', function (): void {
    $response = $this->getJson('/api/health');

    $response->assertOk()->assertJsonStructure(['status', 'timestamp', 'version']);
});
