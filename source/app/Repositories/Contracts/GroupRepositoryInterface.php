<?php
namespace App\Repositories\Contracts;

interface GroupRepositoryInterface {
    public function save(array $data, $id = null);
    public function getAll();
    public function getById(int $id);
    public function delete(int $id);
    public function search(array $filters);
}