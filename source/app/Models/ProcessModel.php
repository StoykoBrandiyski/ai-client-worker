<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessModel extends Model {
    protected $table = 'processes_models';
    protected $fillable = ['process_id', 'model_id', 'sort_order'];

    public function engineModel() {
        return $this->belongsTo(EngineModel::class, 'model_id', 'identifier');
    }
}
