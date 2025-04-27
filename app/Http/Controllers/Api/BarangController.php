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
        $request->validate([
            'barang_kode' => 'required',
            'barang_nama' => 'required',
            'harga_beli' => 'required',
            'harga_jual' => 'required',
            'kategori_id' => 'required',
        ]);

        $barang = BarangModel::create($request->all());
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
