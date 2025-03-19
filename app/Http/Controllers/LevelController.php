<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LevelModel;
use Yajra\DataTables\DataTables;

class LevelController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Level',
            'list'  => ['Home', 'Level']
        ];

        $page = (object) [
            'title' => 'Daftar Level yang terdaftar dalam sistem'
        ];

        $activeMenu = 'level'; // set menu yang sedang aktif

        $level = LevelModel::all();

        return view('level.index', [
            'breadcrumb' => $breadcrumb,
            'page'       => $page,
            'level'       => $level,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['status' => false, 'message' => 'Invalid Request'], 400);
        }
    
        $levels = LevelModel::select('level_id', 'level_kode', 'level_nama');
    
        return DataTables::of($levels)
            ->addIndexColumn()
            ->addColumn('aksi', function ($level) {
                $btn = '<button onclick="modalAction(\'' . url('/level/' . $level->level_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/level/' . $level->level_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="hapusLevel(' . $level->level_id . ')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
    
    
    

    public function create_ajax()
    {
        // Kalau di level tidak butuh data tambahan, langsung return view
        // Kalau butuh data tambahan (misal dropdown lain), tambahkan logic ambil datanya di sini

        return view('level.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'level_kode' => 'required|string|min:2|max:10|unique:m_level,level_kode',
            'level_nama' => 'required|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
    
        try {
            LevelModel::create([
                'level_kode' => $request->level_kode,
                'level_nama' => $request->level_nama
            ]);
    
            return response()->json([
                'status' => true,
                'message' => 'Data Level berhasil disimpan',
                'redirect' => route('level.index') // Tambahkan redirect di response JSON
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function confirm_ajax(string $id)
{
    $level = LevelModel::find($id);

    return view('level.confirm_ajax', ['level' => $level]);
}

// Delete level
// public function destroy($id)
// {
//     $level = LevelModel::findOrFail($id);

//     try {
//         $level->delete();
//         return response()->json([
//             'status' => 'success',
//             'message' => 'Level berhasil dihapus!'
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => 'error',
//             'message' => 'Level gagal dihapus!'
//         ]);
//     }
// }

public function delete_ajax($id)
{
    try {
        $level = LevelModel::findOrFail($id); // Ganti Level jadi LevelModel
        $level->delete();

        return response()->json([
            'message' => 'Data berhasil dihapus!'
        ]);
    } catch (\Exception $e) {
        \Log::error('Delete error: ' . $e->getMessage());
        return response()->json([
            'message' => 'Terjadi kesalahan!',
            'error' => $e->getMessage()
        ], 500);
    }
}




public function destroy_ajax($id)
{
    try {
        $level = LevelModel::findOrFail($id);
        $level->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Level berhasil dihapus'
        ]);
    } catch (\Exception $e) {
        \Log::error('Gagal hapus: ' . $e->getMessage());

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal menghapus level: ' . $e->getMessage()
        ], 500);
    }
}






    

}
