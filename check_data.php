<?php

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$total = Transaction::distinct('invoice_no')->count('invoice_no');
echo "Total Unique Invoices: " . $total . "\n";

if ($total == 0) {
    echo "NO DATA FOUND IN TRANSACTIONS TABLE\n";
    exit;
}

$items = Transaction::select('item_code', 'item_name', DB::raw('count(distinct invoice_no) as count'))
    ->groupBy('item_code', 'item_name')
    ->orderBy('count', 'desc')
    ->limit(10)
    ->get();

echo "TOP 10 ITEMS:\n";
foreach ($items as $i) {
    $percent = ($i->count / $total) * 100;
    echo "- " . $i->item_code . " (" . $i->item_name . "): " . $i->count . " times (" . round($percent, 2) . "%)\n";
}
