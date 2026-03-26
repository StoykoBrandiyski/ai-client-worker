<?php

namespace App\Console;

use App\Services\JobProcess\KernalProcess;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * @var KernalProcess
     */
    private KernalProcess $kernalProcess;

    public function __construct(Application $app, Dispatcher $events, KernalProcess $kernalProcess)
    {
        parent::__construct($app, $events);

        $this->kernalProcess = $kernalProcess;
    }

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $this->kernalProcess->execute($schedule);
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
