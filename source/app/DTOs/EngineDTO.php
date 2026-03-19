<?php

namespace App\DTOs;

class EngineDTO
{
    public string $name;
    public string $baseUrl;
    public ?string $authToken;
    public int $maxTasksCount;
    public int $taskTimeout;
    public ?int $id;

    /**
     * EngineDTO constructor.
     *
     * @param string $name
     * @param string $baseUrl
     * @param string|null $authToken
     * @param int $maxTasksCount
     * @param int $taskTimeout
     * @param int|null $id
     */
    public function __construct(
        string $name,
        string $baseUrl,
        ?string $authToken,
        int $maxTasksCount,
        int $taskTimeout,
        ?int $id = null
    ) {
        $this->name = $name;
        $this->baseUrl = $baseUrl;
        $this->authToken = $authToken;
        $this->maxTasksCount = $maxTasksCount;
        $this->taskTimeout = $taskTimeout;
        $this->id = $id;
    }

    /**
     * @param array $data
     * @param int|null $id
     * @return static
     */
    public static function fromRequest(array $data, ?int $id = null): self
    {
        return new self(
            name: $data['name'],
            baseUrl: $data['base_url'],
            authToken: $data['auth_token'] ?? null,
            maxTasksCount: $data['max_tasks_count'] ?? 0,
            taskTimeout: $data['task_timeout'] ?? 0,
            id: $id
        );

    }
}
