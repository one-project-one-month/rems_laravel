<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("property_id")->constrained('properties');
            $table->foreignId('client_id')->constrained('clients');
            $table->date('transaction_date')->default(DB::raw('(CURRENT_DATE())'));
            $table->integer('rental_period');
            $table->integer('sale_price');
            $table->integer('commission');
            $table->string('status', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
