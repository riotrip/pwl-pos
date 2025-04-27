<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\LevelModel;

class UserController extends Controller
{
    public function index()
    {
        return UserModel::with('level')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'nama' => 'required',
            'password' => 'required',
            'level_id' => 'required',
        ]);
        
        $user = UserModel::create($request->all());
        return response()->json($user, 201);
    }

    public function show(UserModel $user)
    {
        return $user->load('level');
    }

    public function update(Request $request, UserModel $user)
    {
        $request->validate([
            'username' => 'sometimes',
            'nama' => 'sometimes',
            'password' => 'sometimes',
            'level_id' => 'sometimes',
        ]);

        $user->update($request->all());
        return $user->load('level');
    }

    public function destroy(UserModel $user)
    {
        $user->delete();
        return response()->json([
            'status' => true,
            'message' => 'Data terhapus',
        ]);
    }
}
