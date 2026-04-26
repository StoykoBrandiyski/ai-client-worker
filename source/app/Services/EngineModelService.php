<?php

namespace App\Services;

use App\Repositories\Contracts\EngineRepositoryInterface;
use Exception;
use App\Models\EngineModel;
use App\Repositories\Contracts\EngineModelRepositoryInterface;
use App\Exceptions\NoSuchException;

class EngineModelService {

    /**
     * EngineModelService constructor.
     * @param EngineModelRepositoryInterface $repository
     * @param EngineRepositoryInterface $engineRepository
     */
    public function __construct(
        private EngineModelRepositoryInterface $repository,
        private EngineRepositoryInterface $engineRepository
    ) {}

    /**
     * @param array $data
     * @param string|null $id
     * @return EngineModel
     * @throws NoSuchException
     */
    public function saveModel(array $data, ?string $id = null): EngineModel
    {
        $engine = $this->engineRepository->getById((int) $data['engine_id']);

        if (!$id) {
            $data['identifier'] = trim(strtolower($engine->name)) . '-' . $data['identifier'];
        }

        try {
            return $this->repository->save($data, $id);
        } catch (Exception $e) {
            throw new NoSuchException("Could not save Engine Model: " . $e->getMessage());
        }
    }

    /**
     * @param string $id
     * @return EngineModel
     * @throws NoSuchException
     */
    public function getModel(string $id): EngineModel
    {
        $model = $this->repository->getById($id);
        if (!$model) {
            throw new NoSuchException("Engine Model not found.");
        }
        return $model;
    }

    /**
     * @param string $id
     * @throws NoSuchException
     */
    public function deleteModel(string $id) {
        if (!$this->repository->delete($id)) {
            throw new NoSuchException("Engine Model not found for deletion.");
        }
    }
}
