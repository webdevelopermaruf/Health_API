<?php

use App\Models\Cases;
use App\Models\Patient;
use App\Models\PaymentMethods;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pharmacy_billings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Patient::class);
            $table->foreignIdFor(Cases::class)->nullable();
            $table->json('medicines')->nullable();
            $table->tinyInteger('discount_type')->nullable();
            $table->decimal('discount', 10,2)->nullable();
            $table->decimal('VAT', 10,2)->comment('percent')->nullable();
            $table->decimal('payable', 10,2); // bill
            $table->decimal('received', 10,2); // money paid
            $table->decimal('changes', 10,2); // changes
            $table->foreignIdFor(User::class);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
        Schema::table('billings', function (Blueprint $table) {
            DB::statement('ALTER TABLE pharmacy_billings AUTO_INCREMENT = 1000;');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_billings');
    }
};
