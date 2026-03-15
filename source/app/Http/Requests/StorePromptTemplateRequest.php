<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePromptTemplateRequest extends FormRequest {
    public function rules() {
        return [
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'template_group_id' => 'required_without:new_group_name|nullable|exists:template_groups,id',
            'new_group_name' => 'nullable|string|max:255',
        ];
    }
}