<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Class RunCommandTest
 * @package Tests\Feature
 * @preserveGlobalState disabled
 */
class RunCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        File::delete(getcwd() . '/docker-compose.yml');
    }

    /** @test */
    public function it_can_run_a_command_on_a_one_off_container()
    {
        $this->mockProcess('Command executed.');

        $this->createDockerCompose();

        $this->artisan('run', ['ls' => true, '--no-interaction' => true])
             ->expectsOutput('Command executed.')
             ->assertExitCode(0);

        $this->deleteDockerCompose();
    }

    /** @test */
    public function it_can_run_a_command_on_a_one_off_container_with_arguments()
    {
        $this->mockProcess('Command executed.');

        $this->createDockerCompose();

        $this->artisan('run',['ls' => true, '-la' => true, '--no-interaction' => true])
            ->expectsOutput('Command executed.')
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
