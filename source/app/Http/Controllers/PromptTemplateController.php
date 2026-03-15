<?php
namespace App\Http\Controllers;

use App\Services\PromptTemplateService;
use App\Repositories\PromptTemplateRepository;
use App\Http\Requests\StorePromptTemplateRequest;

class PromptTemplateController extends Controller {
    
    private PromptTemplateService $service;
    private PromptTemplateRepository $repository;

    public function __construct(
        PromptTemplateService $service,
        PromptTemplateRepository $repository
    ) {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function create() {
        $groups = $this->repository->getAllGroups();
        return view('prompts.create', compact('groups'));
    }

    public function store(StorePromptTemplateRequest $request) {
        $this->service->createTemplateFromRequest($request->validated());
        return redirect('/dashboard')->with('success', 'Prompt Template created!');
    }
}