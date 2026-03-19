<?php

namespace App\Repositories;

use App\Models\Engine;
use App\Repositories\Contracts\EngineRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EngineRepository implements EngineRepositoryInterface {

    public function getAll(): LengthAwarePaginator {
        return Cache::remember('engines_all', 3600, function() {
            return Engine::orderBy('id', 'desc')->paginate(15);
        });
    }

    public function getById(int $id): Engine {
        return Engine::find($id);
    }

    public function search(array $filters): LengthAwarePaginator {
        $query = Engine::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        // Sorting logic (if 'sort' and 'direction' are passed)
        $sort = $filters['sort'] ?? 'id';
        $direction = $filters['direction'] ?? 'desc';
        $query->orderBy($sort, $direction);

        return $query->paginate(15)->withQueryString();
    }

    public function save(array $data, ?int $id = null): Engine {
        $engine = Engine::updateOrCreate(['id' => $id], $data);
        $this->clearCache();
        return $engine;
    }

    public function delete(int $id): bool {
        $engine = Engine::find($id);
        if ($engine) {
            $engine->delete();
            $this->clearCache();
            return true;
        }
        return false;
    }

    private function clearCache() {
        Cache::forget('engines_all');
    }
}
