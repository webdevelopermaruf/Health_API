<?php

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
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('address')->nullable();
            $table->json('icon');
            $table->string('user_code')->nullable();
            $table->text('license_code')->nullable();
            $table->tinyInteger('maintenance')->default(0);
            $table->tinyInteger('sms')->default(0);
            $table->json('sms_api')->nullable();
            $table->json('attendance');
            $table->json('sms_rules');
            $table->json('payroll_rules');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
