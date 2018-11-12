<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Up extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'up';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Launch your docker containers';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $file = "docker-compose.{$this->option('env')}.yml";

        if (!file_exists(getcwd() . '/' . $file)) {
            $this->error("{$file} does not exist!"); die;
        }

        exec("docker-compose -f {$file} up -d");
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
