<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $shops = Shop::orderBy('created_at', 'desc')->get();
            return response()->json($shops);
        }

        // Bukan AJAX → tampilkan halaman CRUD
        return view('shop.index');
    }

    /**
     * POST /shops
     * Simpan shop baru.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:aktif,nonaktif',
        ]);

        $shop = Shop::create($data);

        return response()->json($shop, Response::HTTP_CREATED);
    }

    /**
     * GET /shops/{shop}
     * Return JSON satu shop untuk edit form.
     */
    public function show(Shop $shop)
    {
        return response()->json($shop);
    }

    /**
     * PUT /shops/{shop}
     * Update shop.
     */
    public function update(Request $request, Shop $shop)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:aktif,nonaktif',
        ]);

        $shop->update($data);

        return response()->json($shop);
    }

    /**
     * DELETE /shops/{shop}
     * Hapus shop.
     */
    public function destroy(Shop $shop)
    {
        $shop->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
