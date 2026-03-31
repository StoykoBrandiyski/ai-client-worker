<?php

namespace App\Http\Controllers;

use App\Exceptions\NoSuchException;
use App\Repositories\Contracts\EngineModelRepositoryInterface;
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
        try {
            $process = $this->service->getProcessById($id);
        } catch (NoSuchException $e) {
            return view('processes.index')->with('error', $e->getMessage());
        }
        $conditions = ProcessCondition::all();
        $allModels = $this->engineModelRepository->getAll();
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

        try {
            $this->service->saveProcess($dto, $val['models']);
        } catch (NoSuchException $e) {
            return redirect('/processes')->with('error', $e->getMessage());
        }
        return redirect('/processes')->with('success', 'Process saved successfully.');
    }

    public function getById($id) {
        try {
            $process = $this->service->getProcessById($id);
        } catch (NoSuchException $e) {
            return redirect('/processes')->with('error', $e->getMessage());
        }
        return view('processes.show', compact('process'));
    }

    public function destroy(Request $request) {
        try {
            $this->service->deleteProcess($request->input('id'));
        } catch (NoSuchException $e) {
            return redirect('/processes')->with('error', $e->getMessage());
        }
        return redirect('/processes')->with('success', 'Process deleted.');
    }
}
