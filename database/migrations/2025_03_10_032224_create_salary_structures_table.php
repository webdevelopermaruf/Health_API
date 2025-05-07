<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     Salary Type / Overtime Type:
         1 => Monthly
         2 => Weekly
         3 => Daily
         4 => Hourly
         5 => Per Shift
     */
    public function up(): void
    {
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->string("designation");
            $table->integer("basic_salary");
            $table->tinyInteger("salary_type");
            $table->integer("overtime_rate")->nullable();
            $table->tinyInteger("overtime_type")->nullable();
            $table->integer("paid_leave")->nullable(); // how many paid leave per month
            $table->json("allowances")->nullable(); // allowances rules
            $table->json("deductions")->nullable(); // deductions rules
            $table->integer("bonus")->default(0);
            $table->integer("emp_count")->default(0);
            $table->tinyInteger("status")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_structures');
    }
};
