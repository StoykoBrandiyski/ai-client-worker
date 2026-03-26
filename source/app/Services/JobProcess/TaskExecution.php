<?php

namespace App\Services\JobProcess;

use App\Exceptions\NoSuchException;
use App\Models\Process;
use App\Models\Task;
use App\Services\Engine\EngineFactory;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

class TaskExecution
{

    public function runTaskInProcess(Task $task, Process $process)
    {
        //1. Set process status => running
        // TODO Uncomment
        //$process->update(['status' => 'running']);
        //2. Get process models order by sort_id ASC

        foreach ($process->models as $model) {
            $engineModel = $model->engineModel;

            try {
                $engineProvider = EngineFactory::make($engineModel->identifier);

                $result = $engineProvider->run($task, $engineModel);

                $task->update(
                    [
                        'response_content' => $result['content'],
                        'status' => 'completed'
                    ]
                );
                break;
            } catch (NoSuchException $e) {
                $this->handleFailure($task, $e->getMessage());
            } catch (RequestException $e) {
                if ($e->getCode() == 403) {
                    continue;
                }
            }

            catch (\Exception $e) {
                if (str_contains($e->getMessage(), 'MAX_TOKENS')) {
                    \Log::warning("Task #{$task->id} cut off due to token limit. Consider simplifying the prompt.");
                }
                // Log error and allow the loop to continue to the next priority model
                \Log::warning("Model {$model->engineModel->identifier} failed: " . $e->getMessage());
                continue;
            }
        }
    }

    private function handleFailure(Task $task,  string $reason)
    {
        $task->update(['status' => 'failed']);
        Log::error("Task #{$task->id}. Error: ". $reason);
    }
}
