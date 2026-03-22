<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EngineModel extends Model {
    protected $primaryKey = 'identifier';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'identifier', 'name', 'engine_id', 'url', 'initial_prompt', 'use_chat'
    ];

    public function engine(): BelongsTo {
        return $this->belongsTo(Engine::class, 'engine_id');
    }
}
