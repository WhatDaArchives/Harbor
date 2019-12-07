<?php

namespace Tests\Feature;

use App\Builders\DockerComposeBuilder;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Class PsCommandTest
 * @package Tests\Feature
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PsCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        File::delete(getcwd() . '/docker-compose.yml');
    }

    /** @test */
    public function it_can_list_docker_containers()
    {
        $this->mockProcess("Listing container...");

        $dockerCompose = new DockerComposeBuilder();
        File::put(getcwd() . '/docker-compose.yml', (string) $dockerCompose);
        $this->artisan('ps', ['--no-interaction' => true])
            ->expectsOutput("Listing container...")
            ->assertExitCode(0);
        File::delete(getcwd() . '/docker-compose.yml');
    }
}
