<?php

namespace App\Services;

use App\Models\Process;
use App\Models\ProcessModel;
use App\Repositories\Contracts\ProcessRepositoryInterface;
use App\Models\ProcessCondition;
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
                    $condition = \App\Models\ProcessCondition::create($dto->newCondition);
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

    /**
     * Executes process logic based on conditionals (Queue Stub)
     */
    public function executeProcessLogic(int $processId) {
        $process = $this->getProcessById($processId);
        $condition = $process->condition;

        // Example: Query tasks based on dynamic condition operator
        $tasksQuery = Task::where($condition->field_key, $condition->operator, $condition->value)
            ->limit($process->limit_tasks)
            ->orderBy('id', 'asc'); // Usually priority logic goes here

        $tasks = $tasksQuery->get();

        foreach ($tasks as $task) {
            // Dispatch Laravel Job to queue
            // ProcessTaskJob::dispatch($task, $process)->onQueue('high-priority');
        }

        return $tasks->count();
    }
}
