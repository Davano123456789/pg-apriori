<?php

namespace App\Http\Controllers;

use App\Models\AnalysisSession;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTransaksi = Transaction::distinct('invoice_no')->count('invoice_no');
        $totalProduk = Transaction::distinct('item_code')->count('item_code');
        $totalAnalisis = AnalysisSession::count();

        $recentAnalyses = AnalysisSession::withCount('results')
            ->latest()
            ->take(5)
            ->get();

        return view('index', compact('totalTransaksi', 'totalProduk', 'totalAnalisis', 'recentAnalyses'));
    }
}
