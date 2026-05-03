<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalysisTransformation extends Model
{
    protected $fillable = ['session_id', 'invoice_no', 'items'];

    public function session()
    {
        return $this->belongsTo(AnalysisSession::class, 'session_id');
    }
}
