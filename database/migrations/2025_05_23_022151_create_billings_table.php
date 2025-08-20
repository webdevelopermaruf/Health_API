<?php

use App\Models\Appointments;
use App\Models\Doctors;
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
     * Status 0 = Pending Payment
     * Status 1 = Paid
     * Status -1 = Cancel.
     *
     *
     */
    public function up(): void
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Patient::class);
            $table->foreignIdFor(Doctors::class)->nullable();
            $table->foreignIdFor(Appointments::class)->nullable();
            $table->json('services')->nullable();
            $table->decimal('appointment_fee', 10,2)->nullable();
            $table->decimal('services_fee', 10,2)->nullable();
            $table->tinyInteger('discount_type')->nullable();
            $table->decimal('discount', 10,2)->nullable();
            $table->decimal('VAT', 10,2)->comment('percent')->nullable();
            $table->decimal('payable', 10,2); // bill
            $table->decimal('received', 10,2); // money paid
            $table->decimal('changes', 10,2); // changes
            $table->foreignIdFor(PaymentMethods::class)->nullable();
            $table->foreignIdFor(User::class);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE billings AUTO_INCREMENT = 1000;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
