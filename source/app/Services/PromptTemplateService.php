<?php
namespace App\Services;

use App\Repositories\PromptTemplateRepository;
use App\Exceptions\NoSuchException;

class PromptTemplateService {
    private PromptTemplateRepository $repository;

    public function __construct(
        PromptTemplateRepository $repository
    ) {
        $this->repository = $repository;
    }

    public function createTemplateFromRequest(array $data) {
        try {
            // Handle group-on-the-fly logic
            if (!empty($data['new_group_name'])) {
                $group = $this->repository->firstOrCreateGroup(
                    $data['new_group_name'], 
                    $data['new_group_description'] ?? ''
                );
                $data['template_group_id'] = $group->id;
            }

            return $this->repository->save([
                'name' => $data['name'],
                'template_group_id' => $data['template_group_id'],
                'content' => $data['content'],
            ]);
        } catch (\Exception $e) {
            throw new NoSuchException("Failed to create template: " . $e->getMessage());
        }
    }
}