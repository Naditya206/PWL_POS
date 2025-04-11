<?php

namespace App\Http\Controllers;

use App\Models\SupplierModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;


class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Supplier',
            'list' => ['Home', 'Supplier']
        ];

        $page = (object) [
            'title' => 'Daftar supplier yang terdaftar dalam sistem',
        ];

        $activeMenu = 'supplier'; // untuk set menu yang sedang aktif

        return view('supplier.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function list()
    {
        $suppliers = SupplierModel::select('supplier_id', 'nama_supplier','kontak', 'alamat');

        return DataTables::of($suppliers)->addIndexColumn()->addColumn('aksi', function ($supplier) {

            $btn  = '<button onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
            $btn .= '<button onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
            $btn .= '<button onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id . '/delete_ajax') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
            return $btn;
        })->rawColumns(['aksi'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Supplier',
            'list' => ['Home', 'Supplier', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah supplier baru',
        ];

        $activeMenu = 'supplier'; // untuk set menu yang sedang aktif

        return view('supplier.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'    => 'required|integer|unique:m_supplier,supplier_id',
            'nama_supplier'  => 'required|string|max:100',
            'kontak'         => 'required|string|max:20',
            'alamat'         => 'required|string|max:255',
        ]);

        SupplierModel::create($request->only(['supplier_id', 'nama_supplier', 'kontak', 'alamat']));

        return redirect('/supplier')->with('success', 'Data supplier berhasil ditambahkan!');
    }


    public function create_ajax()
    {
        return view('supplier.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'supplier_id'    => 'required|integer|unique:m_supplier,supplier_id',
                'nama_supplier'  => 'required|string|max:100',
                'kontak'         => 'required|string|max:20',
                'alamat'         => 'required|string|max:255',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validasi Gagal',
                    'errors'  => $validator->errors(), // Perbaikan: gunakan 'errors' bukan 'msgField'
                ]);
            }

            $supplier = SupplierModel::create($request->only(['supplier_id', 'nama_supplier', 'kontak', 'alamat']));

            return response()->json([
                'status'  => true,
                'message' => 'Data supplier berhasil disimpan',
                'data'    => $supplier, // Perbaikan: Kirim data supplier yang berhasil disimpan
            ]);
        }
        return redirect('/supplier');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $breadcrumb = (object) [
            'title' => 'Detail Supplier',
            'list' => ['Home', 'Supplier', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail supplier',
        ];

        $activeMenu = 'supplier'; // untuk set menu yang sedang aktif

        $supplier = SupplierModel::find($id);

        return view('supplier.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'supplier' => $supplier, 'activeMenu' => $activeMenu]);
    }

    public function show_ajax(string $id)
    {
        $supplier = SupplierModel::find($id);

        return view('supplier.show_ajax', ['supplier' => $supplier]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $breadcrumb = (object) [
            'title' => 'Edit Supplier',
            'list' => ['Home', 'Supplier', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit supplier',
        ];

        $activeMenu = 'supplier'; // untuk set menu yang sedang aktif

        $supplier = SupplierModel::find($id);

        return view('supplier.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'supplier' => $supplier, 'activeMenu' => $activeMenu]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $request->validate([
        'nama_supplier' => 'required|string|max:100',
        'kontak'        => 'required|string|max:20',
        'alamat'        => 'required|string|max:255',
    ]);

    $supplier = SupplierModel::find($id);
    if (!$supplier) {
        return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan!');
    }

    $supplier->update([
        'nama_supplier' => $request->nama_supplier,
        'kontak'        => $request->kontak,
        'alamat'        => $request->alamat,
    ]);

    return redirect('/supplier')->with('success', 'Data supplier berhasil diubah!');
}

public function edit_ajax(string $id)
{
    $supplier = SupplierModel::find($id);
    return view('supplier.edit_ajax', ['supplier' => $supplier]);
}

public function update_ajax(Request $request, string $id)
{
    if ($request->ajax() || $request->wantsJson()) {
        $rules = [
            'nama_supplier' => 'required|string|max:100',
            'kontak'        => 'required|string|max:20',
            'alamat'        => 'required|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi Gagal',
                'errors'  => $validator->errors(),
            ]);
        }

        $supplier = SupplierModel::find($id);
        if (!$supplier) {
            return response()->json([
                'status'  => false,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        $supplier->update($request->only(['nama_supplier', 'kontak', 'alamat']));

        return response()->json([
            'status'  => true,
            'message' => 'Data berhasil diupdate',
            'data'    => $supplier,
        ]);
    }

    return redirect('/supplier');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $check = SupplierModel::find($id);

        if (!$check) {
            return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan!');
        }

        try {
            SupplierModel::destroy($id);

            return redirect('/supplier')->with('success', 'Data supplier berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/supplier')->with('error', 'Data supplier gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini!');
        }
    }

    public function confirm_ajax(string $id)
    {
        $supplier = SupplierModel::find($id);

        return view('supplier.confirm_ajax', ['supplier' => $supplier]);
    }

    public function delete_ajax(Request $request, $id)
{
    try {
        if ($request->ajax() || $request->wantsJson()) {
            $supplier = SupplierModel::find($id);
            if ($supplier) {
                $supplier->delete();
                return response()->json([
                    'status'  => true,
                    'message' => 'Data berhasil dihapus',
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan',
                ]);
            }
        }
    } catch (\Illuminate\Database\QueryException $e) {
        return response()->json([
            'status'  => false,
            'message' => 'Data supplier gagal dihapus karena masih terdapat tabel lain yang terkait',
        ]);
    }
    return redirect('/supplier');
    }

    public function import()
    {
        return view('supplier.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_supplier' => ['required', 'mimes:xlsx,xls', 'max:1024'] // max 1MB
            ];
    
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi Gagal',
                    'errors'   => $validator->errors()
                ]);
            }
    
            try {
                $file = $request->file('file_supplier');
                $spreadsheet = IOFactory::load($file);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();
    
                // Asumsikan baris pertama adalah header
                unset($rows[0]);
    
                $imported = 0;
                foreach ($rows as $row) {
                    $supplierId    = $row[0] ?? null;
                    $namaSupplier  = $row[1] ?? null;
                    $kontak        = $row[2] ?? null;
                    $alamat        = $row[3] ?? null;
    
                    // Validasi sederhana tiap baris (bisa dikembangkan)
                    if ($supplierId && $namaSupplier && $kontak && $alamat) {
                        // Hindari duplikasi berdasarkan supplier_id
                        $exists = SupplierModel::find($supplierId);
                        if (!$exists) {
                            SupplierModel::create([
                                'supplier_id'   => $supplierId,
                                'nama_supplier' => $namaSupplier,
                                'kontak'        => $kontak,
                                'alamat'        => $alamat,
                            ]);
                            $imported++;
                        }
                    }
                }
    
                return response()->json([
                    'status'  => true,
                    'message' => "$imported data supplier berhasil diimport.",
                ]);
    
            } catch (\Exception $e) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Terjadi kesalahan saat membaca file: ' . $e->getMessage()
                ]);
            }
        }
    
        return redirect('/supplier');
    }
    
    public function export_excel()
    {
        $suppliers = SupplierModel::all();
    
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Set header kolom
        $sheet->setCellValue('A1', 'ID Supplier');
        $sheet->setCellValue('B1', 'Nama Supplier');
        $sheet->setCellValue('C1', 'Kontak');
        $sheet->setCellValue('D1', 'Alamat');
    
        // Isi data mulai dari baris ke-2
        $row = 2;
        foreach ($suppliers as $supplier) {
            $sheet->setCellValue('A' . $row, $supplier->supplier_id);
            $sheet->setCellValue('B' . $row, $supplier->nama_supplier);
            $sheet->setCellValue('C' . $row, $supplier->kontak);
            $sheet->setCellValue('D' . $row, $supplier->alamat);
            $row++;
        }
    
        // Siapkan response untuk download
        $filename = 'data_supplier_' . date('Ymd_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    
        // Output file ke browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
    
        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
{
    $supplier = SupplierModel::select('supplier_id', 'nama_supplier', 'kontak', 'alamat')
        ->orderBy('supplier_id')
        ->get();

    $pdf = Pdf::loadView('supplier.export_pdf', ['supplier' => $supplier]);
    $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi
    $pdf->setOption('isRemoteEnabled', true); // jika ada gambar dari URL
    $pdf->render();

    return $pdf->stream('Data Supplier ' . date('Y-m-d H:i:s') . '.pdf');
}

    
}
