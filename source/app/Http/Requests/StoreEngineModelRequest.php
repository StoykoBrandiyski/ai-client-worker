<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEngineModelRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'identifier' => 'required|string|unique:engine_models,identifier,' . $this->identifier . ',identifier',
            'name' => 'required|string|min:1',
            'engine_id' => 'required|exists:engines,id',
            'url' => 'nullable',
            'initial_prompt' => 'nullable|string',
            'use_chat' => 'required|integer|in:0,1'
        ];
    }
}
