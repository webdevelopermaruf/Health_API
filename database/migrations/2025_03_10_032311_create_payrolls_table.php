<?php

use App\Models\SalaryStructure;
use App\Models\Staffs;
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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('trx_id')->unique();
            $table->string('employee_code');
            $table->foreignIdFor(SalaryStructure::class);
            $table->tinyInteger("pay_type");
            $table->string("pay_period")->nullable();
            $table->integer("paid_worked");
            $table->tinyInteger("over_type")->nullable();
            $table->integer("over_worked")->default(0);
            $table->integer("basic_salary"); // basic salary
            $table->integer("over_total")->default(0); // overtime salary
            $table->integer("total_allowance")->default(0);
            $table->integer("total_deduction")->default(0);
            $table->integer("bonus")->default(0);
            $table->integer("gross_salary")->default(0);
            $table->integer("net_salary")->default(0);
            $table->integer("payment_status")->default(0);
            $table->integer("payment_method")->default(0);
            $table->json("details")->nullable();
            $table->integer("received_by")->nullable();
            $table->integer("issued_by")->nullable();
            $table->timestamp("pay_date")->nullable();
            $table->tinyInteger("status")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
