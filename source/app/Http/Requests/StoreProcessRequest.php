<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProcessRequest extends FormRequest {
    public function authorize() { return true; }

    public function rules() {
        return [
            'name' => 'required|string|max:255',
            'status' => 'required|string',
            'is_enabled' => 'required|boolean',
            'schedule' => 'required|string|max:255',
            'timeout' => 'required|integer|min:1',
            'limit_tasks' => 'required|integer|min:1',

            // Allow EITHER an existing condition_id OR all the new_condition_* fields
            'condition_id' => 'nullable|exists:process_conditions,id|required_without:new_condition_name',
            'new_condition_name' => 'nullable|string|required_without:condition_id',
            'new_condition_entity_type' => 'nullable|string|required_without:condition_id',
            'new_condition_field_key' => 'nullable|string|required_without:condition_id',
            'new_condition_operator' => 'nullable|string|required_without:condition_id',
            'new_condition_value' => 'nullable|string|required_without:condition_id',

            'models' => 'required|array|min:1', // Ensures at least one model is selected
            'models.*' => 'exists:engine_models,identifier'
        ];
    }
}
