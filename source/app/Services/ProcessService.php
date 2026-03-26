<?php

namespace App\Services;

use App\Jobs\TaskProcessJob;
use App\Models\Process;
use App\Models\ProcessCondition;
use App\Models\ProcessModel;
use App\Repositories\Contracts\ProcessRepositoryInterface;
use App\Models\Task;
use App\Exceptions\NoSuchException;
use App\DTOs\ProcessDTO;
use Exception;
use Illuminate\Support\Facades\DB;

class ProcessService {
    public function __construct(protected ProcessRepositoryInterface $repository) {}

    public function saveProcess(ProcessDTO $dto, array $modelIdentifiers = []) {
        try {
            return \DB::transaction(function () use ($dto, $modelIdentifiers) {

                // 1. Handle Process Condition
                $conditionId = $dto->conditionId;

                if (!$conditionId && $dto->newCondition) {
                    $condition = ProcessCondition::create($dto->newCondition);
                    $conditionId = $condition->id;
                }

                if (!$conditionId) {
                    throw new Exception("A Process Condition is required.");
                }

                // 2. Map DTO to the array required by the Repository
                $data = [
                    'name'         => $dto->name,
                    'status'       => $dto->status,
                    'is_enabled'   => $dto->isEnabled,
                    'condition_id' => $conditionId,
                    'schedule'     => $dto->schedule,
                    'timeout'      => $dto->timeout,
                    'limit_tasks'  => $dto->limitTasks,
                ];

                // 3. Save the Process via Repository
                $process = $this->repository->save($data, $dto->id);

                // 4. Sync Priority Models (Failover Sequence)
                // Remove existing mappings to prevent duplicates or orphaned records
                ProcessModel::where('process_id', $process->id)->delete();

                // Insert models in the order they were received from the UI
                foreach ($modelIdentifiers as $index => $identifier) {
                    ProcessModel::create([
                        'process_id' => $process->id,
                        'model_id'   => $identifier,
                        'sort_order' => $index + 1
                    ]);
                }

                return $process;
            });
        } catch (Exception $e) {
            throw new NoSuchException("Process Save Failed: " . $e->getMessage());
        }
    }

    /**
     * @param int $id
     * @return Process
     * @throws NoSuchException
     */
    public function getProcessById(int $id): Process
    {
        $process = $this->repository->getById($id);
        if (!$process) {
            throw new NoSuchException("Process not found.");
        }
        return $process;
    }

    /**
     * @param int $id
     * @throws NoSuchException
     */
    public function deleteProcess(int $id) {
        if (!$this->repository->delete($id)) {
            throw new NoSuchException("Process not found.");
        }
    }

    public function runScheduledProcess(Process $process)
    {
        //1. Get tasks by process conditions
        //2 . Iteration of tasks
            //2.1 Update task status to processing
            //2.2 ProcessTaskJob::dispatch() task

        if (!$process || !$process->is_enabled)
        {
            return;
        }

        $condition = $process->condition;

        // Dynamic Task Selection based on ProcessCondition
        $entityTable = 'tasks';
        if ($condition->entity_type == 'task_group') {
            $entityTable = 'task_groups';
        }

        $tasks = DB::table($entityTable)
            ->where($condition->field_key, $condition->operator, $condition->value)
            ->where('status', 'pending') // Only pick up new tasks
            ->limit($process->limit_tasks)
            ->get();

        foreach ($tasks as $taskData) {
            // TODO remove this
            $task = Task::find($taskData->id);
            $task->update(['status' => 'processing']);

            // Dispatch the job
            TaskProcessJob::dispatch($task, $process)
                ->onQueue('process-' . $process->id)
                //->withChain([]) // Ensure clean chain for failover logic
                ->delay(now());
        }
    }
}
