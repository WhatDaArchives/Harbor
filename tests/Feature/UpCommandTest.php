<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Class UpCommandTest
 * @package Tests\Feature
 * @preserveGlobalState disabled
 */
class UpCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        File::delete(getcwd() . '/docker-compose.yml');
    }

    /** @test */
    public function it_can_start_containers()
    {
        $this->mockProcess('Containers started.');

        $this->createDockerCompose();

        $this->artisan('up', ['--no-interaction' => true])
            ->expectsOutput('Containers started.')
            ->assertExitCode(0);

        $this->deleteDockerCompose();
    }

    /** @test */
    public function it_displays_error_if_docker_compose_does_not_exists()
    {
        $this->mockProcess('', 'docker-compose.yml does not exist!');

        $this->deleteDockerCompose();

        $this->artisan('up', ['--no-interaction' => true])
            ->expectsOutput('docker-compose.yml does not exist!')
            ->assertExitCode(1);
    }
}
