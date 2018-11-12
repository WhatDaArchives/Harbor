<?php

namespace App\Commands;

use App\Builders\DockerComposeBaseBuilder;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Yaml\Yaml;

class Init extends Command
{
    /**
     * @var boolean
     */
    protected $mysql = true;

    /**
     * @var boolean
     */
    protected $redis = true;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'init:laravel';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Initialize your project to use Docker for Laravel';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->mysql = $this->confirm('Will you be using a database?', true);
        $this->redis = $this->confirm('Will you be using a redis?', true);

        // Generate docker-compose
        $this->info('Generate docker-compose.base.yml...');
        $this->generateDockerComposeBase();
    }

    /**
     * @return void
     */
    private function generateDockerComposeBase(): void
    {
        $dockerComposeBase = new DockerComposeBaseBuilder();

        if ($this->mysql) {
            $dockerComposeBase->includeMySQL();
        }

        if ($this->redis) {
            $dockerComposeBase->includeRedis();
        }

        $this->writeFile('docker-compose.base.yml', (string) $dockerComposeBase);
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
