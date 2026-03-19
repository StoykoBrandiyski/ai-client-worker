<?php

namespace App\Repositories\Contracts;

use App\Models\Engine;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EngineRepositoryInterface {
    /**
     * @param array $data
     * @param int|null $id
     * @return Engine
     */
    public function save(array $data, ?int $id = null): Engine;

    /**
     * @return LengthAwarePaginator
     */
    public function getAll(): LengthAwarePaginator ;

    /**
     * @param int $id
     * @return Engine
     */
    public function getById(int $id): Engine;

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
