<?php
namespace App\Repositories;

use App\Exceptions\NoSuchException;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class TaskRepository implements TaskRepositoryInterface {
    public function getAll() {
        return Cache::remember('tasks_all', 3600, fn() => Task::with('group')->paginate(15));
    }

    public function getListByGroupId(int $id) {
        return Cache::remember("tasks_group_{$id}", 3600, function() use ($id) {
            $query = Task::query();
            $query->where('group_id', '=', $id);
            $query->where('parent_id', '=',null);
            $query->select('id', 'name', 'status', 'created_at');
            return $query->get();
        });
    }

    public function save(array $data, $id = null) {
        $task = Task::updateOrCreate(['id' => $id], $data);
        $this->clearCache($task->group_id);
        return $task;
    }

    public function delete(Task $task)
    {
        $groupId = $task->group_id;
        $task->delete();
        $this->clearCache($groupId);
    }

    public function deleteById(int $id)
    {
        $task = Task::find($id);
        if ($task) {
            $groupId = $task->group_id;
            $task->delete();
            $this->clearCache($groupId);
        }
    }

    public function search(array $filters) {
        $query = Task::query();
        if (!empty($filters['name'])) $query->where('name', 'like', "%{$filters['name']}%");
        if (!empty($filters['status'])) $query->where('status', $filters['status']);
        return $query->paginate(10);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws NoSuchException
     */
    public function getById(int $id)
    {
        $task = Task::find($id);
        if (!$task) {
            throw new NoSuchException("Task not found");
        }

        return $task;
    }

    private function clearCache($groupId) {
        Cache::forget('tasks_all');
        Cache::forget("tasks_group_{$groupId}");
    }
}
