<?php

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$transactions = Transaction::select('invoice_no', 'item_code')
    ->get()
    ->groupBy('invoice_no');

echo "Checking invoices for items 2, 19, 20:\n";
foreach ($transactions as $invoiceNo => $items) {
    $codes = $items->pluck('item_code')->toArray();
    if (in_array('2', $codes) && in_array('19', $codes) && in_array('20', $codes)) {
        echo "- " . $invoiceNo . " HAS 2, 19, 20\n";
    }
}
