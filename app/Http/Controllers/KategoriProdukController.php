<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use App\Models\ProductCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         return view('performa_produk.kategori');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function lists()
    {
        $kategori = KategoriProduk::query()->where('status', 'aktif')->get();
        return response()->json(['categories' => $kategori], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        KategoriProduk::create([
            'nama_kategori' => $request->nama_kategori,
            'status' => 'aktif'
        ]);

        return response()->json(['success' => true,'message' => 'Data berhasil disimpan.'], 200);
    }

    public function storeProduct(Request $request){
        ProductCode::create([
            'kategori_id' => $request->kategori_id,
            'product_code' => $request->product_code,
        ]);

        return response()->json(['success' => true,'message' => 'Data berhasil disimpan.']);
    }

    public function getProductCodes($id){
        $product = DB::select("
        SELECT a.id,a.product_code AS kode_produk, IFNULL(b.produk,'-') AS nama_produk, 
        CASE WHEN b.produk IS NULL THEN 'tidak_ada'
        ELSE 'ada' END AS status
        FROM product_codes AS a
        LEFT JOIN product_compare_table_ones AS b ON a.product_code = b.kode_produk AND b.kode_variasi IS NULL
        WHERE a.kategori_id = $id
        ");
  
        return response()->json(['data' => $product], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $kategori = KategoriProduk::where('id', $id)->first();
        return view('performa_produk.showCategory', ['kategori' => $kategori ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KategoriProduk $kategoriProduk)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KategoriProduk $kategoriProduk)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        KategoriProduk::where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus.']);

    }

    public function deleteProductCode($id){
        ProductCode::where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Product code berhasil dihapus.']);
    }
}
