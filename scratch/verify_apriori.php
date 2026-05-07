<?php

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// 1. Get all transactions grouped by invoice
$transactions = Transaction::select('invoice_no', 'item_code')
    ->get()
    ->groupBy('invoice_no');

$totalInvoices = $transactions->count();
echo "Total Invoices: " . $totalInvoices . "\n\n";

$pairCounts = [];
$tripleCounts = [];

foreach ($transactions as $invoiceNo => $items) {
    $itemCodes = $items->pluck('item_code')->unique()->values()->toArray();
    sort($itemCodes);
    
    $count = count($itemCodes);
    
    // 2-Itemsets
    for ($i = 0; $i < $count; $i++) {
        for ($j = $i + 1; $j < $count; $j++) {
            $pair = $itemCodes[$i] . ', ' . $itemCodes[$j];
            if (!isset($pairCounts[$pair])) {
                $pairCounts[$pair] = 0;
            }
            $pairCounts[$pair]++;
            
            // 3-Itemsets
            for ($k = $j + 1; $k < $count; $k++) {
                $triple = $itemCodes[$i] . ', ' . $itemCodes[$j] . ', ' . $itemCodes[$k];
                if (!isset($tripleCounts[$triple])) {
                    $tripleCounts[$triple] = 0;
                }
                $tripleCounts[$triple]++;
            }
        }
    }
}

arsort($pairCounts);
arsort($tripleCounts);

echo "2-ITEMSET FREQUENCIES:\n";
foreach ($pairCounts as $pair => $count) {
    if ($count >= 1) {
        $support = ($count / $totalInvoices) * 100;
        echo "- [" . $pair . "]: " . $count . " times (" . number_format($support, 2) . "%)\n";
    }
}

echo "\n3-ITEMSET FREQUENCIES (Count >= 2):\n";
foreach ($tripleCounts as $triple => $count) {
    if ($count >= 2) {
        $support = ($count / $totalInvoices) * 100;
        echo "- [" . $triple . "]: " . $count . " times (" . number_format($support, 2) . "%)\n";
    }
}
