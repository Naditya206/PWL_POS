<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    // Halaman daftar barang
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Barang',
            'list' => ['Home', 'Barang']
        ];

        $page = (object) [
            'title' => 'Daftar barang yang tersedia dalam sistem'
        ];

        $kategori = KategoriModel::all();
        $activeMenu = 'barang';

        return view('barang.index', compact('breadcrumb', 'page', 'kategori', 'activeMenu'));
    }

    // DataTables list
    public function list(Request $request)
    {
        if ($request->ajax()) {
            $data = BarangModel::with('kategori')->select([
                'barang_id',
                'barang_kode',
                'barang_nama',
                'kategori_id',
                'harga_beli',
                'harga_jual'
            ]);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kategori_nama', function($row){
                    return $row->kategori ? $row->kategori->kategori_nama : '-';
                })
                ->addColumn('aksi', function($row){
                    $editUrl = route('barang.edit', $row->barang_id);
                    $btnEdit = '<a href="'.$editUrl.'" class="btn btn-warning btn-sm">Edit</a>';
                    $btnDelete = '<button onclick="hapusBarang('.$row->barang_id.')" class="btn btn-danger btn-sm">Hapus</button>';
                    return $btnEdit.' '.$btnDelete;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

    // Create form
    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Barang',
            'list' => ['Home', 'Barang', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah Barang Baru'
        ];

        $kategori = KategoriModel::all();
        $activeMenu = 'barang';

        return view('barang.create', compact('breadcrumb', 'page', 'kategori', 'activeMenu'));
    }

    // Store barang baru
    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:m_kategori,kategori_id',
            'barang_kode' => 'required|string|max:50|unique:m_barang,barang_kode',
            'barang_nama' => 'required|string|max:100',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric'
        ]);

        BarangModel::create($request->all());

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    // Edit form
    public function edit($id)
    {
        $barang = BarangModel::findOrFail($id);

        $breadcrumb = (object) [
            'title' => 'Edit Barang',
            'list' => ['Home', 'Barang', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit Barang'
        ];

        $kategori = KategoriModel::all();
        $activeMenu = 'barang';

        return view('barang.edit', compact('breadcrumb', 'page', 'barang', 'kategori', 'activeMenu'));
    }

    // Update barang
    public function update(Request $request, $id)
    {
        $barang = BarangModel::findOrFail($id);

        $request->validate([
            'kategori_id' => 'required|exists:m_kategori,kategori_id',
            'barang_kode' => 'required|string|max:50|unique:m_barang,barang_kode,'.$barang->barang_id.',barang_id',
            'barang_nama' => 'required|string|max:100',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric'
        ]);

        $barang->update($request->all());

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui.');
    }

    // Delete barang
    public function destroy($id)
    {
        $barang = BarangModel::findOrFail($id);

        try {
            $barang->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Barang berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Barang gagal dihapus!'
            ]);
        }
    }

    public function create_ajax()
{
    $kategori = KategoriModel::all();

    return view('barang.create_ajax', compact('kategori'))->render();
}


        // Store AJAX (proses simpan)
        public function store_ajax(Request $request)
        {
            $validator = \Validator::make($request->all(), [
                'kategori_id' => 'required|exists:m_kategori,kategori_id',
                'barang_kode' => 'required|string|max:50|unique:m_barang,barang_kode',
                'barang_nama' => 'required|string|max:100',
                'harga_beli' => 'required|numeric',
                'harga_jual' => 'required|numeric'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors()
                ]);
            }
    
            try {
                BarangModel::create($request->all());
    
                return response()->json([
                    'status' => 'success',
                    'message' => 'Barang berhasil ditambahkan.'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Barang gagal ditambahkan.',
                    'error' => $e->getMessage()
                ]);
            }
            redirect('/');
        }

            // Edit AJAX (tampilkan data untuk form edit)
    public function edit_ajax($id)
    {
        $barang = BarangModel::find($id);
        $kategori = KategoriModel::all();

        if (!$barang) {
            return response()->json([
                'status' => 'error',
                'message' => 'Barang tidak ditemukan!'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data barang berhasil dimuat.',
            'data' => [
                'barang' => $barang,
                'kategori' => $kategori
            ]
        ]);
    }

        // Confirm AJAX (untuk menampilkan info sebelum hapus)
        public function confirm_ajax($id)
        {
            $barang = BarangModel::find($id);
    
            if (!$barang) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Barang tidak ditemukan!'
                ]);
            }
    
            return response()->json([
                'status' => 'success',
                'message' => 'Data barang ditemukan.',
                'data' => $barang
            ]);
        }

            // Delete AJAX (hapus data barang)
    public function delete_ajax($id)
    {
        $barang = BarangModel::find($id);

        if (!$barang) {
            return response()->json([
                'status' => 'error',
                'message' => 'Barang tidak ditemukan!'
            ]);
        }

        try {
            $barang->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Barang berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Barang gagal dihapus.',
                'error' => $e->getMessage()
            ]);
        }
    }
}
