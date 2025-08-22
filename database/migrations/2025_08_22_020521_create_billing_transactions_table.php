<?php

use App\Models\Cases;
use App\Models\Patient;
use App\Models\PaymentMethods;
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
        Schema::create('billing_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Patient::class);
            $table->foreignIdFor(Cases::class)->nullable();
            $table->tinyInteger('trx_type')->default(1);
            $table->decimal('amount', 10,2)->default(1);
            $table->tinyInteger('billing_type')->default(1);
            $table->integer('billing_id');
            $table->foreignIdFor(PaymentMethods::class)->default(1);
            $table->foreignIdFor(User::class)->default(1);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_transactions');
    }
};
