<?php

use App\Models\Billing;
use App\Models\Cases;
use App\Models\Patient;
use App\Models\Services;
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
        Schema::create('lab_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Billing::class);
            $table->foreignIdFor(Patient::class);
            $table->foreignIdFor(Services::class);
            $table->foreignIdFor(Cases::class)->nullable();
            $table->tinyInteger('billing_type')->default(2); // 1 for indoor 2 for outdoor
            $table->text('report')->nullable();
            $table->foreignIdFor(User::class)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
        DB::statement('ALTER TABLE lab_reports AUTO_INCREMENT = 100000;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_reports');
    }
};
