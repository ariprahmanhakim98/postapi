<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function store(Request $request)
    {
        // Validasi data yang diterima
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);//menampilkan kesalahan jika validasi tidak terpenuhi
        }

        // Simpan data pengguna
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Kembalikan response dengan status 201 dan data pengguna (kecuali password)
        return response()->json([
            'status' => 201,
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }

    public function destroy($id)
    {
        // Cari pengguna berdasarkan ID terlebih dahulu
        $user = User::find($id);

        // Jika pengguna tidak ditemukan, kembalikan respons 404
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'status' => 404
            ]);
        }

        // Mulai transaksi setelah memastikan pengguna ditemukan
        DB::beginTransaction();

        try {
            // Hapus semua postingan terkait pengguna
            $user->posts()->delete();

            // Hapus pengguna
            $user->delete();

            // Commit transaksi jika berhasil
            DB::commit();

            return response()->json([
                'message' => 'User and related posts deleted successfully',
                'status' => 200
            ]);
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete user and related posts',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'Invalid credentials', 'status' => 401]);
            }
        } catch (JWTException $e) {
            return response()->json(['message' => 'Could not create token', 'status' => 500]);
        }
        
        // Kembalikan token JWT jika autentikasi berhasil
        return response()->json(compact('token'));
    }


}
