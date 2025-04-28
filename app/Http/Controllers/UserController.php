<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use App\Models\LevelModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar User',
            'list' => ['Home', 'User'],
        ];

        $page = (object) [
            'title' => 'Daftar User yang terdaftar di sistem',
        ];

        $activeMenu = 'user';

        $level = LevelModel::all();

        return view('user.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $users = UserModel::select('user_id', 'username', 'nama', 'level_id')->with('level');

        if ($request->level_id) {
            $users->where('level_id', $request->level_id);
        }

        return DataTables::of($users)->addIndexColumn()->addColumn('aksi', function ($user) {
            $btn = '<a href="' . url('/user/' . $user->user_id) . '" class="btn btn-info btn-sm">Detail</a> ';
            $btn .= '<a href="' . url('/user/' . $user->user_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
            $btn .= '<form class="d-inline-block" method="POST" action="' . url('/user/' . $user->user_id) . '">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    // public function list(Request $request)
    // {
    //     $users = UserModel::select('user_id', 'username', 'nama', 'level_id')->with('level');

    //     if ($request->level_id) {
    //         $users->where('level_id', $request->level_id);
    //     }

    //     return DataTables::of($users)
    //         // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
    //         ->addIndexColumn()
    //         ->addColumn('aksi', function ($user) { // menambahkan kolom aksi
    //             $btn = '<a href="' . url('/user/' . $user->user_id) . '" class="btn btn-info btnsm"> Detail</a> ';
    //             $btn .= '<a href="' . url('/user/' . $user->user_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
    //             $btn .= '<form class="d-inline-block" method="POST" action="' . url('/user/' . $user->user_id) . '">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
    //             return $btn;
    //         })
    //         ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
    //         ->make(true);
    // }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah User',
            'list' => ['Home', 'User', 'Tambah'],
        ];

        $page = (object) [
            'title' => 'Tambah User',
        ];

        $level = LevelModel::all();
        $activeMenu = 'user';

        return view('user.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'level' => $level]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3|unique:m_user,username',
            'nama' => 'required|string|max:100',
            'password' => 'required|min:5',
            'level_id' => 'required|integer',
        ]);

        UserModel::create([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => bcrypt($request->password),
            'level_id' => $request->level_id,
        ]);

        return redirect('/user')->with('success', 'Data user berhasil ditambahkan');
    }

    public function show(String $id)
    {
        $user = UserModel::with('level')->find($id);

        $breadcrumb = (object) [
            'title' => 'Detail User',
            'list' => ['Home', 'User', 'Detail'],
        ];

        $page = (object) [
            'title' => 'Detail User',
        ];

        $activeMenu = 'user';

        return view('user.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'user' => $user]);
    }

    public function edit(String $id)
    {
        $user = UserModel::find($id);
        $level = LevelModel::all();

        $breadcrumb = (object) [
            'title' => 'Edit User',
            'list' => ['Home', 'User', 'Edit'],
        ];

        $page = (object) [
            'title' => 'Edit User',
        ];

        $activeMenu = 'user';

        return view('user.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'user' => $user, 'level' => $level, 'activeMenu' => $activeMenu, 'user' => $user]);
    }

    public function update(Request $request, String $id)
    {
        $request->validate([
            'username' => 'required|string|min:3|unique:m_user,username,' . $id . ',user_id',
            'nama' => 'required|string|max:100',
            'password' => 'required|min:5',
            'level_id' => 'required|integer',
        ]);

        UserModel::find($id)->update([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => $request->password ? bcrypt($request->password) : UserModel::find($id)->password,
            'level_id' => $request->level_id,
        ]);

        return redirect('/user')->with('success', 'Data user berhasil diubah');
    }

    public function destroy(String $id)
    {
        $check = UserModel::find($id);
        if (!$check) {
            return redirect('/user')->with('error', 'Data user tidak ditemukan');
        }

        try {
            UserModel::destroy($id);
            return redirect('/user')->with('success', 'Data user berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/user')->with('error', 'Data user gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
    {
        $level = LevelModel::all();
        return view('user.create_ajax')->with('level', $level);
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_id' => 'required|integer',
                'username' => 'required|string|min:3|unique:m_user,username',
                'nama' => 'required|string|max:100',
                'password' => 'required|min:6',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            UserModel::create(request()->all());
            return response()->json([
                'status' => true,
                'message' => 'Data user berhasil ditambahkan'
            ]);
        }
        redirect('/');
    }

    public function edit_ajax(String $id)
    {
        $user = UserModel::find($id);
        $level = LevelModel::all();
        return view('user.edit_ajax', ['user' => $user, 'level' => $level]);
    }

    public function update_ajax(Request $request, $id)
    { // cek apakah request dari ajax 
        if ($request->ajax() || $request->wantsJson()) {
            $rules = ['level_id' => 'required|integer', 'username' => 'required|max:20|unique:m_user,username,' . $id . ',user_id', 'nama' => 'required|max:100', 'password' => 'nullable|min:6|max:20'];
            // use Illuminate\Support\Facades\Validator; 
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false, // respon json, true: berhasil, false: gagal 
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors() // menunjukkan field mana yang error 
                ]);
            }
            $check = UserModel::find($id);
            if ($check) {
                if (!$request->filled('password')) { // jika password tidak diisi, maka hapus dari request 
                    $request->request->remove('password');
                }
                $check->update($request->all());
                return response()->json(['status' => true, 'message' => 'Data berhasil diupdate']);
            } else {
                return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(String $id)
    {
        $user = UserModel::find($id);
        return view('user.confirm_ajax', ['user' => $user]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $user = UserModel::find($id);
            if ($user) {
                $user->delete();
                return response()->json(['status' => true, 'message' => 'Data berhasil dihapus']);
            } else {
                return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
            }
        }
        return redirect('/');
    }

    public function upload_image(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('uploads/image_profile'), $filename);

            if ($user->image && file_exists(public_path('uploads/image_profile/' . $user->image))) {
                unlink(public_path('uploads/image_profile/' . $user->image));
            }

            $user->image = $filename;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'image berhasil diperbarui',
                'filename' => $filename
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak ada file yang diupload'
        ], 400);
    }

    public function edit_profile()
    {
        $user = Auth::user();

        $breadcrumb = (object) [
            'title' => 'Edit Profile',
            'list' => ['Home', 'User', 'Edit Profile'],
        ];

        return view('user.edit_profile', ['user' => $user, 'breadcrumb' => $breadcrumb]);
    }

    public function delete_image(Request $request)
    {
        $user = Auth::user();

        if ($user->image) {
            $path = 'uploads/image_profile/' . $user->image;

            if (Storage::exists($path)) {
                Storage::delete($path);
            }

            $user->image = null;
            $user->save();

            return response()->json(['success' => true, 'message' => 'image berhasil dihapus.']);
        }

        return response()->json(['success' => false, 'message' => 'image tidak ditemukan.']);
    }

    public function import()
    {
        return view('user.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [ // validasi file harus xls atau xlsx, max 1MB 
                'file_user' => ['required', 'mimes:xlsx', 'max:1024']
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => 'Validasi Gagal', 'msgField' => $validator->errors()]);
            }
            $file = $request->file('file_user'); // ambil file dari request
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
                            'level_id' => $value['A'],
                            'username' => $value['B'],
                            'nama' => $value['C'],
                            'password' => $value['D'],
                            'created_at' => now(),
                        ];
                    }
                }
                if (count($insert) > 0) { // insert data ke database, jika data sudah ada, maka diabaikan 
                    UserModel::insertOrIgnore($insert);
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
        $user = UserModel::select('level_id', 'username', 'nama','password')
            ->orderBy('level_id')
            ->with(['level'])
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Username');
        $sheet->setCellValue('C1', 'Nama');
        $sheet->setCellValue('D1', 'Password');
        $sheet->setCellValue('E1', 'Level');

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;
        foreach ($user as $key => $value) {
            $sheet->setCellValue('A' . $baris, $baris - 1);
            $sheet->setCellValue('B' . $baris, $value->username);
            $sheet->setCellValue('C' . $baris, $value->nama);
            $sheet->setCellValue('D' . $baris, $value->password);
            $sheet->setCellValue('E' . $baris, optional($value->level)->level_nama);
            $baris++;
            $no++;
        }

        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data User');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data User ' . date('Y-m-d H:i:s') . '.xlsx';

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
        $user = UserModel::select('user_id', 'level_id', 'username', 'nama', 'password')
            ->orderBy('user_id')
            ->with(['level'])
            ->get();

        $pdf = Pdf::loadView('user.export_pdf', ['user' => $user]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->render();

        return $pdf->download('Data User ' . date('Y-m-d H:i:s') . '.pdf');
    }

    // public function index()
    // {
    //     // $data = [
    //     //     'username' => 'customer-1',
    //     //     'nama' => 'pelanggan',
    //     //     'password' => Hash::make('12345'),
    //     //     'level_id' => 4
    //     // ];
    //     // UserModel::insert($data);

    //     // $data = [
    //     //     'nama' => 'Pelanggan Pertama',
    //     // ];
    //     // UserModel::where('username', 'customer-1')->update($data);

    //     // $data = [
    //     //     'level_id' => 2,
    //     //     'username' => 'manager_dua',
    //     //     'nama' => 'Manager 2',
    //     //     'password' => Hash::make('12345')
    //     // ];

    //     // $data = [
    //     //     'level_id' => 2,
    //     //     'username' => 'manager_tiga',
    //     //     'nama' => 'Manager 3',
    //     //     'password' => Hash::make('12345')
    //     // ];
    //     // UserModel::create($data);

    //     // $user = UserModel::all();
    //     // $user = UserModel::find(1);
    //     // $user = UserModel::where('level_id', 1)->first();
    //     // $user = UserModel::firstWhere('level_id', 1);

    //     // $user = UserModel::findOr(1, ['username', 'nama'], function () {
    //     //     abort(404);
    //     // });
    //     // $user = UserModel::findOr(20, ['username', 'nama'], function () {
    //     //     abort(404);
    //     // });

    //     // $user = UserModel::findOrFail(1);
    //     // $user = UserModel::where('username', 'manager9')->firstOrFail();
    //     // $user = UserModel::where('level_id', 2)->count();
    //     // dd($user);

    //     // $user = UserModel::firstOrCreate(
    //     //     [
    //     //         'username' => 'manager',
    //     //         'nama' => 'Manager',
    //     //     ],
    //     // );

    //     // $user = UserModel::firstOrCreate(
    //     //     [
    //     //         'username' => 'manager22',
    //     //         'nama' => 'Manager Dua Dua',
    //     //         'password' => Hash::make('12345'),
    //     //         'level_id' => 2
    //     //     ],
    //     // );

    //     // $user = UserModel::firstOrNew(
    //     //     [
    //     //         'username' => 'manager33',
    //     //         'nama' => 'Manager Tiga Tiga',
    //     //         'password' => Hash::make('12345'),
    //     //         'level_id' => 2
    //     //     ],
    //     // );
    //     // $user->save();

    //     // return view('user', ['data' => $user]);

    //     // $user = UserModel::create([
    //     //     'username' => 'manager55',
    //     //     'nama' => 'Manager Lima Lima',
    //     //     'password' => Hash::make('12345'),
    //     //     'level_id' => 2
    //     // ]);

    //     // $user->username = 'manager56';

    //     // $user->isDirty(); // true
    //     // $user->isDirty('username'); // true
    //     // $user->isDirty('nama'); // false
    //     // $user->isDirty(['nama', 'username']); // true

    //     // $user->isClean(); // false
    //     // $user->isClean('username'); // false
    //     // $user->isClean('nama'); // true
    //     // $user->isClean(['nama', 'username']); // false

    //     // $user->save();

    //     // $user->isDirty(); // false
    //     // $user->isClean(); // true
    //     // dd($user->isDirty());

    //     // $user = UserModel::create(
    //     //     [
    //     //         'username' => 'manager11',
    //     //         'nama' => 'Manager11',
    //     //         'password' => Hash::make('12345'),
    //     //         'level_id' => 2
    //     //     ]
    //     // );

    //     // $user->username = 'manager12';

    //     // $user->save();

    //     // $user->wasChanged(); // true
    //     // $user->wasChanged('username'); // true
    //     // $user->wasChanged(['username', 'level_id']); // true
    //     // $user->wasChanged('nama'); // false
    //     // dd($user->wasChanged(['nama', 'username'])); // true

    //     // $user = UserModel::all();
    //     // return view('user', ['data' => $user]);

    //     $user = UserModel::with('level')->get();
    //     // dd($user);
    //     return view('user', ['data' => $user]);
    // }

    // public function tambah()
    // {
    //     return view('user_tambah');
    // }

    // public function tambah_simpan(Request $request)
    // {
    //     UserModel::create([
    //         'username' => $request->username,
    //         'nama' => $request->nama,
    //         'password' => Hash::make($request->password),
    //         'level_id' => $request->level_id
    //     ]);

    //     return redirect('/user');
    // }

    // public function ubah($id)
    // {
    //     $user = UserModel::find($id);
    //     return view('user_ubah', ['data' => $user]);
    // }

    // public function ubah_simpan(Request $request, $id)
    // {
    //     $user = UserModel::find($id);

    //     $user->username = $request->username;
    //     $user->nama = $request->nama;
    //     $user->password = Hash::make($request->password);
    //     $user->level_id = $request->level_id;

    //     $user->save();

    //     return redirect('/user');
    // }

    // public function hapus($id)
    // {
    //     $user = UserModel::find($id);
    //     $user->delete();

    //     return redirect('/user');
    // }
}
