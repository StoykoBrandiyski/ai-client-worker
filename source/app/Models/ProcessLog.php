<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessLog extends Model
{
    protected $fillable = ['process_id', 'engine_id', 'engine_model_identifier', 'status', 'started_at', 'finished_at', 'process_message', 'task_id'];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    /**
     * @return BelongsTo
     */
    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    /**
     * @return BelongsTo
     */
    public function engine(): BelongsTo
    {
        return $this->belongsTo(Engine::class);
    }

    /**
     * @return BelongsTo
     */
    public function engineModel(): BelongsTo
    {
        return $this->belongsTo(EngineModel::class);
    }
}
