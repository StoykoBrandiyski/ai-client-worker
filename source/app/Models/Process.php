<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Process extends Model {
    protected $fillable = [
        'name', 'status', 'is_enabled', 'condition_id', 'schedule', 'timeout', 'limit_tasks'
    ];

    public function condition() {
        return $this->belongsTo(ProcessCondition::class, 'condition_id');
    }

    public function models() {
        return $this->hasMany(ProcessModel::class, 'process_id')->orderBy('sort_order');
    }
}
