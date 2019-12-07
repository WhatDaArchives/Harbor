<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Class ComposerCommandTest
 * @package Tests\Feature
 * @preserveGlobalState disabled
 */
class ComposerCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        File::delete(getcwd() . '/docker-compose.yml');
    }

    /** @test */
    public function it_can_run_a_command_on_a_one_off_container()
    {
        $this->mockProcess('Composer executed.');

        $this->createDockerCompose();

        $this->artisan('composer', ['--no-interaction' => true])
             ->expectsOutput('Composer executed.')
             ->assertExitCode(0);

        $this->deleteDockerCompose();
    }

    /** @test */
    public function it_can_run_a_command_on_a_one_off_container_with_arguments()
    {
        $this->mockProcess('Composer executed.');

        $this->createDockerCompose();

        $this->artisan('composer', ['-v', '--no-interaction' => true])
            ->expectsOutput('Composer executed.')
            ->assertExitCode(0);

        $this->deleteDockerCompose();
    }

    /** @test */
    public function it_displays_error_if_docker_compose_does_not_exists()
    {
        $this->mockProcess('', 'docker-compose.yml does not exist!');

        $this->deleteDockerCompose();

        $this->artisan('composer', ['--no-interaction' => true])
            ->expectsOutput('docker-compose.yml does not exist!')
            ->assertExitCode(1);
    }
}
