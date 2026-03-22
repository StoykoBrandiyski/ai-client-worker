<?php

namespace App\Repositories\Contracts;

use App\Exceptions\NoSuchException;
use App\Models\EngineModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EngineModelRepositoryInterface
{
    /**
     * @param array $data
     * @param string|null $id
     * @return EngineModel
     */
    public function save(array $data, ?string $id = null): EngineModel;

    /**
     * @param int $engineId
     * @return Collection
     */
    public function getAllByEngineId(int $engineId): Collection;

    /**
     * @param string $id
     * @return EngineModel
     * @throws NoSuchException
     */
    public function getById(string $id): EngineModel;

    /**
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool;

    /**
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function search(array $filters): LengthAwarePaginator;

}
