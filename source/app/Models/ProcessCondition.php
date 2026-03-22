<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessCondition extends Model {
    protected $fillable = ['name', 'entity_type', 'field_key', 'operator', 'value'];
}
