<?php
namespace App\Repositories;

use App\Models\PromptTemplate;
use App\Models\TemplateGroup;

class PromptTemplateRepository {
    public function save(array $data) {
        return PromptTemplate::create($data);
    }

    public function firstOrCreateGroup(string $name, string $description = '') {
        return TemplateGroup::firstOrCreate(
            ['name' => $name],
            ['description' => $description ?: 'Auto-generated group']
        );
    }

    public function getAllGroups() {
        return TemplateGroup::all();
    }
}