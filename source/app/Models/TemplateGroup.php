<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateGroup extends Model {
    protected $fillable = ['name', 'description'];

    public function promptTemplates() {
        return $this->hasMany(PromptTemplate::class, 'template_group_id');
    }
}