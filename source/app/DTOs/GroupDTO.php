<?php
namespace App\DTOs;

class GroupDTO {
    public string $name;
    public string $description;
    public ?int $parent_id = null;

    public function __construct(
        string $name,
        string $description,
        ?int $parent_id = null
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->parent_id = $parent_id;
    }

    public static function fromRequest($request): self {
        return new self(
            $request->name,
            $request->description,
            $request->parent_id
        );
    }
}