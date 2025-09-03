<?php

use App\Models\Cases;
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
        Schema::create('case_records', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Cases::class, 'cases_id');
            $table->string('admission_reason')->nullable();
            $table->date('dischargeDate');
            $table->string('dischargeType')->default('N/A');
            $table->string('diagnosis')->nullable();
            $table->string('allergyHistory')->nullable();
            $table->string('complaints')->nullable();
            $table->string('pastHistory')->nullable();
            $table->string('findings')->nullable();
            $table->string('investigation')->nullable();
            $table->string('hospitalCourse')->nullable();
            $table->string('medicationsDuringStay')->nullable();
            $table->string('diet')->nullable();
            $table->string('dischargeMedications')->nullable();
            $table->string('advice')->nullable();
            $table->string('followUp')->nullable();
            $table->string('urgentCareInstructions')->nullable();
            $table->string('seniorHouseOfficer');
            $table->string('specialist');
            $table->string('consultant');
            $table->foreignIdFor(User::class, 'discharged_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_records');
    }
};
