<?php

namespace App\Services;

use App\Models\Engine;
use App\Repositories\Contracts\EngineRepositoryInterface;
use App\DTOs\EngineDTO;
use App\Exceptions\NoSuchException;

class EngineService {
    public function __construct(
        protected EngineRepositoryInterface $repository
    ) {

    }

    /**
     * @param EngineDTO $dto
     * @return Engine
     * @throws NoSuchException
     */
    public function saveEngine(EngineDTO $dto): Engine
    {
        try {
            $data = [
                'name' => $dto->name,
                'base_url' => $dto->baseUrl,
                'max_tasks_count' => $dto->maxTasksCount,
                'task_timeout' => $dto->taskTimeout,
            ];

            // SHA-256 Hash token if it is provided
            if ($dto->authToken) {
                $data['auth_token'] = base64_encode($dto->authToken);
            }

            return $this->repository->save($data, $dto->id);
        } catch (\Exception $e) {
            throw new NoSuchException("Database error occurred while saving the Engine: " . $e->getMessage());
        }
    }

    /**
     * @param int $id
     * @return Engine
     * @throws NoSuchException
     */
    public function getEngineById(int $id): Engine
    {
        $engine = $this->repository->getById($id);
        if (!$engine) {
            throw new NoSuchException("Engine with ID {$id} not found.");
        }
        return $engine;
    }

    /**
     * @param int $id
     * @return bool
     * @throws NoSuchException
     */
    public function deleteEngine(int $id): bool
    {
        if (!$this->repository->delete($id)) {
            throw new NoSuchException("Failed to delete. Engine with ID {$id} not found.");
        }

        return true;
    }
}
