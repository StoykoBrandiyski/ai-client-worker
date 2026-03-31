<?php

namespace App\Jobs;

use App\Models\Process;
use App\Models\Task;
use App\Services\JobProcess\TaskExecution;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TaskProcessJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800; // 10 minutes

    /**
     * @param Task $task
     * @param Process $process
     * @param int $currentModelIndex The index of the model in the priority sequence
     */
    public function __construct(
        private Task $task,
        private Process $process,
        private int $currentModelIndex = 0
    ) {}

    public function handle(TaskExecution $service)
    {
        $this->timeout = $this->process->timeout;
        $service->runTaskInProcess(
            $this->task,
            $this->process
        );
    }
}
