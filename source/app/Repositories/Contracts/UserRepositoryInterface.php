<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function save(array $data, ?User $user = null): User;
    public function getById(int $id): User;
    public function delete(int $id): bool;
}