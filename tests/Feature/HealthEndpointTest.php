<?php

declare(strict_types=1);

it('returns health payload', function (): void {
    $response = $this->getJson('/api/health');

    $response->assertOk()
        ->assertJsonStructure(['status', 'timestamp', 'version']);
});
