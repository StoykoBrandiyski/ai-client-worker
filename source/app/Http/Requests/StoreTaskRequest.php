<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest {
    public function rules() {
        return [
            'name' => 'required|string|max:255',
            'request_content' => 'required|string',
            'group_id' => 'required|numeric|min:1',
            //'status' => 'required|string',
            'executed_count' => 'required|numeric|min:1',
            'sort_order' => 'required|numeric|min:0'
        ];
    }
}