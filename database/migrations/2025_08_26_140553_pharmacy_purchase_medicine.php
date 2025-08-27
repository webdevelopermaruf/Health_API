<?php

use App\Models\PharmacyMedicines;
use App\Models\PharmacyPurchases;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pharmacy_purchase_medicine', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PharmacyPurchases::class, 'pharmacy_purchase_id');
            $table->foreignIdFor(PharmacyMedicines::class, 'pharmacy_medicine_id');
            $table->integer('qty');
            $table->integer('prevStock');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_purchase_medicine');
    }
};
