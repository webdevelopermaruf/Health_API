<?php

use App\Models\Rooms;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     timeline 1 = hourly
     timeline 2 = daily
     */
    public function up(): void
    {
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Rooms::class);
            $table->string('bed_number');
            $table->string('bed_type');
            $table->decimal('price',10,2);
            $table->tinyInteger('timeline')->default(1);
            $table->tinyInteger('is_booked')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beds');
    }
};
