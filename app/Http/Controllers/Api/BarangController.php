<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangModel;
use Illuminate\Http\Request;

class BarangController extends Controller
{

    public function index()
    {
        return BarangModel::with('kategori')->get();
    }

    public function store(Request $request)
    {
        $barang = BarangModel::create([
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'kategori_id' => $request->kategori_id,
            'image' => $request->image->hashName(),
        ]);

        return response()->json($barang, 201);
    }

    public function show(BarangModel $barang)
    {
        return $barang->load('kategori');
    }

    public function update(Request $request, BarangModel $barang)
    {
        $request->validate([
            'barang_kode' => 'sometimes',
            'barang_nama' => 'sometimes',
            'harga_beli' => 'sometimes',
            'harga_jual' => 'sometimes',
            'kategori_id' => 'sometimes',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $barang->update($request->all());
        return $barang->load('kategori');
    }

    public function destroy(BarangModel $barang)
    {
        $barang->delete();
        return response()->json([
            'status' => true,
            'message' => 'Data terhapus',
        ]);
    }
}
