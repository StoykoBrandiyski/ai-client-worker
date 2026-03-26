<?php


namespace App\Services\JobProcess;


use App\Repositories\Contracts\ProcessRepositoryInterface;
use App\Services\ProcessService;
use Illuminate\Console\Scheduling\Schedule;

class KernalProcess
{
    public function __construct(
        private ProcessRepositoryInterface $processRepository)
    {}

    public function execute(Schedule $schedule)
    {
        // Retrieve all enabled processes from the database\
        $processes = $this->processRepository->getAllByFields(['is_enabled' => 1]);

        foreach ($processes as $process) {
            $schedule->call(function () use ($process) {
                app(ProcessService::class)->runScheduledProcess($process);
            })->cron($process->schedule)
                ->name('process_' . $process->id)
                ->withoutOverlapping(); // Prevent same process running twice if previous is slow
        }
    }
}
