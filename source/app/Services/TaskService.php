<?php
namespace App\Services;

use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Exceptions\NoSuchException;
use Illuminate\Support\Facades\Storage;

class TaskService {
    private TaskRepositoryInterface $repository;

    public function __construct(
        TaskRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function storeTask(array $data, $images = []) {
        // Business Logic: Parent ID management
        if (isset($data['reply_to_task_id'])) {
            $parent = $this->repository->getById($data['reply_to_task_id']);

            foreach ($parent->only($parent->getFillable()) as $field => $value) {
                if (in_array($field, ['request_content', 'parent_id'])) {
                    continue;
                }
                $data[$field] = $value;
            }
            $data['parent_id'] = $parent->parent_id ?? $parent->id;
            unset($data['reply_to_task_id']);
        }

        $task = $this->repository->save($data);

        // Image Upload Logic (Max 3)
        if ($images) {
            foreach (array_slice($images, 0, 3) as $image) {
                $filename = $task->id . '_' . time() . '.' . $image->extension();
                $path = $image->storeAs('task-images', $filename, 'public');
                $task->images()->create(['path' => $path]);
            }
        }
        return $task;
    }

    public function getDownloadResponse($taskId) {
        $task = $this->repository->getById($taskId);
        $content = $task->response_content;

        // Syntax Detection Logic
        $extension = 'txt';
        $map = ['<?php' => 'php', 'import ' => 'py', 'function' => 'js', '<html>' => 'html'];
        foreach ($map as $snippet => $ext) {
            if (str_contains($content, $snippet)) {
                $extension = $ext;
                break;
            }
        }

        return response()->streamDownload(function() use ($content) {
            echo $content;
        }, "task_{$task->id}.{$extension}");
    }
}
