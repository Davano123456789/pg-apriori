<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalysisStep extends Model
{
    protected $fillable = ['session_id', 'k'];

    public function session()
    {
        return $this->belongsTo(AnalysisSession::class, 'session_id');
    }

    public function itemsets()
    {
        return $this->hasMany(AnalysisItemset::class, 'step_id');
    }

    public function candidates()
    {
        return $this->hasMany(AnalysisItemset::class, 'step_id')->where('type', 'candidate');
    }

    public function frequent()
    {
        return $this->hasMany(AnalysisItemset::class, 'step_id')->where('type', 'frequent');
    }
}
