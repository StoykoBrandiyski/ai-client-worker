<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model {
    protected $fillable = [
        'name', 'request_content', 'response_content', 'user_id',
        'prompt_template_id', 'parent_id', 'group_id', 'status', 'executed_count'
    ];

    public function group() { return $this->belongsTo(Group::class, 'group_id'); }
    public function images() { return $this->hasMany(TaskImage::class, 'task_id'); }
    public function promptTemplate() { return $this->belongsTo(PromptTemplate::class); }

    public function parent(): BelongsTo {
        return $this->belongsTo(self::class, 'parent_id');
    }

    // Relationship to all "Replies" belonging to this task
    public function children(): HasMany {
        return $this->hasMany(self::class, 'parent_id');
    }
}
