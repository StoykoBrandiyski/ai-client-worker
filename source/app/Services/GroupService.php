<?php
namespace App\Services;

use App\Repositories\Contracts\GroupRepositoryInterface;
use App\DTOs\GroupDTO;
use App\Exceptions\NoSuchException;

class GroupService {
    private  GroupRepositoryInterface $repository;
    public function __construct(
        GroupRepositoryInterface $repository
        ) {
            $this->repository = $repository;
        }


    public function listGroups() {
        return $this->repository->getAll();
    }

    public function storeOrUpdate(GroupDTO $dto, $id = null) {
        try {
            return $this->repository->save((array)$dto, $id);
        } catch (\Exception $e) {
            throw new NoSuchException("Database error: " . $e->getMessage());
        }
    }

    public function findGroup(int $id) {
        try {
            return $this->repository->getById($id);
        } catch (\Exception $e) {
            throw new NoSuchException("Group not found.");
        }
    }

    public function removeGroup(int $id) {
        return $this->repository->delete($id);
    }
}