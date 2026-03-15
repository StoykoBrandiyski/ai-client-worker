<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromptTemplate extends Model {
    protected $fillable = ['name', 'template_group_id', 'content'];

    public function group() {
        return $this->belongsTo(TemplateGroup::class, 'template_group_id');
    }
}