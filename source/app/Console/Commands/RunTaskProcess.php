<?php

namespace App\Console\Commands;

use App\Models\Process;
use App\Repositories\Contracts\ProcessRepositoryInterface;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\JobProcess\TaskExecution;
use App\Services\ProcessService;
use Illuminate\Console\Command;

class RunTaskProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-task-process {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually trigger a specific process by ID';

    /**
     * Execute the console command.
     * @param TaskExecution $service
     * @param TaskRepositoryInterface $taskRepository
     * @param ProcessRepositoryInterface $processRepository
     */
    public function handle(
        TaskExecution $service,
        TaskRepositoryInterface $taskRepository,
        ProcessRepositoryInterface $processRepository
    ) {
        $process = $processRepository->getById((int) $this->argument('id'));

        if (!$process) {
            $this->error("Process not found.");
            return;
        }

        $task = $taskRepository->getById(17);
        $this->info("Starting process: {$process->name}...");
        $service->runTaskInProcess($task, $process);
        $this->info("Process jobs have been dispatched to the queue.");
    }
}
