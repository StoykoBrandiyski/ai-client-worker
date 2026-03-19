<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Engine extends Model {
    protected $fillable = [
        'name', 'base_url', 'auth_token', 'max_tasks_count', 'task_timeout'
    ];

    protected $hidden = ['auth_token'];
}
