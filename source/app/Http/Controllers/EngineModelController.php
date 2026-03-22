<?php

namespace App\Http\Controllers;

use App\Exceptions\NoSuchException;
use App\Repositories\Contracts\EngineRepositoryInterface;
use App\Services\EngineModelService;
use App\Repositories\Contracts\EngineModelRepositoryInterface;
use App\Http\Requests\StoreEngineModelRequest;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class EngineModelController extends Controller {
    /**
     * EngineModelController constructor.
     * @param EngineModelService $service
     * @param EngineModelRepositoryInterface $repository
     * @param EngineRepositoryInterface $engineRepository
     */
    public function __construct(
        private EngineModelService $service,
        private EngineModelRepositoryInterface $repository,
        private EngineRepositoryInterface $engineRepository
    ) {}

    /**
     * @param Request $request
     * @return View|Factory
     */
    public function getList(Request $request): View|Factory
    {
        $models = $this->repository->search($request->all());
        return view('engine_models.index', compact('models'));
    }

    /**
     * Show form for creating a new model.
     */
    public function create(): View|Factory
    {
        // We need all engines to populate the 'Provider Engine' select box
        $engines = $this->engineRepository->getAll();

        return view('engine_models.storeModel', compact('engines'));
    }

    /**
     * Show form for editing an existing model.
     * @param string $id
     * @return View|Factory
     * @throws NoSuchException
     */
    public function edit(string $id): View|Factory
    {
        $model = $this->service->getModel($id);
        $engines = $this->engineRepository->getAll();

        return view('engine_models.storeModel', compact('model', 'engines'));
    }

    /**
     * @param StoreEngineModelRequest $request
     * @return Redirector|RedirectResponse
     * @throws NoSuchException
     */
    public function store(StoreEngineModelRequest $request): Redirector|RedirectResponse
    {
        $this->service->saveModel($request->validated(), $request->input('id'));
        return redirect('/engine/models')->with('success', 'Model Saved');
    }

    /**
     * @param $id
     * @return View|Factory
     * @throws NoSuchException
     */
    public function getById($id): View|Factory
    {
        $model = $this->service->getModel($id);
        return view('engine_models.show', compact('model'));
    }

    /**
     * @param Request $request
     * @return Redirector|RedirectResponse
     * @throws NoSuchException
     */
    public function destroy(Request $request): Redirector|RedirectResponse
    {
        $this->service->deleteModel($request->input('id'));
        return redirect('/engine/models')->with('success', 'Model Deleted');
    }
}
