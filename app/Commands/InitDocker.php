<?php

namespace App\Commands;

use App\Builders\DockerComposeBuilder;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Yaml\Yaml;

class InitDocker extends Command
{
    /**
     * @var string
     */
    protected $filename = 'docker-compose.yml';

    /**
     * @var boolean
     */
    protected $settings = [];

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'init {--force : Force initialization}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Initialize your project with docker-compose';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Does the docker-compose file already exist for the given environment?
        if (file_exists(getcwd() . '/' . $this->filename) && !$this->option('force')) {
            $this->error('Docker already initialized for this environment!');
            die;
        }

        // Is this initializing for a given environment?
        if ($this->option('env')) {
            $this->filename = "docker-compose.{$this->option('env')}.yml";
        }

        $this->settings['mysql'] = $this->confirm('Will you be using a database?', true);
        $this->settings['redis'] = $this->confirm('Will you be using a redis?', true);

        // Generate docker-compose
        $this->info("Generate {$this->filename}...");
        $this->generateDockerCompose();
    }

    /**
     * @return void
     */
    private function generateDockerCompose(): void
    {
        $dockerCompose = new DockerComposeBuilder();

        if ($this->settings['mysql']) {
            $dockerCompose->includeMySQL();
        }

        if ($this->settings['redis']) {
            $dockerCompose->includeRedis();
        }

        $this->writeFile($this->filename, (string)$dockerCompose);
    }

    /**
     * @param string $name
     * @param string $contents
     */
    private function writeFile(string $name, string $contents): void
    {
        file_put_contents(getcwd() . '/' . $name, $contents);
    }
}
