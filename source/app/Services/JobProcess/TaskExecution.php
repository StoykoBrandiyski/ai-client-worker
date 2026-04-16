<?php

namespace App\Services\JobProcess;

use App\Exceptions\NoSuchException;
use App\Models\Process;
use App\Models\Task;
use App\Repositories\Contracts\ProcessLogRepositoryInterface;
use App\Services\Engine\EngineFactory;
use ErrorException;
use Exception;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

class TaskExecution
{
    public function __construct(
       private ProcessLogRepositoryInterface $processLogRepository
    ) {
    }

    public function runTaskInProcess(Task $task, Process $process)
    {
        //1. Set process status => running
        // TODO Uncomment
        $process->update(['status' => 'running']);
        //2. Get process models order by sort_id ASC

        $startedAt = now();
        $engineId = 0;
        $engineModelIdentifier = 0;
        $status = '';
        $message = 'Execution success Process';
        foreach ($process->models as $model) {

            $engineModel = $model->engineModel;

            try {
                $engineProvider = EngineFactory::make($engineModel->identifier);

                $result = $engineProvider->run($task, $engineModel);

                $engineModelIdentifier = $engineModel->identifier;
                $engineId = $engineModel->engine->id;
                $status = 'success';
                $task->update(
                    [
                        'response_content' => $result['content'],
                        'status' => 'completed'
                    ]
                );

                $process->update(['status' => 'ready']);
                break;
            } catch (RequestException|ErrorException $e) {
                $message = $e->getMessage();
                $status = 'error';

                if ($e->getCode() == 403) {
                    continue;
                }
            } catch (NoSuchException | HttpClientException $e) {
                $this->handleFailure($task, $process, $e->getMessage());
                $message = $e->getMessage();
                $status = 'error';
            } catch (Exception $e) {
                if (str_contains($e->getMessage(), 'MAX_TOKENS')) {
                    Log::warning("Task #{$task->id} cut off due to token limit. Consider simplifying the prompt.");
                }

                $message = $e->getMessage();
                $status = 'error';
                Log::warning("Model {$model->engineModel->identifier} failed: " . $e->getMessage());
                continue;
            }
        }
        if ($engineId && $engineModelIdentifier) {
            $this->processLogRepository->createLog([
                'process_id'    => $process->id,
                'engine_id'     => $engineId,
                'engine_model_identifier' => $engineModelIdentifier,
                'status'        => $status ?? 'error',
                'started_at'    => $startedAt,
                'finished_at'       => now(),
                'process_message' => $message,
                'task_id'         => $task->id
            ]);
        }
        Log::info("Execution success Process");
    }

    private function handleFailure(Task $task, Process $process, string $reason)
    {
        $task->update(['status' => 'failed']);
        $process->update(['status' => 'failed']);
        Log::error("Task #{$task->id}. Error: ". $reason);
    }
}
