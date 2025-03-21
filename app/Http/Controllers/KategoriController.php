<?php

namespace App\Http\Controllers;
use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Data Kategori Barang',
            'list'  => ['Home', 'User']
        ];

        $page = (object) [
            'title' => 'Data Kategori Barang'
        ];

        $activeMenu = 'kategori'; // set menu yang sedang aktif

        $kategori = KategoriModel::all();

        return view('kategori.index', [
            'breadcrumb' => $breadcrumb,
            'page'       => $page,
            'kategori'       => $kategori,
            'activeMenu' => $activeMenu
        ]);
    } 


    public function list(Request $request)
    {
        if ($request->ajax()) {
            $data = KategoriModel::orderBy('kategori_id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function($row){
                    $btn = '<a href="'.route('kategori.ajax.edit', $row->kategori_id).'" class="btn btn-sm btn-warning">Edit</a> ';
                    $btn .= '<button onclick="hapusKategori('.$row->kategori_id.')" class="btn btn-sm btn-danger">Hapus</button>';
                    return $btn;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return response()->json([
            'status' => false,
            'message' => 'Not an AJAX request'
        ], 400);
    }

    public function create_ajax()
    {
        return view('kategori.create_ajax');
    }
    
    

    
    // Proses simpan data kategori dari form create_ajax()
    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {

            $rules = [
                'kategori_kode' => 'required|string|max:10|unique:m_kategori,kategori_kode',
                'kategori_nama' => 'required|string|max:100'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            // Simpan data ke database
            KategoriModel::create([
                'kategori_kode' => $request->kategori_kode,
                'kategori_nama' => $request->kategori_nama
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data kategori berhasil disimpan'
            ]);
        }

        return redirect('/');
    }

    // Menampilkan halaman form edit kategori AJAX
    public function edit_ajax(string $id)
    {
        $kategori = KategoriModel::find($id);

        return view('kategori.edit_ajax', ['kategori' => $kategori]);
    }

    // Proses update data kategori dari form edit_ajax()
    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {

            $rules = [
                'kategori_kode' => 'required|string|max:10|unique:m_kategori,kategori_kode,' . $id . ',kategori_id',
                'kategori_nama' => 'required|string|max:100'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $kategori = KategoriModel::find($id);

            if ($kategori) {
                $kategori->update([
                    'kategori_kode' => $request->kategori_kode,
                    'kategori_nama'  => $request->kategori_nama
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Data kategori berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data kategori tidak ditemukan'
                ]);
            }
        }

        return redirect('/');
    }

      // Konfirmasi hapus kategori (bisa jadi modal konfirmasi)
      public function confirm_ajax(string $id)
      {
          $kategori = KategoriModel::find($id);
  
          return view('kategori.confirm_ajax', ['kategori' => $kategori]);
      }
  

// Delete kategori
public function delete_ajax($id)
{
    $kategori = KategoriModel::findOrFail($id);

    try {
        $kategori->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil dihapus!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Kategori gagal dihapus!',
            'error' => $e->getMessage() // opsional, buat debug kalau perlu
        ]);
    }
}


}
