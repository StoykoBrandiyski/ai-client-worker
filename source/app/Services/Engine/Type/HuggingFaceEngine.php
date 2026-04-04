<?php

namespace App\Services\Engine\Type;

use App\Models\EngineModel;
use App\Models\Task;
use App\Services\Engine\EngineProviderInterface;
use AzahariZaman\Huggingface\Exceptions\TransporterException;
use AzahariZaman\Huggingface\Exceptions\UnserializableResponse;
use AzahariZaman\Huggingface\Huggingface;
use Illuminate\Http\Client\HttpClientException;
use InvalidArgumentException;
use OutOfRangeException;

class HuggingFaceEngine  implements EngineProviderInterface
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
        $huggingFaceModel = str_replace(strtolower($engine->name).'-', '', $engineModel->identifier);

        if ($this->totalTasksCount >= $engine->max_tasks_count) {
            throw new OutOfRangeException("Max tasks count is reached from engine");
        }

        if (!$engineModel->use_chat) {
            throw new InvalidArgumentException("The model has only chat version");
        }
        // 2. Format the history array
        $messages = [];
        $messages[] = [
            'role'  => 'system',
            'content' => $engineModel->initial_prompt ?? ''
        ];

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

        $httpClient = new \GuzzleHttp\Client([
            'timeout'         => $engine->task_timeout ?? 30,
            'connect_timeout' => 15,
            'verify'          => false, // Bypasses local XAMPP missing CA certificate issues
        ]);
        $client = Huggingface::factory()
            ->withApiKey((base64_decode($engine->auth_token)))
            ->withHttpClient($httpClient)
            ->withQueryParam('wait_for_model', 'true')
            ->make();

        try {
            $result = $client->chatCompletion()->create([
                'model' => $huggingFaceModel,
                'messages' => $messages,
                'max_tokens' => 1026,
                'temperature' => 0.7,
            ]);
        } catch (TransporterException | UnserializableResponse $e) {
            throw new HttpClientException($e->getMessage());
        }

        if (!isset($result->choices[0])) {
            throw new HttpClientException("Missing response message content");
        }

        if (empty($result->choices[0]->message->content)) {
            throw new HttpClientException("Content is empty");
        }
        return [
            'content' =>  $result->choices[0]->message->content
        ];
    }
}
