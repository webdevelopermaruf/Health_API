<?php

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
        Schema::create('out_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('trx_id')->nullable();
            $table->string('description')->nullable(); // 1 = Administration;  2 = Pharmacy;
            $table->string('type'); // 1 = expenses type;  2 = pharmacy purchase; 3 = Assets
            $table->decimal('amount');
            $table->string('paid_to');
            $table->foreignIdFor(PaymentMethods::class,'payment_methods_id');
            $table->foreignIdFor(User::class,'user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('out_transactions');
    }
};
