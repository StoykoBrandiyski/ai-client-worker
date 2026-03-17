<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model {
    protected $fillable = [
        'name', 'request_content', 'response_content', 'user_id', 
        'prompt_template_id', 'parent_id', 'group_id', 'status', 'executed_count'
    ];

    public function group() { return $this->belongsTo(Group::class, 'group_id'); }
    public function images() { return $this->hasMany(TaskImage::class, 'task_id'); }
    public function promptTemplate() { return $this->belongsTo(PromptTemplate::class); }
}

// app/Models/TaskImage.php
class TaskImage extends Model {
    protected $table = 'uploaded_task_images';
    protected $fillable = ['path', 'task_id'];
}