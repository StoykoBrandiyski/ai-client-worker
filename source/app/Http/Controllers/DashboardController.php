<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Repositories\Contracts\ProcessLogRepositoryInterface;

class DashboardController extends Controller
{
    /**
     * DashboardController constructor.
     * @param ProcessLogRepositoryInterface $processLogRepository
     */
    public function __construct(
        private ProcessLogRepositoryInterface $processLogRepository
    ) {
    }

    // Show Register Form
    public function show()
    {
        // 1. High-level Stats
        $stats = [
            'total_tasks'    => Task::count(),
            'pending_tasks'  => Task::where('status', 'pending')->count(),
            'success_rate'   => Task::count() > 0 ? round((Task::where('status', 'completed')->count() / Task::count()) * 100) : 0
        ];

        // 2. Latest Process Logs (Using your new Repository)
        $latestLogs = $this->processLogRepository->getLatestLogs(10);

        return view('welcome', compact('stats', 'latestLogs', 'tasks'));
    }
}
