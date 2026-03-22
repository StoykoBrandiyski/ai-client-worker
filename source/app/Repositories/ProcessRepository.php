<?php

namespace App\Repositories;

use App\Exceptions\NoSuchException;
use App\Models\Process;
use App\Models\ProcessModel;
use App\Repositories\Contracts\ProcessRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ProcessRepository implements ProcessRepositoryInterface {
    /**
     * @param array $data
     * @param int|null $id
     * @return Process
     */
    public function save(array $data, ?int $id = null): Process {
        $process = Process::updateOrCreate(['id' => $id], $data);
        $this->clearCache($process->id);
        return $process;
    }

    /**
     * @param array|string[] $fields
     * @return Collection
     */
    public function getAll(array $fields = ['*']): Collection {
        return Process::select($fields)->get();
    }

    /**
     * @param int $processId
     * @return LengthAwarePaginator
     */
    public function getModelsByProcessId(int $processId): LengthAwarePaginator {
        return ProcessModel::with('engineModel')->where('process_id', $processId)->orderBy('sort_order')->get();
    }

    /**
     * @param int $id
     * @return Process
     */
    public function getById(int $id): Process {
        return Cache::remember("process_{$id}", 3600, function() use ($id) {
            $process = Process::with(['condition', 'models.engineModel'])->find($id);

            if (!$process) {
                throw new NoSuchException("The process is not found.");
            }

            return $process;
        });
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool {
        $process = Process::find($id);

        if ($process) {
            $process->delete();
            $this->clearCache($id);
            return true;
        }
        return false;
    }

    /**
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function search(array $filters): LengthAwarePaginator {
        $query = Process::with('condition');

        if (!empty($filters['name'])) {
            $query->where('name', 'like', "%{$filters['name']}%");
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $sort = $filters['sort'] ?? 'name';
        $direction = $filters['direction'] ?? 'asc';

        return $query->orderBy($sort, $direction)->paginate(15)->withQueryString();
    }

    /**
     * @param int $id
     */
    private function clearCache(int $id) {
        Cache::forget("process_{$id}");
    }
}
