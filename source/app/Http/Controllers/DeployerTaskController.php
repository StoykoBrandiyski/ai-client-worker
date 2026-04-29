<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Http\Request;
use Stoyko\SandboxDeployer\DeployProcess;
use Stoyko\SandboxDeployer\Exceptions\DeployException;

class DeployerTaskController extends Controller
{
    //
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private DeployProcess $deployProcess
    ) {
    }

    public function deploy(Request $request)
    {
        $taskId = $request->input('task_id');
        $taskContent = $request->input('task_response_content');

        //@TODO this
//        try {
//            $task = $this->taskRepository->getById($taskId);
//        } catch (NoSuchException $e) {
//            return redirect()->back()->with('error', $e->getMessage());
//        }

        $output = '';
        try {
            $output = $this->deployProcess->run($taskContent);
        } catch (DeployException $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }

        return redirect()->back()->with('success', $output);
    }
}
