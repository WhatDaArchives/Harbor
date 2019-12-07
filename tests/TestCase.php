<?php

namespace Tests;

use App\Builders\DockerComposeBuilder;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Testing\TestCase as BaseTestCase;
use Mockery;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function mockProcess($output = '', $errorOutput = '')
    {
        $process = Mockery::mock('overload:Symfony\Component\Process\Process');
        $process->shouldReceive('setTty');
        $process->shouldReceive('isTtySupported');
        $process->shouldReceive('run')
            ->once()
            ->andReturn(true);
        $process->shouldReceive('isSuccessful')
            ->once()
            ->andReturn(true);
        $process->shouldReceive('getOutput')
            ->once()
            ->andReturn($output);
        $process->shouldReceive('getErrorOutput')
            ->once()
            ->andReturn($errorOutput);

        $this->app->bind('App\Process', function ($app, $args) use ($process) {
            return $process;
        });
    }

    /**
     *
     */
    public function createDockerCompose()
    {
        $dockerCompose = new DockerComposeBuilder();
        File::put(getcwd() . '/docker-compose.yml', (string) $dockerCompose);
    }

    /**
     * Delete docker-compose.yml
     */
    public function deleteDockerCompose() {
        File::delete(getcwd() . '/docker-compose.yml');
    }
}
