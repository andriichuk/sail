<?php

namespace Laravel\Sail\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class QueueWorkerMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:queue-worker {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make queue worker';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $queue = $this->argument('name');

        if (! File::exists(base_path('docker-compose.yml'))) {
            $this->error('The `docker-compose.yml` file does not exist! Please run `sail:install` command first.');

            return 1;
        }

        $dockerCompose = File::get(base_path('docker-compose.yml'));

        if (Str::contains($dockerCompose, 'queue-'.$queue.'-worker')) {
            $this->error('Queue worker already exists!');

            return 1;
        }

        $queueWorker = File::get(__DIR__ . '/../../stubs/queue-worker.stub');

        $queueWorker = str_replace('{{queue}}', $queue, $queueWorker);

        $dockerCompose = str_replace('services:', "services:\n".rtrim($queueWorker), $dockerCompose);

        File::put(base_path('docker-compose.yml'), $dockerCompose);

        $this->info('Queue worker created successfully. Please restart Sail processes.');

        return 0;
    }
}
