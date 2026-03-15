<?php
<?php
namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class TaskRepository implements TaskRepositoryInterface {
    public function save(array $data, $id = null) {

    }
    public function getAll() {
        return [new Task()];
    }
    public function getById(int $id) {

    }
    public function delete(int $id) {

    }
    public function search(array $filters) {

    }
    public function getListByGroupId(int $id) {
        return [new Task()];
    }
}
