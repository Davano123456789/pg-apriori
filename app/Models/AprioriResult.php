<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AprioriResult extends Model
{
    protected $fillable = ['session_id', 'antecedent', 'consequent', 'support', 'confidence'];

    public function session()
    {
        return $this->belongsTo(AnalysisSession::class, 'session_id');
    }
}
