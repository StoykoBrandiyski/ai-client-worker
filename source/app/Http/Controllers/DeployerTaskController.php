<?php

namespace App\Http\Controllers;

use App\Models\Task;
use InvalidArgumentException;
use App\Services\TaskService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Stoyko\SandboxDeployer\DeployProcess;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Stoyko\SandboxDeployer\Exceptions\AppErrorException;
use Stoyko\SandboxDeployer\Exceptions\CodeErrorException;
use Stoyko\SandboxDeployer\Exceptions\DatabaseErrorException;
use Stoyko\SandboxDeployer\Exceptions\DeployException;

class DeployerTaskController extends Controller
{

    /**
     * DeployerTaskController constructor.
     * @param TaskRepositoryInterface $taskRepository
     * @param TaskService $taskService
     * @param DeployProcess $deployProcess
     */
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private TaskService $taskService,
        private DeployProcess $deployProcess
    ) {
    }

    public function deploy(Task $task): RedirectResponse
    {
        $taskContent = $this->taskRepository->getLatestChild($task)->response_content
                        ?? $task->response_content;

        $output = '';
        $isWillCreateChildTask = false;
        try {
            $output = $this->deployProcess->run($taskContent);
        } catch (CodeErrorException | DatabaseErrorException $e) {
            //create new child task
            $isWillCreateChildTask = true;
            $output = $e->getMessage();
        } catch (AppErrorException | DeployException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        if ($isWillCreateChildTask) {
            $data['user_id'] = 1; //TODO Create tester-deploy user and remove hardcoded.
            $data['status'] = 'pending';
            $data['reply_to_task_id'] = $task->id;
            $data['request_content'] = $output;

            try {
                $this->taskService->storeTask($data, []);
            } catch (InvalidArgumentException $e) {
                // log file
                Log::error($e->getMessage());
            }

            $output = 'Successful create new child task';
        }

        return redirect()->back()->with('success', $output);
    }
}
