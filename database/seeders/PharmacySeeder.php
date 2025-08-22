<?php

namespace Database\Seeders;

use App\Models\PharmacyMedicines;
use App\Models\PharmacySupplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PharmacySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PharmacySupplier::insert([
            'name' => ucwords("Abul"),
            'company_name' =>ucwords("Square"),
            'email' => strtolower("abul@square.com"),
            'phone' => "01746803899",
            'address' => ucfirst("Sylhet"),
            'status' => 1,
            'updated_at' => now(),
            'created_at' => now(),
        ]);

        PharmacyMedicines::insert([
            'code'          => strtolower("P-51651"),
            'name'          => ucwords("napa"),
            'unit'          => "stp",
            'generic_name'  => strtolower("paracetamol"),
            'shelf'         => "A",
            'pharmacy_supplier_id'=> 1,
            'factory_price' => rand(90,100),
            'sales_price'   => rand(100,200),
            'qty'           => 10,
            'expiry_date'   => date('Y-m-d'),
            'status'        => 1,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }
}
