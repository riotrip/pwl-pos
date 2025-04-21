<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SupplierModel;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class SupplierController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Supplier',
            'list' => ['Home', 'Supplier'],
        ];

        $page = (object) [
            'title' => 'Daftar Supplier yang terdaftar di sistem',
        ];

        $activeMenu = 'supplier';

        return view('supplier.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $suppliers = SupplierModel::select('supplier_id', 'supplier_kode', 'supplier_nama');

        return DataTables::of($suppliers)->addIndexColumn()->addColumn('aksi', function ($supplier) {
            $btn = '<a href="' . url('/supplier/' . $supplier->supplier_id) . '" class="btn btn-info btn-sm">Detail</a> ';
            $btn .= '<a href="' . url('/supplier/' . $supplier->supplier_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
            $btn .= '<form class="d-inline-block" method="POST" action="' . url('/supplier/' . $supplier->supplier_id) . '">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    // public function list(Request $request)
    // {
    //     $suppliers = SupplierModel::select('supplier_id', 'supplier_kode', 'supplier_nama');

    //     return DataTables::of($suppliers)
    //         ->addIndexColumn()
    //         ->addColumn('aksi', function ($supplier) {
    //             $btn = '<a href="' . url('/supplier/' . $supplier->supplier_id) . '" class="btn btn-info btn-sm"> Detail</a> ';
    //             $btn .= '<a href="' . url('/supplier/' . $supplier->supplier_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
    //             $btn .= '<form class="d-inline-block" method="POST" action="' . url('/supplier/' . $supplier->supplier_id) . '">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
    //             return $btn;
    //         })
    //         ->rawColumns(['aksi'])
    //         ->make(true);
    // }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Supplier',
            'list' => ['Home', 'Supplier', 'Tambah'],
        ];

        $page = (object) [
            'title' => 'Tambah Supplier Baru',
        ];

        $activeMenu = 'supplier';

        return view('supplier.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_kode' => 'required',
            'supplier_nama' => 'required',
        ]);

        SupplierModel::create([
            'supplier_kode' => $request->supplier_kode,
            'supplier_nama' => $request->supplier_nama,
        ]);

        return redirect('/supplier')->with('status', 'Data supplier berhasil ditambahkan!');
    }

    public function show($id)
    {
        $breadcrumb = (object) [
            'title' => 'Detail Supplier',
            'list' => ['Home', 'Supplier', 'Detail'],
        ];

        $page = (object) [
            'title' => 'Detail Supplier',
        ];

        $activeMenu = 'supplier';

        $supplier = SupplierModel::find($id);

        return view('supplier.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'supplier' => $supplier, 'activeMenu' => $activeMenu]);
    }

    public function edit($id)
    {
        $breadcrumb = (object) [
            'title' => 'Edit Supplier',
            'list' => ['Home', 'Supplier', 'Edit'],
        ];

        $page = (object) [
            'title' => 'Edit Supplier',
        ];

        $activeMenu = 'supplier';

        $supplier = SupplierModel::find($id);

        return view('supplier.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'supplier' => $supplier, 'activeMenu' => $activeMenu]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_kode' => 'required',
            'supplier_nama' => 'required',
        ]);

        SupplierModel::where('supplier_id', $id)
            ->update([
                'supplier_kode' => $request->supplier_kode,
                'supplier_nama' => $request->supplier_nama,
            ]);

        return redirect('/supplier')->with('status', 'Data supplier berhasil diubah!');
    }

    public function destroy($id)
    {
        $check = SupplierModel::find($id);
        if (!$check) {
            return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
        }

        try {
            SupplierModel::destroy($id);
            return redirect('/supplier')->with('success', 'Data supplier berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/supplier')->with('error', 'Data supplier gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
    {
        return view('supplier.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'supplier_kode' => 'required',
                'supplier_nama' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => 'Validasi Gagal', 'msgField' => $validator->errors()->all()]);
            }

            SupplierModel::create($request->all());
            return response()->json(['status' => true, 'message' => 'Data supplier berhasil ditambahkan!']);
        }
        redirect('/supplier');
    }

    public function edit_ajax($id)
    {
        $supplier = SupplierModel::find($id);
        return view('supplier.edit_ajax', ['supplier' => $supplier]);
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'supplier_kode' => 'required',
                'supplier_nama' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => 'Validasi Gagal', 'msgField' => $validator->errors()->all()]);
            }

            SupplierModel::where('supplier_id', $id)
                ->update([
                    'supplier_kode' => $request->supplier_kode,
                    'supplier_nama' => $request->supplier_nama,
                ]);

            return response()->json(['status' => true, 'message' => 'Data supplier berhasil diubah!']);
        }
        redirect('/supplier');
    }

    public function confirm_ajax($id)
    {
        $supplier = SupplierModel::find($id);
        return response()->json(['status' => true, 'data' => $supplier]);
    }

    public function delete_ajax($id)
    {
        $check = SupplierModel::find($id);
        if (!$check) {
            return response()->json(['status' => false, 'message' => 'Data supplier tidak ditemukan']);
        }

        try {
            SupplierModel::destroy($id);
            return response()->json(['status' => true, 'message' => 'Data supplier berhasil dihapus']);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['status' => false, 'message' => 'Data supplier gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini']);
        }
    }
}
