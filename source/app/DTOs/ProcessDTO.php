<?php

namespace App\DTOs;

class ProcessDTO {
    public function __construct(
        public  string $name,
        public  string $status,
        public  int $isEnabled,
        public  string $schedule,
        public  int $timeout,
        public  int $limitTasks,
        public  ?int $conditionId = null,
        public  ?array $newCondition = null,
        public  ?int $id = null
    ) {}
}
