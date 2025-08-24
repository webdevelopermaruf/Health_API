<?php

use App\Models\Beds;
use App\Models\Cases;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * discharged_from_bed = if patient discharged from last bed then 1. if change the bed then 0
     */
    public function up(): void
    {
        Schema::create('bed_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Cases::class);
            $table->foreignIdFor(Beds::class, 'current_bed');
            $table->foreignIdFor(Beds::class, 'from_bed')->nullable();
            $table->tinyInteger('discharged_from_bed')->default(1)->nullable(); // if discharged from this bed then 1
            $table->timestamp('entered_at');
            $table->timestamp('exited_at')->nullable();
            $table->foreignIdFor(User::class, 'allocated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bed_allocations');
    }
};
