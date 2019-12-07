<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Class ArtisanCommandTest
 * @package Tests\Feature
 * @preserveGlobalState disabled
 */
class ArtisanCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        File::delete(getcwd() . '/docker-compose.yml');
    }

    /** @test */
    public function it_can_run_a_command_on_a_one_off_container()
    {
        $this->mockProcess('Artisan executed.');

        $this->createDockerCompose();

        $this->artisan('artisan', ['--no-interaction' => true])
             ->expectsOutput('Artisan executed.')
             ->assertExitCode(0);

        $this->deleteDockerCompose();
    }

    /** @test */
    public function it_can_run_a_command_on_a_one_off_container_with_arguments()
    {
        $this->mockProcess('Artisan executed.');

        $this->createDockerCompose();

        $this->artisan('artisan', ['inspire', '--no-interaction' => true])
            ->expectsOutput('Artisan executed.')
            ->assertExitCode(0);

        $this->deleteDockerCompose();
    }

    /** @test */
    public function it_displays_error_if_docker_compose_does_not_exists()
    {
        $this->mockProcess('', 'docker-compose.yml does not exist!');

        $this->deleteDockerCompose();

        $this->artisan('artisan', ['--no-interaction' => true])
            ->expectsOutput('docker-compose.yml does not exist!')
            ->assertExitCode(1);
    }
}
