<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;
use App\Models\LevelModel;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) { // jika sudah login, maka redirect ke halaman home
            return redirect('/');
        }
        return view('auth.login');
    }

    public function postLogin(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $credentials = $request->only('username', 'password');
            if (Auth::attempt($credentials)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Login Berhasil',
                    'redirect' => url('/')
                ]);
            }
            return response()->json([
                'status' => false,
                'message' => 'Login Gagal'
            ]);
        }
        return redirect('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }

    public function register()
    {
        $levels = LevelModel::all(); 
        return view('auth.register', compact('levels'));
    }

    public function postregister(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:20|unique:m_user,username',
            'nama' => 'required|string|max:100',
            'password' => 'required|string|min:5|confirmed',
            'level_id' => 'required|exists:m_level,level_id',
        ]);

        $user = new UserModel();
        $user->username = $request->username;
        $user->nama = $request->nama;
        $user->level_id = $request->level_id;
        $user->password = Hash::make($request->password); 

        $user->save();

        return redirect()->route('login')->with('success', 'Registrasi berhasil. Silakan login.');
    }
}
