<?php

use App\Models\Billing;
use App\Models\Departments;
use App\Models\Doctors;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Status = 1 = Active Cases
     * Status = 2 = Discharged / Done
     * Status = 0 = Cancel Cases
     */
    public function up(): void
    {
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Patient::class);
            $table->foreignIdFor(Doctors::class);
            $table->foreignIdFor(Departments::class);
            $table->foreignIdFor(Billing::class)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->foreignIdFor(User::class, 'prepared_by');
            $table->foreignIdFor(User::class, 'referred_by')->nullable();
            // is_discharged == 0 because it's active case
            $table->timestamps();
        });
        Schema::table('cases', function (Blueprint $table) {
            DB::statement('ALTER TABLE cases AUTO_INCREMENT = 1000;');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
