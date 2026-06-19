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
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('office_id')->nullable();
                $table->string('date_format')->default('Y-m-d');
                $table->string('time_format')->default('H:i:s');
                $table->timestamps();
                
                $table->foreign('office_id')->references('id')->on('admins')->onDelete('cascade');
                $table->index(['office_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
