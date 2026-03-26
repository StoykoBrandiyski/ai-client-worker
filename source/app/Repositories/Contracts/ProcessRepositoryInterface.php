<?php

namespace App\Repositories\Contracts;

use App\Exceptions\NoSuchException;
use App\Models\Process;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProcessRepositoryInterface {
    /**
     * @param array $data
     * @param int|null $id
     * @return Process
     */
    public function save(array $data, ?int $id = null): Process;

    /**
     * @param array|string[] $fields
     * @return Collection
     */
    public function getAll(array $fields = ['*']): Collection;

    /**
     * @param int $processId
     * @return LengthAwarePaginator
     */
    public function getModelsByProcessId(int $processId): LengthAwarePaginator;

    /**
     * @param array $whereFields
     * @return Collection
     */
    public function getAllByFields(array $whereFields = []): Collection;

    /**
     * @param int $id
     * @return Process
     * @throws NoSuchException
     */
    public function getById(int $id): Process;

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function search(array $filters): LengthAwarePaginator;
}
