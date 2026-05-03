<?php

namespace App\Http\Controllers;

use App\Models\AnalysisSession;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        $sessions = AnalysisSession::withCount('results')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('history.index', compact('sessions'));
    }

    public function show($id)
    {
        $session = AnalysisSession::with(['results', 'transformations', 'steps.itemsets'])->findOrFail($id);
        return view('history.show', compact('session'));
    }

    public function destroy($id)
    {
        $session = AnalysisSession::findOrFail($id);
        $session->delete(); // This will also delete related results if cascading is set up, 
                           // but let's be safe if not using cascades.
        
        return redirect()->route('history.index')->with('success', 'Riwayat analisis berhasil dihapus.');
    }
}
