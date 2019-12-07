<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Process\Process;

class Composer extends Command
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
    protected $name = 'composer';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Run composer commands on a ephemeral test container.';


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

        $this->info("Running composer...");

        $process = app('App\Process', [
            'docker-compose',
            '-f',
            $this->filename,
            'run',
            '--rm',
            '-w',
            '/var/www/html',
            'app',
            ...$arguments
        ]);

        $process->setTty(Process::isTtySupported());

        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        $this->info('Composer executed.');
    }
}
