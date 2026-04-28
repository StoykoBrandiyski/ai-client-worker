<?php

namespace App\Http\Controllers;

use App\Exceptions\NoSuchException;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Http\Request;
use Stoyko\SandboxDeployer\Services\SandboxDeployer;
use Stoyko\SandboxDeployer\Services\CodeExtractor;

class DeployerTaskController extends Controller
{
    //
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private CodeExtractor $codeExtractor,
        private SandboxDeployer $sandboxDeployer
    ) {
    }

    public function deploy(Request $request)
    {
        $taskId = $request->input('task_id');
        $taskContent = $request->input('task_response_content');

//        try {
//            $task = $this->taskRepository->getById($taskId);
//        } catch (NoSuchException $e) {
//            return redirect()->back()->with('error', $e->getMessage());
//        }

        $cleanCode = array_first(
            $this->codeExtractor->extractUsefulCodeBlocks($taskContent)
        );

        $path = $this->codeExtractor->getMigrationPath($cleanCode) ??
            $this->codeExtractor->getFilePathFromCode($cleanCode);

        $this->sandboxDeployer->injectCode($path, $cleanCode);
        return redirect()->back()->with('success', 'Deployed succes');
    }
}
