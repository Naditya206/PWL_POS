<?php

namespace App\Http\Controllers\Api;

use App\Models\UserModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'nama' => 'required',
            'password' => 'required',
            'level_id' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Ambil file dan generate nama hash
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        // Simpan user
        $user = UserModel::create([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => bcrypt($request->password),
            'level_id' => $request->level_id,
            'image' => $image->hashName() // hanya nama file yang disimpan
        ]);

        // Response jika berhasil
        if ($user) {
            return response()->json([
                'success' => true,
                'user' => $user
            ], 201);
        }

        // Response jika gagal
        return response()->json([
            'success' => false,
            'message' => 'Gagal menambahkan user'
        ], 409);
    }
}
