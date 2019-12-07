<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Process;

class Run extends Command
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
    protected $name = 'run';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Run any commands on a ephemeral test container.';


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

        $this->info("Running command...");

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

        $exitCode = $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        $this->info('Command executed.');
        return $exitCode;
    }
}
