<?php
namespace App\Repositories;

use App\Models\Group;
use App\Repositories\Contracts\GroupRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class GroupRepository implements GroupRepositoryInterface {
    public function getAll() {
        return Cache::remember('groups_list', 3600, function() {
            return Group::select('id', 'name')
                ->withCount('tasks')
                ->with(['latestThreeTasks' => function($q) {
                    $q->select('id', 'name', 'status', 'group_id');
                }])->get();
        });
    }

    public function save(array $data, $id = null) {
        Cache::forget('groups_list');
        return Group::updateOrCreate(['id' => $id], $data);
    }

    public function getById(int $id) {
        return Group::findOrFail($id);
    }

    public function delete(int $id) {
        Cache::forget('groups_list');
        return Group::destroy($id);
    }

    public function search(array $filters) {
        $query = Group::query();
        if (!empty($filters['name'])) {
            $query->where('name', 'LIKE', '%' . $filters['name'] . '%');
        }
        return $query->paginate(10);
    }
}