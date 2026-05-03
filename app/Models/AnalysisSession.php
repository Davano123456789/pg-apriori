<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalysisSession extends Model
{
    protected $fillable = ['name', 'min_support', 'min_confidence', 'start_date', 'end_date', 'total_transactions'];

    public function results()
    {
        return $this->hasMany(AprioriResult::class, 'session_id');
    }

    public function transformations()
    {
        return $this->hasMany(AnalysisTransformation::class, 'session_id');
    }

    public function steps()
    {
        return $this->hasMany(AnalysisStep::class, 'session_id');
    }
}
