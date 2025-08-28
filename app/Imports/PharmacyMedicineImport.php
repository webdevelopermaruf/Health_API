<?php

namespace App\Imports;

use App\Models\PharmacyMedicines;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PharmacyMedicineImport implements ToModel, WithHeadingRow
{
    public function headingRow(): int
    {
        return 1; // first row is header
    }

    public function model(array $row)
    {
        PharmacyMedicines::insert([
            'code'          => $row['code'],
            'name'          => $row['name'],
            'unit'          => $row['unit'] ?? '',
            'generic_name'  => $row['generic'] ?? '',
            'strength'          => $row['strength'],
            'shelf'         => null,
            'pharmacy_supplier_id' => $row['supplier'] ?? null,
            'factory_price' =>0,
            'sales_price'   => 0,
            'qty'           => 0,
            'expiry_date'   => null,
            'status'        => 1,
        ]);
    }
}
