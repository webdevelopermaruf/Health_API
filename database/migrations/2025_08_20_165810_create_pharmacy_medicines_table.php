<?php

use App\Models\PharmacySupplier;
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
        Schema::create('pharmacy_medicines', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('unit');
            $table->string('name');
            $table->string('generic_name');
            $table->string('shelf')->nullable();
            $table->foreignIdFor(PharmacySupplier::class)->nullable();
            $table->decimal('factory_price',10,2);
            $table->decimal('sales_price',10,2);
            $table->integer('qty');
            $table->date('expiry_date')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_medicines');
    }
};
