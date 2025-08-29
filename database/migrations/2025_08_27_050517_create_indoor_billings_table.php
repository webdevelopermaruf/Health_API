<?php

use App\Models\Cases;
use App\Models\Patient;
use App\Models\User;
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
        Schema::create('indoor_billings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Patient::class, 'patient_id');
            $table->foreignIdFor(Cases::class, 'cases_id');
            $table->json( 'services');
            $table->decimal('total', 10,2)->nullable();
            $table->decimal( 'bed_bill',10,2)->nullable();
            $table->decimal( 'pharmacy_bill',10,2)->nullable();
            $table->tinyInteger('discount_type')->nullable();
            $table->decimal('discount', 10,2)->nullable();
            $table->decimal('VAT', 10,2)->comment('percent')->nullable();
            $table->decimal('payable', 10,2); // total bill
            $table->decimal('received', 10,2); // money paid
            $table->foreignIdFor(User::class, 'discounted_by')->nullable(); // discounted by
            $table->tinyInteger('status')->default(0);
            $table->foreignIdFor(User::class,'received_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indoor_billings');
    }
};
