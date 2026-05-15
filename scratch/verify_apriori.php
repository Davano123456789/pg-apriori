<?php

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$items = ['968', '1152', '1155'];

// Total unique invoices
$totalInvoices = Transaction::distinct('invoice_no')->count('invoice_no');

// Invoices containing all three
$countAllThree = Transaction::select('invoice_no')
    ->whereIn('item_code', $items)
    ->groupBy('invoice_no')
    ->havingRaw('COUNT(DISTINCT item_code) = 3')
    ->get()
    ->count();

$support = ($countAllThree / $totalInvoices) * 100;

echo "Total Unique Invoices: $totalInvoices\n";
echo "Count for {968, 1152, 1155}: $countAllThree\n";
echo "Support: " . number_format($support, 4) . "%\n";

// Confidence {968, 1152} -> {1155}
$countSub = Transaction::select('invoice_no')
    ->whereIn('item_code', ['968', '1152'])
    ->groupBy('invoice_no')
    ->havingRaw('COUNT(DISTINCT item_code) = 2')
    ->get()
    ->count();

if ($countSub > 0) {
    $confidence = ($countAllThree / $countSub) * 100;
    echo "Confidence {968, 1152} -> {1155}: " . number_format($confidence, 2) . "% ($countAllThree / $countSub)\n";
}

// Confidence {968, 1155} -> {1152}
$countSub2 = Transaction::select('invoice_no')
    ->whereIn('item_code', ['968', '1155'])
    ->groupBy('invoice_no')
    ->havingRaw('COUNT(DISTINCT item_code) = 2')
    ->get()
    ->count();

if ($countSub2 > 0) {
    $confidence2 = ($countAllThree / $countSub2) * 100;
    echo "Confidence {968, 1155} -> {1152}: " . number_format($confidence2, 2) . "% ($countAllThree / $countSub2)\n";
}

// Confidence {1152, 1155} -> {968}
$countSub3 = Transaction::select('invoice_no')
    ->whereIn('item_code', ['1152', '1155'])
    ->groupBy('invoice_no')
    ->havingRaw('COUNT(DISTINCT item_code) = 2')
    ->get()
    ->count();

if ($countSub3 > 0) {
    $confidence3 = ($countAllThree / $countSub3) * 100;
    echo "Confidence {1152, 1155} -> {968}: " . number_format($confidence3, 2) . "% ($countAllThree / $countSub3)\n";
}
