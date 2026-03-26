<?php

namespace App\Services\Engine\Type;

use App\Models\EngineModel;
use Exception;
use HosseinHezami\LaravelGemini\Facades\Gemini;
use App\Models\Task;
use App\Services\Engine\EngineProviderInterface;
use OutOfRangeException;

class GeminiEngine implements EngineProviderInterface
{
    private int $totalTasksCount = 0;

    /**
     * @param Task $task
     * @param EngineModel $engineModel
     * @throws Exception
     * @return array
     */
    public function run(Task $task, EngineModel $engineModel): array
    {
        $engine =  $engineModel->engine;

        if ($this->totalTasksCount >= $engine->max_tasks_count) {
            throw new OutOfRangeException("Max tasks count is reached from engine");
        }

        // 2. Format the history array
        $historyFormat = [];
        if ($engineModel->use_chat) {

            $parentTask = $task->parent;
            $sortedChildren = $parentTask?->children()->orderBy('id', 'asc')->get() ?? collect();

            // Append parent task first
            $userContent = [
                'role'  => 'user',
                'parts' => [['text' => $parentTask->request_content]]
            ];
            $modelContent = [
                'role'  => 'model',
                'parts' => [['text' => $parentTask->response_content]]
            ];
            $historyFormat[] = $userContent;
            $historyFormat[] = $modelContent;
            foreach ($sortedChildren as $parentChild) {
                if ($parentChild->id == $task->id) {
                    continue;
                }
                $userContent = [
                    'role'  => 'user',
                    'parts' => [['text' => $parentChild->request_content]]
                ];
                $modelContent = [
                    'role'  => 'model',
                    'parts' => [['text' => $parentChild->response_content]]
                ];

                $historyFormat[] = $userContent;
                $historyFormat[] = $modelContent;
            }
        }

        //return [ 'content' => 'Test'];
        $response = Gemini::setApiKey(base64_decode($engine->auth_token))
            ->text()
            ->model($engineModel->identifier)
                //TODO Change to be dynamic
            //->model('gemini-2.5-flash')
            ->system($engineModel->initial_prompt)
            ->prompt($task->request_content)
            ->history($historyFormat)
            ->temperature( 0.7)
            ->maxTokens(4096)
            ->generate();

        return [
            'content' => $response->content()
        ];
    }
}
