<?php
namespace App\Http\Controllers;

use App\Exceptions\NoSuchException;
use App\Services\GroupService;
use App\Http\Requests\StoreGroupRequest;
use App\DTOs\GroupDTO;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Http\Request;

class GroupController extends Controller {

    private GroupService $groupService;
    private TaskRepositoryInterface $taskRepo;

    public function __construct(
         GroupService $groupService,
         TaskRepositoryInterface $taskRepo
    ) {
        $this->groupService = $groupService;
        $this->taskRepo = $taskRepo;
    }

    public function getAll(Request $request) {
        $groups = $this->groupService->listGroups();
        return view('groups.index', compact('groups'));
    }

    public function getListByGroupId(int $id) {
        $tasks = $this->taskRepo->getListByGroupId($id);
        return view('groups.tasks', compact('tasks'));
    }


    public function storeGroup(Request $request) {
        $allGroups = $this->groupService->listGroups();

        return view('groups.storeGroup', compact('allGroups'));
    }

    public function store(StoreGroupRequest $request) {
        $dto = GroupDTO::fromRequest($request);
        $this->groupService->storeOrUpdate($dto, $request->id);
        return redirect('/groups')->with('success', 'Operation Successful');
    }

    public function getById($id) {
        try {
            $group = $this->groupService->findGroup($id);
        } catch (NoSuchException $e) {
            return back()->with('error', $e->getMessage());
        }

        $tasks = $this->taskRepo->getListByGroupIdWithLatestChildStatus($id);
        return view('groups.tasks', compact('group', 'tasks'));
    }

    public function destroy($id) {
        $this->groupService->removeGroup($id);
        return redirect('/groups');
    }
}
