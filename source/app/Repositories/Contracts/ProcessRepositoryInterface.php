<?php

namespace App\Repositories\Contracts;

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
     * @param int $id
     * @return Process
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
