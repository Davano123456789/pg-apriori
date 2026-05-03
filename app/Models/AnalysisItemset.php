<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalysisItemset extends Model
{
    protected $fillable = ['step_id', 'items', 'count', 'support', 'is_frequent', 'type'];

    protected $casts = [
        'is_frequent' => 'boolean',
    ];

    public function step()
    {
        return $this->belongsTo(AnalysisStep::class, 'step_id');
    }
}
