<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Process;

class Up extends Command
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
    protected $name = 'up';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Launch your docker containers';

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

        $this->info('Starting containers...');

        $process = app('App\Process', [
            'docker-compose',
            '-f',
            $this->filename,
            'up',
            ...$arguments
        ]);

        $process->setTty(Process::isTtySupported());

        $exitCode = $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        $this->info('Containers started.');

        return $exitCode;
    }
}
