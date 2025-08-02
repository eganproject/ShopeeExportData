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
        Schema::create('multi_comparative_f_sales_twos', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nama_produk');
            $table->string('sku');
            $table->decimal('pendapatan', 20, 2)->default(0);
            $table->string('platform');
            $table->integer('shop_id');
            $table->enum('month_status',['current','previous'])->default('current');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('multi_comparative_f_sales_twos');
    }
};
