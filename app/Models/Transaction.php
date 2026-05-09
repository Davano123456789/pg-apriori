<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['invoice_no', 'customer_code', 'customer_name', 'item_code', 'item_name', 'quantity', 'transaction_date'];

    public function analysisSessions()
    {
        return $this->belongsToMany(AnalysisSession::class, 'analysis_details', 'transaction_id', 'session_id')->withTimestamps();
    }
}
