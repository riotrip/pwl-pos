<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LevelModel;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class LevelController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Level',
            'list' => ['Home', 'Level'],
        ];

        $page = (object) [
            'title' => 'Daftar Level yang terdaftar di sistem',
        ];

        $activeMenu = 'level';

        return view('level.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }
    
    public function list(Request $request)
    {
        $levels = LevelModel::select('level_id', 'level_kode', 'level_nama');

        return DataTables::of($levels)->addIndexColumn()->addColumn('aksi', function ($level) {
            $btn = '<a href="' . url('/level/' . $level->level_id) . '" class="btn btn-info btn-sm">Detail</a> ';
            $btn .= '<a href="' . url('/level/' . $level->level_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
            $btn .= '<form class="d-inline-block" method="POST" action="' . url('/level/' . $level->level_id) . '">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    // public function list(Request $request)
    // {
    //     $levels = LevelModel::select('level_id', 'level_kode', 'level_nama');

    //     return DataTables::of($levels)
    //         ->addIndexColumn()
    //         ->addColumn('aksi', function ($level) {
    //             $btn = '<a href="' . url('/level/' . $level->level_id) . '" class="btn btn-info btn-sm"> Detail</a> ';
    //             $btn .= '<a href="' . url('/level/' . $level->level_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
    //             $btn .= '<form class="d-inline-block" method="POST" action="' . url('/level/' . $level->level_id) . '">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
    //             return $btn;
    //         })
    //         ->rawColumns(['aksi'])
    //         ->make(true);
    // }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Level',
            'list' => ['Home', 'Level', 'Tambah'],
        ];

        $page = (object) [
            'title' => 'Tambah Level Baru',
        ];

        $activeMenu = 'level';

        return view('level.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'level_kode' => 'required',
            'level_nama' => 'required',
        ]);

        LevelModel::create([
            'level_kode' => $request->level_kode,
            'level_nama' => $request->level_nama,
        ]);

        return redirect('/level')->with('status', 'Data level berhasil ditambahkan!');
    }

    public function show($id)
    {
        $breadcrumb = (object) [
            'title' => 'Detail Level',
            'list' => ['Home', 'Level', 'Detail'],
        ];

        $page = (object) [
            'title' => 'Detail Level',
        ];

        $activeMenu = 'level';

        $level = LevelModel::find($id);

        return view('level.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function edit($id)
    {
        $breadcrumb = (object) [
            'title' => 'Edit Level',
            'list' => ['Home', 'Level', 'Edit'],
        ];

        $page = (object) [
            'title' => 'Edit Level',
        ];

        $activeMenu = 'level';

        $level = LevelModel::find($id);

        return view('level.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'level_kode' => 'required',
            'level_nama' => 'required',
        ]);

        LevelModel::where('level_id', $id)
            ->update([
                'level_kode' => $request->level_kode,
                'level_nama' => $request->level_nama,
            ]);

        return redirect('/level')->with('status', 'Data level berhasil diubah!');
    }

    public function destroy($id)
    {
        $check = LevelModel::find($id);
        if (!$check) {
            return redirect('/level')->with('error', 'Data level tidak ditemukan');
        }

        try {
            LevelModel::destroy($id);
            return redirect('/level')->with('success', 'Data level berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/level')->with('error', 'Data level gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
    {
        return view('level.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_kode' => 'required',
                'level_nama' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => 'Validasi Gagal', 'msgField' => $validator->errors()->all()]);
            }

            LevelModel::create($request->all());
            return response()->json(['status' => true, 'message' => 'Data level berhasil ditambahkan!']);
        }
        redirect('/level');
    }

    public function edit_ajax($id)
    {
        $level = LevelModel::find($id);
        return response()->json(['status' => true, 'data' => $level]);
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_kode' => 'required',
                'level_nama' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => 'Validasi Gagal', 'msgField' => $validator->errors()->all()]);
            }

            LevelModel::where('level_id', $id)->update($request->all());
            return response()->json(['status' => true, 'message' => 'Data level berhasil diubah!']);
        }
        redirect('/level');
    }

    public function confirm_ajax($id)
    {
        $level = LevelModel::find($id);
        return response()->json(['status' => true, 'data' => $level]);
    }

    public function delete_ajax($id)
    {
        if (request()->ajax() || request()->wantsJson()) {
            $check = LevelModel::find($id);
            if (!$check) {
                return response()->json(['status' => false, 'message' => 'Data level tidak ditemukan']);
            }

            try {
                LevelModel::destroy($id);
                return response()->json(['status' => true, 'message' => 'Data level berhasil dihapus']);
            } catch (\Illuminate\Database\QueryException $e) {
                return response()->json(['status' => false, 'message' => 'Data level gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini']);
            }
        }
        redirect('/level');
    }

    public function import()
    {
        return view('level.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [ // validasi file harus xls atau xlsx, max 1MB 
                'file_level' => ['required', 'mimes:xlsx', 'max:1024']
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => 'Validasi Gagal', 'msgField' => $validator->errors()]);
            }
            $file = $request->file('file_level'); // ambil file dari request
            $reader = IOFactory::createReader('Xlsx'); // load reader file excel 
            $reader->setReadDataOnly(true); // hanya membaca data 
            $spreadsheet = $reader->load($file->getRealPath()); // load file excel 
            $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif 
            $data = $sheet->toArray(null, false, true, true); // ambil data excel 
            $insert = [];
            if (count($data) > 1) { // jika data lebih dari 1 baris 
                foreach ($data as $baris => $value) {
                    if ($baris > 1) { // baris ke 1 adalah header, maka lewati 
                        $insert[] = [
                            'level_kode' => $value['A'],
                            'level_nama' => $value['B'],
                            'created_at' => now(),
                        ];
                    }
                }
                if (count($insert) > 0) { // insert data ke database, jika data sudah ada, maka diabaikan 
                    LevelModel::insertOrIgnore($insert);
                }
                return response()->json(['status' => true, 'message' => 'Data berhasil diimport']);
            } else {
                return response()->json(['status' => false, 'message' => 'Tidak ada data yang diimport']);
            }
        }
        return redirect('/');
    }

    public function export_excel()
    {
        $level = LevelModel::select('level_id', 'level_kode', 'level_nama')
            ->orderBy('level_id')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Level Kode');
        $sheet->setCellValue('C1', 'Level Nama');

        $sheet->getStyle('A1:C1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;
        foreach ($level as $key => $value) {
            $sheet->setCellValue('A' . $baris, $baris - 1);
            $sheet->setCellValue('B' . $baris, $value->level_kode);
            $sheet->setCellValue('C' . $baris, $value->level_nama);
            $baris++;
            $no++;
        }

        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Level');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Level ' . date('Y-m-d H:i:s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $level = LevelModel::select('level_id', 'level_kode', 'level_nama')
            ->orderBy('level_id')
            ->get();

        $pdf = Pdf::loadView('level.export_pdf', ['level' => $level]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->render();

        return $pdf->download('Data Level ' . date('Y-m-d H:i:s') . '.pdf');
    }
    // public function index()
    // {
    //     // DB::insert('insert into m_level (level_kode, level_nama, created_at) values (?, ?, ?)', ['CUS', 'Pelanggan', now()]);
    //     // return 'Insert data baru berhasil';

    //     // $row = DB::update('update m_level set level_nama = ? where level_kode = ?', ['Customer', 'CUS']);
    //     // return 'Update data berhasil. Jumlah data yang diupdate: ' . $row . 'baris';

    //     // $row = DB::delete('delete from m_level where level_kode = ?', ['CUS']);
    //     // return 'Delete data berhasil. Jumlah data yang dihapus: ' . $row . 'baris';

    //     $data = DB::select('select * from m_level');
    //     return view('level', ['data' => $data]);
    // }
}
