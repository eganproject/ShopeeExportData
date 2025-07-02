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
        Schema::create('produk_performances', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk');
            $table->string('produk');
            $table->string('status_produk_saat_ini')->nullable();
            $table->string('kode_variasi')->nullable();
            $table->string('nama_variasi')->nullable();
            $table->string('status_variasi_saat_ini')->nullable();
            $table->string('kode_variasi_2')->nullable();
            $table->string('sku_induk')->nullable();
            $table->decimal('pengunjung_produk_kunjungan')->default(0);
            $table->decimal('halaman_produk_dilihat')->default(0);
            $table->decimal('pengunjung_melihat_tanpa_membeli')->default(0);
            $table->decimal('tingkat_pengunjung_melihat_tanpa_membeli', 5, 2)->default(0.00);
            $table->decimal('klik_pencarian')->default(0);
            $table->decimal('suka')->default(0);
            $table->decimal('pengunjung_produk_menambahkan_ke_keranjang')->default(0);
            $table->decimal('dimasukkan_ke_keranjang_produk')->default(0);
            $table->decimal('tingkat_konversi_produk_dimasukkan_ke_keranjang', 5, 2)->default(0.00);
            $table->decimal('total_pembeli_pesanan_dibuat')->default(0);
            $table->decimal('produk_pesanan_dibuat')->default(0);
            $table->decimal('total_penjualan_pesanan_dibuat_idr', 15, 2)->default(0.00);
            $table->decimal('tingkat_konversi_pesanan_dibuat', 5, 2)->default(0.00);
            $table->decimal('total_pembeli_pesanan_siap_dikirim')->default(0);
            $table->decimal('produk_pesanan_siap_dikirim')->default(0);
            $table->decimal('penjualan_pesanan_siap_dikirim_idr', 15, 2)->default(0.00);
            $table->decimal('tingkat_konversi_pesanan_siap_dikirim', 5, 2)->default(0.00);
            $table->decimal('tingkat_konversi_pesanan_siap_dikirim_dibagi_pesanan_dibuat', 5, 2)->default(0.00);
            $table->decimal('persen_pembelian_ulang_pesanan_siap_dikirim', 5, 2)->default(0.00);
            $table->decimal('rata_rata_hari_pembelian_terulang_pesanan_siap_dikirim')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_performances');
    }
};
