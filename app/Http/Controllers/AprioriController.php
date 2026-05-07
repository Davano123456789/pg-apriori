<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\AnalysisSession;
use App\Models\AprioriResult;
use App\Models\AnalysisTransformation;
use App\Models\AnalysisStep;
use App\Models\AnalysisItemset;
use App\Services\AprioriService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AprioriController extends Controller
{
    public function index()
    {
        $minDate = Transaction::min('transaction_date');
        $maxDate = Transaction::max('transaction_date');

        return view('apriori.index', [
            'min_db_date' => $minDate ? Carbon::parse($minDate)->format('Y-m-d') : null,
            'max_db_date' => $maxDate ? Carbon::parse($maxDate)->format('Y-m-d') : null,
        ]);
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'min_support' => 'required|numeric|min:0|max:100',
            'min_confidence' => 'required|numeric|min:0|max:100',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $minSupport = $request->min_support;
        $minConfidence = $request->min_confidence;

        // 1. Get transactions grouped by invoice - STRICT DATE FILTER
        $rawTransactions = Transaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->get();

        if ($rawTransactions->isEmpty()) {
            return back()->with('error', 'Tidak ada data transaksi pada rentang tanggal tersebut.');
        }

        // Mapping code to names
        $itemNames = $rawTransactions->pluck('item_name', 'item_code')->toArray();

        // Transformation Data (All for pagination/full view)
        $groupedTransactions = [];
        foreach ($rawTransactions->groupBy('invoice_no') as $invoiceNo => $rows) {
            $groupedTransactions[$invoiceNo] = $rows->pluck('item_code')->unique()->toArray();
        }

        // Prepare Top 10 items for Tabular Matrix Header - ALSO FILTER BY DATE
        $topItemsForMatrix = Transaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->select('item_code', DB::raw('count(distinct invoice_no) as count'))
            ->groupBy('item_code')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->pluck('item_code')
            ->toArray();
        sort($topItemsForMatrix);

        // 2. Run Apriori Service
        $apriori = new AprioriService(array_values($groupedTransactions), $minSupport, $minConfidence, $itemNames);
        $results = $apriori->process();

        // 3. Pass everything to view
        $minDate = Transaction::min('transaction_date');
        $maxDate = Transaction::max('transaction_date');

        return view('apriori.index', [
            'results' => $results,
            'params' => $request->all(),
            'transformation' => $groupedTransactions, // Full data
            'matrix_items' => $topItemsForMatrix,
            'total_invoices' => count($groupedTransactions),
            'step_by_step' => $results['step_by_step'],
            'min_db_date' => $minDate ? Carbon::parse($minDate)->format('Y-m-d') : null,
            'max_db_date' => $maxDate ? Carbon::parse($maxDate)->format('Y-m-d') : null,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required',
            'end_date' => 'required',
            'min_support' => 'required',
            'min_confidence' => 'required',
            'results' => 'required|json',
            'step_by_step_data' => 'nullable|json',
            'transformation_data' => 'nullable|json'
        ]);

        $results = json_decode($request->results, true);

        if (empty($results)) {
            return back()->with('error', 'Tidak ada aturan yang bisa disimpan.');
        }

        DB::transaction(function () use ($request, $results) {
            $session = AnalysisSession::create([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'min_support' => $request->min_support,
                'min_confidence' => $request->min_confidence,
                'total_transactions' => $request->total_transactions ?? 0,
            ]);

            // Save Transformations
            $transformationData = json_decode($request->transformation_data, true);
            if ($transformationData) {
                foreach ($transformationData as $invoiceNo => $items) {
                    AnalysisTransformation::create([
                        'session_id' => $session->id,
                        'invoice_no' => $invoiceNo,
                        'items' => implode(', ', $items)
                    ]);
                }
            }

            // Save Steps and Itemsets
            $stepByStepData = json_decode($request->step_by_step_data, true);
            if ($stepByStepData) {
                foreach ($stepByStepData as $k => $data) {
                    $step = AnalysisStep::create([
                        'session_id' => $session->id,
                        'k' => $k
                    ]);

                    // Save Candidates
                    if (isset($data['candidates'])) {
                        foreach ($data['candidates'] as $candidate) {
                            AnalysisItemset::create([
                                'step_id' => $step->id,
                                'items' => implode(', ', $candidate['items']),
                                'count' => $candidate['count'],
                                'support' => $candidate['support'],
                                'is_frequent' => $candidate['is_frequent'],
                                'type' => 'candidate'
                            ]);
                        }
                    }

                    // Save Frequent
                    if (isset($data['frequent'])) {
                        foreach ($data['frequent'] as $frequent) {
                            AnalysisItemset::create([
                                'step_id' => $step->id,
                                'items' => implode(', ', $frequent['items']),
                                'count' => $frequent['count'],
                                'support' => $frequent['support'],
                                'is_frequent' => true,
                                'type' => 'frequent'
                            ]);
                        }
                    }
                }
            }

            foreach ($results as $rule) {
                AprioriResult::create([
                    'session_id' => $session->id,
                    'antecedent' => implode(', ', $rule['antecedent_names']),
                    'consequent' => implode(', ', $rule['consequent_names']),
                    'support' => $rule['support'],
                    'confidence' => $rule['confidence'],
                ]);
            }
        });

        return redirect()->route('history.index')->with('success', 'Hasil analisis berhasil disimpan!');
    }
}
