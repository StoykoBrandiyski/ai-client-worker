<?php
namespace App\Http\Controllers;

use App\Exceptions\NoSuchException;
use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Services\GroupService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\StoreChildRequest;
use App\Repositories\PromptTemplateRepository;
use App\Repositories\Contracts\TaskRepositoryInterface;

class TaskController extends Controller {

    private TaskService $service;
    private TaskRepositoryInterface $taskRepo;
    private GroupService $groupService;
    private PromptTemplateRepository $promptTemplateRepository;


    public function __construct(
        TaskService $service,
        TaskRepositoryInterface $taskRepo,
        GroupService $groupService,
        PromptTemplateRepository $promptTemplateRepository
    ) {
        $this->service = $service;
        $this->taskRepo = $taskRepo;
        $this->groupService  = $groupService;
        $this->promptTemplateRepository = $promptTemplateRepository;
    }

    public function getList(Request $request) {
        $tasks = [];//$this->service->search($request->all());
        return view('tasks.index', compact('tasks'));
    }

    public function getListByGroupId(int $id) {
        $tasks = $this->taskRepo->getListByGroupId($id);
        return view('tasks.index', compact('tasks'));
    }

    public function store(StoreTaskRequest $request) {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';
        $this->service->storeTask($data, $request->file('images'));
        return redirect()->back()->with('success', 'Task Updated');
    }

    public function storeChild(StoreChildRequest $request) {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';
        $this->service->storeTask($data, $request->file('images'));
        return redirect()->back()->with('success', 'Task Updated');
    }


    public function download($id) {
        return $this->service->getDownloadResponse($id);
    }

    public function createTasks() {
        $groups = $this->groupService->listGroups();
        $templates = $this->promptTemplateRepository->getAll();
        return view('tasks.create', compact(['groups', 'templates']));
    }

    public function editTaskId($id) {
        try {
            $task = $this->taskRepo->getById($id);
        } catch (NoSuchException $e) {
        }

        $taskStatus = $task->status;
        if ($task->children) {
            $lastChild = $task->children->last();
            $taskStatus = $lastChild->status;
        }

        return view('tasks.detail', compact('task', 'taskStatus'));
    }
}
