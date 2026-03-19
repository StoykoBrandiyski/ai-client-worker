<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEngineRequest extends FormRequest {
    public function authorize() { return true; }

    public function rules() {
        return [
            'name' => 'required|string|max:255',
            'base_url' => 'required|url|max:255',
            'auth_token' => 'nullable|string|max:255',
            'max_tasks_count' => 'required|integer|min:0',
            'task_timeout' => 'required|integer|min:0',
        ];
    }
}
