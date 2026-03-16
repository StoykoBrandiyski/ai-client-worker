<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class TaskImage extends Model {
    protected $table = 'uploaded_task_images';
    protected $fillable = ['path', 'task_id'];
}