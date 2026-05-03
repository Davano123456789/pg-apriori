<?php

namespace App\Imports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\LibreSpreadsheet\Shared\Date;

class TransactionImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Skip empty rows or rows missing vital data
        if (!isset($row['no_invoice']) || empty($row['no_invoice']) || !isset($row['kode_barang'])) {
            return null;
        }

        // Handle date (Excel dates can be serial numbers)
        $date = $row['tanggal'];
        if (is_numeric($date)) {
            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date);
        } else {
            try {
                $date = Carbon::parse($date);
            } catch (\Exception $e) {
                $date = now();
            }
        }

        return new Transaction([
            'invoice_no'       => $row['no_invoice'],
            'customer_code'    => $row['kode_customer'] ?? null,
            'customer_name'    => $row['nama_customer'] ?? null,
            'item_code'        => $row['kode_barang'],
            'item_name'        => $row['nama_barang'],
            'quantity'         => $row['qty'] ?? null,
            'transaction_date' => $date,
        ]);
    }
}
