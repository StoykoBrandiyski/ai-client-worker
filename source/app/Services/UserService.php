<?php

namespace App\Services;

use App\DTOs\UserDTO;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser(UserDTO $dto)
    {
        $data = [
            'username' => $dto->username,
            'email' => $dto->email,
            'password' => Hash::make($dto->password), // Password encryption
            'role_id' => $dto->role_id,
            'is_active' => $dto->is_active,
        ];

        return $this->userRepository->save($data);
    }

    public function updateUser(int $id, array $data)
    {
        $user = $this->userRepository->getById($id);
        
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->userRepository->save($data, $user);
    }

    public function deleteUser(int $id)
    {
        return $this->userRepository->delete($id);
    }
    
    public function getUser(int $id)
    {
        return $this->userRepository->getById($id);
    }
}