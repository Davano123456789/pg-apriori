<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Imports\TransactionImport;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::orderBy('transaction_date', 'asc')->paginate(100);
        return view('transactions.index', compact('transactions'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new TransactionImport, $request->file('file'));
            return back()->with('success', 'Data transaksi berhasil diimport!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function truncate()
    {
        Transaction::truncate();
        return back()->with('success', 'Semua data transaksi telah dihapus!');
    }
}
