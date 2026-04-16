<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\ProcessLogRepositoryInterface;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class ProcessLogController extends Controller
{

    /**
     * ProcessLogController constructor.
     * @param ProcessLogRepositoryInterface $processLogRepo
     */
    public function __construct(
        private ProcessLogRepositoryInterface $processLogRepo
    ) {
    }

    /**
     * Display a listing of the process execution logs.
     */
    public function index(): View|Factory
    {
        // Fetch logs with 15 items per page
        $logs = $this->processLogRepo->getLatestLogs(15);

        return view('process_logs.index', compact('logs'));
    }
}
