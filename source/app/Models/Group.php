<?php
namespace App\Models;

use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Group extends Model {
    protected $table = 'task_groups';
    protected $fillable = ['name', 'description', 'parent_id'];

    public function tasks(): HasMany {
        return $this->hasMany(Task::class, 'group_id');
    }

    // Relation for the "Last 3 tasks" requirement
    public function latestThreeTasks(): HasMany {
        return $this->hasMany(Task::class, 'group_id')->latest()->limit(3);
    }

    public function parent(): BelongsTo {
        return $this->belongsTo(Group::class, 'parent_id');
    }
}