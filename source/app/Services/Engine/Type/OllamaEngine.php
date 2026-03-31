<?php

namespace App\Services\Engine\Type;


use App\Models\EngineModel;
use App\Models\Task;
use App\Services\Engine\EngineProviderInterface;
use Cloudstudio\Ollama\Facades\Ollama;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Facades\Storage;
use OutOfRangeException;

class OllamaEngine implements EngineProviderInterface
{
    private int $totalTasksCount = 0;

    /**
     * @param Task $task
     * @param EngineModel $engineModel
     * @throws HttpClientException
     * @return array
     */
    public function run(Task $task, EngineModel $engineModel): array
    {
        $engine =  $engineModel->engine;
        $ollamaModel = str_replace(strtolower($engine->name).'-', '', $engineModel->identifier);

        if ($this->totalTasksCount >= $engine->max_tasks_count) {
            throw new OutOfRangeException("Max tasks count is reached from engine");
        }

        // 2. Format the history array
        $messages = [];
        if ($engineModel->use_chat) {

            $parentTask = $task->parent;
            $sortedChildren = $parentTask?->children()->orderBy('id', 'asc')->get() ?? collect();

            if($sortedChildren->isNotEmpty()) {
                // Append parent task first
                $userContent = [
                    'role'  => 'user',
                    'content' => $parentTask->request_content
                ];
                $modelContent = [
                    'role'  => 'assistant',
                    'content' => $parentTask->response_content
                ];
                $messages[] = $userContent;
                $messages[] = $modelContent;
                foreach ($sortedChildren as $parentChild) {
                    if ($parentChild->id == $task->id) {
                        continue;
                    }

                    $userContent = [
                        'role'  => 'user',
                        'content' => $parentChild->request_content
                    ];
                    $modelContent = [
                        'role'  => 'assistant',
                        'content' => $parentChild->response_content
                    ];

                    $messages[] = $userContent;
                    $messages[] = $modelContent;
                }
            }
            $userContent = [
                'role'  => 'user',
                'content' => $task->request_content
            ];

            $messages[] = $userContent;
        }

        $files = [];
        foreach ($task->images as $image) {
            if (Storage::disk('public')->exists($image->path)) {
                $files[] = Storage::disk('public')->get($image->path);
            }
        }

        config([
            'ollama-laravel.url' => $engine->base_url,
            'ollama-laravel.connection.timeout' => $engine->task_timeout,
        ]);

        $ollamaRequest = Ollama::agent($engineModel->initial_prompt ?? '')
            ->model($ollamaModel)
            ->options([
                'temperature' => 0.9,
                'top_p' => 0.9,
                'num_predict' => 4096, // Ollama uses 'num_predict' instead of 'max_tokens'
            ]);

        if ($engineModel->use_chat) {
            // Chat endpoint
            $response = $ollamaRequest->chat($messages);
        } else {
            // Generate endpoint (include images here if needed)
            $response = $ollamaRequest
                ->prompt($task->request_content)
                ->images($files)
                ->ask();
        }

        if (isset($response['error'])) {
            throw new HttpClientException($response['error']);
        }

        if (!isset($response['message']['content'])) {
            throw new HttpClientException("Missing response message content");
        }

        if (empty($response['message']['content'])) {
            throw new HttpClientException("Content is empty");
        }
        return [
            'content' => $response['message']['content']
        ];
    }
}
