<?php

namespace App\Repositories;

use App\Models\ProcessLog;
use App\Repositories\Contracts\ProcessLogRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use InvalidArgumentException;

class ProcessLogRepository implements ProcessLogRepositoryInterface {

    /**
     * @param array $data
     * @return mixed
     */
    public function createLog(array $data) {
        $data['process_message'] = substr( $data['process_message'], 0, 255);
        return ProcessLog::create($data);
    }

    /**
     * @param int $limit
     * @return LengthAwarePaginator|mixed
     * @throws InvalidArgumentException
     */
    public function getLatestLogs($limit = 50) {
        return ProcessLog::with(['process', 'engine'])->latest()->paginate($limit);
    }
}
