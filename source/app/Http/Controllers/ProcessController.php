<?php

namespace App\Http\Controllers;

use App\Jobs\TasksProcessJob;
use App\Repositories\Contracts\EngineModelRepositoryInterface;
use App\Repositories\EngineModelRepository;
use App\Services\ProcessService;
use App\Repositories\Contracts\ProcessRepositoryInterface;
use App\Models\ProcessCondition;
use App\Http\Requests\StoreProcessRequest;
use App\DTOs\ProcessDTO;
use Illuminate\Http\Request;

class ProcessController extends Controller {

    /**
     * ProcessController constructor.
     * @param ProcessService $service
     * @param ProcessRepositoryInterface $repository
     * @param EngineModelRepositoryInterface $engineModelRepository
     */
    public function __construct(
        private ProcessService $service,
        private ProcessRepositoryInterface $repository,
        private EngineModelRepositoryInterface $engineModelRepository
    ) {}

    public function getAll(Request $request) {
        $processes = $this->repository->search($request->all());
        return view('processes.index', compact('processes'));
    }

    public function create() {
        $conditions = ProcessCondition::all();
        // Change engine id to be dynamic
        $allModels = $this->engineModelRepository->getAllByEngineId(1);
        return view('processes.form', compact('conditions', 'allModels'));
    }

    public function edit($id) {
        $process = $this->service->getProcessById($id);
        $conditions = ProcessCondition::all();
        $allModels = $this->engineModelRepository->getAllByEngineId(2);
        // Transform existing models into a JS-friendly format for Alpine
        $selectedModels = $process->models->map(function($pm) {
            return [
                'identifier' => $pm->model_id,
                'name' => $pm->engineModel->name ?? $pm->model_id
            ];
        });

        return view('processes.form', compact('process', 'conditions', 'allModels','selectedModels'));
    }

    public function store(StoreProcessRequest $request) {
        $val = $request->validated();

        $newCondition = null;
        if (empty($val['condition_id']) && !empty($val['new_condition_name'])) {
            $newCondition = [
                'name' => $val['new_condition_name'],
                'entity_type' => $val['new_condition_entity_type'],
                'field_key' => $val['new_condition_field_key'],
                'operator' => $val['new_condition_operator'],
                'value' => $val['new_condition_value']
            ];
        }

        $dto = new ProcessDTO(
            name: $val['name'],
            status: 'new',//$val['status'],
            isEnabled: (int)$val['is_enabled'],
            schedule: $val['schedule'],
            timeout: (int)$val['timeout'],
            limitTasks: (int)$val['limit_tasks'],
            conditionId: $val['condition_id'] ?? null,
            newCondition: $newCondition,
            id: $request->input('id')
        );

        $this->service->saveProcess($dto, $val['models']);
        return redirect('/processes')->with('success', 'Process saved successfully.');
    }

    public function getById($id) {
        $process = $this->service->getProcessById($id);
        return view('processes.show', compact('process'));
    }

    public function destroy(Request $request) {
        $this->service->deleteProcess($request->input('id'));
        return redirect('/processes')->with('success', 'Process deleted.');
    }
}
