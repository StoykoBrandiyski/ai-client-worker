<?php
namespace App\Repositories\Contracts;

use App\Models\Task;

interface TaskRepositoryInterface {
    public function save(array $data, $id = null);
    public function getAll();
    public function getById(int $id);
    public function delete(Task $task);
    public function deleteById(int $id);
    public function search(array $filters);
    public function getListByGroupId(int $id);
    public function getListByGroupIdWithLatestChildStatus(int $groupId);

}
