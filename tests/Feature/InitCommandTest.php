<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class InitCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->deleteDockerCompose();
    }

    /** @test */
    public function it_can_initialize()
    {
        $this->deleteDockerCompose();
        $this->artisan('init', ['--no-interaction' => true])
            ->expectsOutput('Harbor initialized!')
            ->assertExitCode(0);

        $this->assertTrue(File::exists(getcwd() . '/docker-compose.yml'));

        $this->deleteDockerCompose();
    }
}
