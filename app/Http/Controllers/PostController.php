<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{

    public function index(Request $request)
    {
         // Ambil parameter query dari request
        $search = $request->query('search');
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 5);

        // Query builder untuk mengambil postingan
        $query = Post::query();

        // Tambahkan pencarian jika parameter search diberikan
        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        // Paginasi
        $posts = $query->paginate($limit, ['*'], 'page', $page);

        // Kembalikan respons dengan data postingan
        return response()->json($posts);
    }

    public function getUserPosts($id)
    {
        // Cari pengguna berdasarkan ID
        $user = User::find($id);

        // Jika pengguna tidak ditemukan, kembalikan respons 404
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'status' => 404
            ]);
        }

        // Ambil semua postingan milik pengguna tersebut
        $posts = Post::where('user_id', $id)->get();

        // Jika pengguna tidak memiliki postingan, kembalikan pesan yang sesuai
        if ($posts->isEmpty()) {
            return response()->json([
                'message' => 'No posts found for this user',
                'status' => 404
            ]);
        }

        // Kembalikan postingan sebagai JSON
        return response()->json($posts, 201);
    }

    public function store(Request $request)
    {
        // Validasi data yang diterima
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);//menampilkan kesalahan jika validasi tidak terpenuhi
        }
        // Simpan data
        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'user_id' => $request->user_id,
        ]);
      
       //respon
        return response()->json([
            'status' => 201,
            'title' => $request->title,
            'body' => $request->body,
            'user_id' => $request->user_id
        ]);

    }

}
