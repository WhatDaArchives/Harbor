<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Class DownCommandTest
 * @package Tests\Feature
 * @preserveGlobalState disabled
 */
class DownCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        File::delete(getcwd() . '/docker-compose.yml');
    }

    /** @test */
    public function it_can_start_containers()
    {
        $this->mockProcess('Containers stopped.');

        $this->createDockerCompose();

        $this->artisan('down', ['--no-interaction' => true])
             ->expectsOutput('Containers stopped.')
             ->assertExitCode(0);

        $this->deleteDockerCompose();
    }

    /** @test */
    public function it_displays_error_if_docker_compose_does_not_exists()
    {
        $this->mockProcess('', 'docker-compose.yml does not exist!');

        $this->deleteDockerCompose();

        $this->artisan('down', ['--no-interaction' => true])
            ->expectsOutput('docker-compose.yml does not exist!')
            ->assertExitCode(1);
    }
}
