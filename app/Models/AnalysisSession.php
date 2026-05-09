<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalysisSession extends Model
{
    protected $fillable = ['user_id', 'min_support', 'min_confidence', 'start_date', 'end_date', 'total_transactions'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

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

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, 'analysis_details', 'session_id', 'transaction_id')->withTimestamps();
    }
}
