<?php

namespace App\Services\Engine;

use App\Models\EngineModel;
use App\Models\Task;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Client\RequestException;

interface EngineProviderInterface
{
    /**
     * @param Task $task
     * @param EngineModel $engineModel
     * @return array
     * @throws AuthenticationException
     * @throws RequestException
     */
    public function run(Task $task, EngineModel $engineModel): array;
}
