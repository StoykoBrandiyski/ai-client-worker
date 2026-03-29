<?php
namespace App\Http\Controllers;

use App\Exceptions\NoSuchException;
use App\Models\Task;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Services\GroupService;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\StoreChildRequest;
use App\Repositories\PromptTemplateRepository;
use App\Repositories\Contracts\TaskRepositoryInterface;
use InvalidArgumentException;
use LogicException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TaskController extends Controller {

    /**
     * TaskController constructor.
     * @param TaskService $service
     * @param TaskRepositoryInterface $taskRepo
     * @param GroupService $groupService
     * @param PromptTemplateRepository $promptTemplateRepository
     */
    public function __construct(
        private TaskService $service,
        private TaskRepositoryInterface $taskRepo,
        private GroupService $groupService,
        private PromptTemplateRepository $promptTemplateRepository
    ) {}

    /**
     * @param Request $request
     * @return View|Factory
     */
    public function getList(Request $request): View|Factory
    {
        $tasks = [];//$this->service->search($request->all());
        return view('tasks.index', compact('tasks'));
    }

    /**
     * @param int $id
     * @return View|Factory
     */
    public function getListByGroupId(int $id): View|Factory
    {
        $tasks = $this->taskRepo->getListByGroupId($id);
        return view('tasks.index', compact('tasks'));
    }

    /**
     * @param StoreTaskRequest $request
     * @return RedirectResponse
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';
        $this->service->storeTask($data, $request->file('images'));
        return redirect()->back()->with('success', 'Task Updated');
    }

    /**
     * @param StoreChildRequest $request
     * @return RedirectResponse
     */
    public function storeChild(StoreChildRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';
        try {
            $this->service->storeTask($data, $request->file('images'));
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->back()->with('success', 'Task Updated');
    }


    /**
     * @param $id
     * @return StreamedResponse
     */
    public function download($id): StreamedResponse
    {
        return $this->service->getDownloadResponse($id);
    }

    /**
     * @return View|Factory
     */
    public function createTasks(): View|Factory
    {
        $groups = $this->groupService->listGroups();
        $templates = $this->promptTemplateRepository->getAll();
        return view('tasks.create', compact(['groups', 'templates']));
    }

    /**
     * @param $id
     * @return Factory|View|RedirectResponse
     */
    public function editTaskId($id): Factory|View|RedirectResponse
    {
        try {
            $task = $this->taskRepo->getById($id);
        } catch (NoSuchException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $taskStatus = $task->status;
        if ($task->children->isNotEmpty()) {
            $lastChild = $task->children->last();
            $taskStatus = $lastChild->status;
        }

        return view('tasks.detail', compact('task', 'taskStatus'));
    }

    /**
     * @param Task $task
     * @return Redirector|RedirectResponse
     */
    public function destroy(Task $task): Redirector|RedirectResponse
    {
        $groupId = $task->group_id;
        $parentId = $task->parent_id;

        // This will also delete children if you have 'onDelete(cascade)' in your migration
        try {
            $this->taskRepo->delete($task);
        } catch (LogicException $e) {
            return back()->with('error', $e->getMessage());
        }
        if ($parentId) {
            // It was a reply, stay on the current task detail page
            return back()->with('success', 'Reply deleted successfully.');
        }

        // It was the main task, go back to the group list
        return redirect("/groups/{$groupId}")->with('success', 'Task deleted successfully.');
    }
}
