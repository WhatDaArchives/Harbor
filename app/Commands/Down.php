<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Process;

class Down extends Command
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
    protected $signature = 'down';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Shut down your docker containers';

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

        $this->info('Stopping containers...');

        $process = app('App\Process', ['docker-compose',
            '-f',
            $this->filename,
            'down',
            ...$arguments
        ]);

        $process->setTty(Process::isTtySupported());

        $exitCode = $process->run(function ($type, $buffer) {
            $this->comment($buffer);
        });

        $this->info('Containers stopped.');
        return $exitCode;
    }
}
