<?php

use App\Models\Cases;
use App\Models\PharmacyBilling;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * status = 0
     */
    public function up(): void
    {
        Schema::create('pharmacy_requisitions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Cases::class);
            $table->json('medicines');
            $table->foreignIdFor(PharmacyBilling::class)->nullable();
            $table->foreignIdFor(User::class, 'requisite_by');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_requisitions');
    }
};
