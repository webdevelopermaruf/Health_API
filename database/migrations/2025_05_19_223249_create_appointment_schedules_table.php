<?php

use App\Models\Doctors;
use App\Models\Rooms;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Schedule = {'sun': ['9:00 - 14:00'], 'mon': [], 'tue':[] }
     *
     */

    public function up(): void
    {
        Schema::create('appointment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Doctors::class);
            $table->foreignIdFor(Rooms::class);
            $table->json('schedule')->nullable();
            $table->decimal('fee', 10, 2);
            $table->date('appointment_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_schedules');
    }
};
