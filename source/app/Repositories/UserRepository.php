<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Exceptions\NoSuchException;
use Illuminate\Database\QueryException;

class UserRepository implements UserRepositoryInterface
{
    public function save(array $data, ?User $user = null): User
    {
        try {
            if ($user) {
                $user->update($data);
                return $user;
            }
            return User::create($data);
        } catch (QueryException $e) {
            throw new NoSuchException("Database error occurred while saving user: " . $e->getMessage());
        }
    }

    public function getById(int $id): User
    {
        $user = User::find($id);
        if (!$user) {
            throw new NoSuchException("User with ID {$id} not found.");
        }
        return $user;
    }

    public function delete(int $id): bool
    {
        $user = $this->getById($id);
        return $user->delete();
    }
}