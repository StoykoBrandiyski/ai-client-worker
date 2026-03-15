<?php

namespace App\DTOs;

class UserDTO
{
public string $username;
    public string $email;
    public string $password;
    public ?int $role_id;
    public ?int $is_active;

    public function __construct(
        string $username,
        string $email,
        string $password,
        ?int $role_id = 1,
        ?int $is_active = 0
    ) {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->role_id = $role_id;
        $this->is_active = $is_active;
    }
}