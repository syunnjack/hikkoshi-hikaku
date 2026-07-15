<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_reports', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('move_type', 20)->nullable();
            $table->string('distance_range', 20)->nullable();
            $table->unsignedInteger('total_price');
            $table->text('comment')->nullable();
            $table->string('nickname', 30)->default('匿名');
            $table->string('ip_hash', 64);
            $table->timestamps();

            $table->index('company_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_reports');
    }
};
