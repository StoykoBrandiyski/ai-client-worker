<?php

namespace App\Http\Controllers;

use App\Exceptions\NoSuchException;
use App\Services\EngineService;
use App\Repositories\Contracts\EngineRepositoryInterface;
use App\Http\Requests\StoreEngineRequest;
use App\DTOs\EngineDTO;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class EngineController extends Controller {
    public function __construct(
        protected EngineService $service,
        protected EngineRepositoryInterface $repository
    ) {}

    /**
     * @param Request $request
     * @return View|Factory
     */
    public function getAll(Request $request): View|Factory
    {
        if ($request->has('name') || $request->has('sort')) {
            $engines = $this->repository->search($request->all());
        } else {
            $engines = $this->repository->getAll();
        }
        return view('engines.index', compact('engines'));
    }

    /**
     * @return View|Factory
     */
    public function create(): View|Factory
    {
        return view('engines.form');
    }

    /**
     * @param StoreEngineRequest $request
     * @return Redirector|RedirectResponse
     * @throws NoSuchException
     */
    public function store(StoreEngineRequest $request): Redirector|RedirectResponse
    {
        $dto = EngineDTO::fromRequest($request->validated(), $request->input('id'));
        $this->service->saveEngine($dto);
        return redirect('/engines')->with('success', 'Engine saved successfully.');
    }

    /**
     * @param $id
     * @return View|Factory
     * @throws NoSuchException
     */
    public function getById($id): View|Factory
    {
        $engine = $this->service->getEngineById($id);
        return view('engines.show', compact('engine'));
    }

    /**
     * @param Request $request
     * @return Redirector|RedirectResponse
     * @throws NoSuchException
     */
    public function destroy(Request $request): Redirector|RedirectResponse
    {
        $this->service->deleteEngine($request->input('id'));
        return redirect('/engines')->with('success', 'Engine deleted.');
    }
}
