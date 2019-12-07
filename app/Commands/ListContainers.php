<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ListContainers extends Command
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
    protected $signature = 'ps';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List your docker containers';

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
            die;
        }

        $arguments = explode(' ', $this->input->__toString());

        array_shift($arguments);

        $this->info('Listing container...');

        $process = app('App\Process', [
            'docker-compose',
            '-f',
            $this->filename,
            'ps',
            ...$arguments
        ]);

        $exitCode = $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        return $exitCode;
    }
}
