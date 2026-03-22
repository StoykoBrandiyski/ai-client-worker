<?php


namespace App\DTOs;


class ProcessModelDTO
{
    public function __construct(
        public  ?int $process_id = null,
        public  int $model_id,
        public  int $sort_order,
    ) {}
}
