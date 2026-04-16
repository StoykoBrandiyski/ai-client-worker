<?php


namespace App\Repositories\Contracts;


use Illuminate\Pagination\LengthAwarePaginator;
use InvalidArgumentException;

interface ProcessLogRepositoryInterface {

    /**
     * @param array $data
     * @return mixed
     */
    public function createLog(array $data);

    /**
     * @param int $limit
     * @return LengthAwarePaginator|mixed
     * @throws InvalidArgumentException
     */
    public function getLatestLogs(int $limit = 50);
}
