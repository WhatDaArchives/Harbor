<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Process\Process;

class PhpUnit extends Command
{
    /**
     * @var string
     */
    protected $filename = 'docker-compose.yml';

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $name = 'phpunit';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Run phpunit command on a ephemeral test container.';


    public function __construct()
    {
        parent::__construct();
        $this->ignoreValidationErrors();
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('env')) {
            $this->filename = "docker-compose.{$this->option('env')}.yml";
        }

        if (!file_exists(getcwd() . '/' . $this->filename)) {
            $this->error("{$this->filename} does not exist!");
            return 1;
        }

        $arguments = explode(' ', $this->input->__toString());

        array_shift($arguments);

        $this->info("Running PHPUnit...");

        $process = app(
            'App\Process',
            [
                'docker-compose',
                '-f',
                $this->filename,
                'run',
                '--rm',
                '-w',
                '/var/www/html',
                'app',
                'vendor/bin/phpunit',
                ...$arguments
            ]
        );

        $process->setTty(Process::isTtySupported());

        $exitCode = $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        $this->info('PHPUnit executed.');

        return $exitCode;
    }
}
