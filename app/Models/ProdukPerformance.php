<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukPerformance extends Model
{
       use HasFactory;
       protected $fillable = [
        'kode_produk',
        'produk',
        'status_produk_saat_ini',
        'kode_variasi',
        'nama_variasi',
        'status_variasi_saat_ini',
        'kode_variasi_2',
        'sku_induk',
        'pengunjung_produk_kunjungan',
        'halaman_produk_dilihat',
        'pengunjung_melihat_tanpa_membeli',
        'tingkat_pengunjung_melihat_tanpa_membeli',
        'klik_pencarian',
        'suka',
        'pengunjung_produk_menambahkan_ke_keranjang',
        'dimasukkan_ke_keranjang_produk',
        'tingkat_konversi_produk_dimasukkan_ke_keranjang',
        'total_pembeli_pesanan_dibuat',
        'produk_pesanan_dibuat',
        'total_penjualan_pesanan_dibuat_idr',
        'tingkat_konversi_pesanan_dibuat',
        'total_pembeli_pesanan_siap_dikirim',
        'produk_pesanan_siap_dikirim',
        'penjualan_pesanan_siap_dikirim_idr',
        'tingkat_konversi_pesanan_siap_dikirim',
        'tingkat_konversi_pesanan_siap_dikirim_dibagi_pesanan_dibuat',
        'persen_pembelian_ulang_pesanan_siap_dikirim',
        'rata_rata_hari_pembelian_terulang_pesanan_siap_dikirim',
    ];

    // Kolom yang harus di-casting ke tipe data tertentu
    protected $casts = [
        'tingkat_pengunjung_melihat_tanpa_membeli' => 'decimal:2',
        'tingkat_konversi_produk_dimasukkan_ke_keranjang' => 'decimal:2',
        'total_penjualan_pesanan_dibuat_idr' => 'decimal:2',
        'tingkat_konversi_pesanan_dibuat' => 'decimal:2',
        'penjualan_pesanan_siap_dikirim_idr' => 'decimal:2',
        'tingkat_konversi_pesanan_siap_dikirim' => 'decimal:2',
        'tingkat_konversi_pesanan_siap_dikirim_dibagi_pesanan_dibuat' => 'decimal:2',
        'persen_pembelian_ulang_pesanan_siap_dikirim' => 'decimal:2',
    ];
}
