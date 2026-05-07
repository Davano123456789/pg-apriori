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
        $dateValue = $row['tanggal'];
        if (is_numeric($dateValue)) {
            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue);
        } else {
            try {
                // Try d/m/Y first as it is common in Indonesia/Excel
                $date = Carbon::createFromFormat('d/m/Y', $dateValue);
            } catch (\Exception $e) {
                try {
                    $date = Carbon::parse($dateValue);
                } catch (\Exception $e2) {
                    $date = now();
                }
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
