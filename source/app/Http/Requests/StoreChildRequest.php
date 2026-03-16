<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChildRequest extends FormRequest {
    public function rules() {
        return [
            'parent_id' => 'required|numeric|min:1',
            'request_content' => 'required|string',
        ];
    }
}