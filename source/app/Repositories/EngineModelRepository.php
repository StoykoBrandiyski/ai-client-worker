<?php

namespace App\Repositories;

use App\Exceptions\NoSuchException;
use App\Models\EngineModel;
use App\Repositories\Contracts\EngineModelRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class EngineModelRepository implements EngineModelRepositoryInterface {

    /**
     * @param array $data
     * @param string|null $id
     * @return EngineModel
     */
    public function save(array $data, ?string $id = null): EngineModel {
        $identifier = $id ?? $data['identifier'];
        $model = EngineModel::updateOrCreate(['identifier' => $identifier], $data);
        $this->clearCache($model->engine_id);
        return $model;
    }

    /**
     * @param int $engineId
     * @return Collection
     */
    public function getAllByEngineId(int $engineId): Collection {
        return Cache::remember("engine_models_list_{$engineId}", 3600, function() use ($engineId) {
            return EngineModel::where('engine_id', $engineId)->get();
        });
    }

    /**
     * @param string $id
     * @return EngineModel
     * @throws NoSuchException
     */
    public function getById(string $id): EngineModel {
        $engineModel = EngineModel::with('engine')->find($id);

        if (!$engineModel) {
            throw new NoSuchException("Engine Model not found");
        }

        return $engineModel;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool {
        $model = EngineModel::find($id);
        if ($model) {
            $engineId = $model->engine_id;
            $model->delete();
            $this->clearCache($engineId);
            return true;
        }

        return false;
    }

    /**
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function search(array $filters): LengthAwarePaginator {
        $query = EngineModel::with('engine');
        if (!empty($filters['name'])) {
            $query->where('name', 'like', "%{$filters['name']}%");
        }
        if (!empty($filters['engine_id'])) {
            $query->where('engine_id', $filters['engine_id']);
        }

        $sort = $filters['sort'] ?? 'name';
        $direction = $filters['direction'] ?? 'asc';

        return $query->orderBy($sort, $direction)->paginate(15);
    }

    /**
     * @param int $engineId
     */
    private function clearCache(int $engineId) {
        Cache::forget("engine_models_list_{$engineId}");
    }

    /**
     * @return Collection
     */
    public function getAll(): Collection
    {
        return EngineModel::all(['identifier', 'name']);
    }
}
