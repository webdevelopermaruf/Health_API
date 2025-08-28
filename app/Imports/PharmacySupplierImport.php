<?php

namespace App\Imports;

use App\Models\PharmacySupplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PharmacySupplierImport implements ToModel, WithHeadingRow
{
    public function headingRow(): int
    {
        return 1; // first row is header
    }
    public function model(array $row)
    {
        PharmacySupplier::insert([
            'id' => $row['id'],
            'company_name' => $row['company'],
            'name'          => "Unknown",
            'status'        => 1,
        ]);
    }
}
