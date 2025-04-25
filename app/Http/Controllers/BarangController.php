<?php
namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;


class BarangController extends Controller
{

    public function index (){

        $activeMenu = 'barang';
        $breadcrumb = (object) [
            'title' => 'Data Barang',
            'list' => ['Home', 'Barang']
        ];

        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();
        return view('barang.index', [
            'activeMenu' => $activeMenu,
            'breadcrumb' => $breadcrumb,
            'kategori' => $kategori
        ]);
    }

    public function list(Request $request)
    {
        // Ambil data dari model BarangModel dengan relasi kategori
        $barang = BarangModel::with('kategori')
            ->select('barang_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual', 'kategori_id');

        // Jika ada filter kategori
        $kategori_id = $request->input('filter_kategori');
        if (!empty($kategori_id)) {
            $barang->where('kategori_id', $kategori_id);
        }

        // Kembalikan dalam bentuk DataTables response
        return DataTables::of($barang->get()) // <= ini penting, agar relasi bisa ikut di-load
            ->addIndexColumn()
            ->addColumn('aksi', function ($barang) {
                $btn = '<div class="d-flex flex-wrap gap-1 justify-content-center">';
                $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button>';
                $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button>';
                $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
                $btn .= '</div>';
                return $btn;
            })
            
            ->addColumn('kategori_nama', function ($barang) {
                return $barang->kategori->kategori_nama ?? '-';
            })
            ->rawColumns(['aksi']) // render HTML untuk kolom aksi
            ->make(true);
    }

    public function create_ajax()
    {
        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();
        return  view('barang.create_ajax')->with('kategori', $kategori);
    }

    public function show_ajax($id)
{
    $barang = BarangModel::with('kategori')->find($id);
    return view('barang.show_ajax', compact('barang'));
}


    public function store_ajax(Request $request)
    {
        if($request->ajax() || $request->wantsJson()){
            $rules = [
                'kategori_id' => ['required', 'integer', 'exists:m_kategori,kategori_id'],
                'barang_kode' => ['required', 'min:3', 'max:20',
'unique:m_barang,barang_kode'],
                'barang_nama' => ['required', 'string', 'max:100'],
                'harga_beli' => ['required', 'numeric'],
                'harga_jual' => ['required', 'numeric'],
            ];

            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            BarangModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil disimpan'
            ]);
        }
        redirect('/');
    }

    public function edit_ajax($id)
    {
        $barang = BarangModel::find($id);
        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get(); // tambahkan ini
        $level = LevelModel::select('level_id', 'level_nama')->get();
        return view('barang.edit_ajax', ['barang' => $barang, 'kategori' => $kategori, 'level' => $level]);
    }
    

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_nama' => ['required', 'string', 'max:100'],
                'harga_beli' => ['required', 'numeric'],
                'harga_jual' => ['required', 'numeric'],
            ];
    
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }
    
            $barang = BarangModel::find($id);
            if ($barang) {
                $barang->update([
                    'barang_nama' => $request->barang_nama,
                    'harga_beli' => $request->harga_beli,
                    'harga_jual' => $request->harga_jual,
                ]);
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }
    
    public function confirm_ajax($id)
    {
        $barang = BarangModel::find($id);
        return view('barang.confirm_ajax', ['barang' => $barang]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if($request->ajax() || $request->wantsJson()){
            $barang = BarangModel::find($id);
            if($barang){ // jika sudah ditemuikan
                $barang->delete(); // barang di hapus
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil dihapus'
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function import()
    {
        return view('barang.import');
    }
    public function import_ajax(Request $request)
    {
        if($request->ajax() || $request->wantsJson()){
            $rules = [
                // validasi file harus xls atau xlsx, max 1MB
                'file_barang' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_barang');  // ambil file dari request

            $reader = IOFactory::createReader('Xlsx');  // load reader file excel
            $reader->setReadDataOnly(true);             // hanya membaca data
            $spreadsheet = $reader->load($file->getRealPath()); // load file excel
            $sheet = $spreadsheet->getActiveSheet();    // ambil sheet yang aktif

            $data = $sheet->toArray(null, false, true, true);   // ambil data excel

            $insert = [];
            if(count($data) > 1){ // jika data lebih dari 1 baris
                foreach ($data as $baris => $value) {
                    if($baris > 1){ // baris ke 1 adalah header, maka lewati
                        $insert[] = [
                            'kategori_id' => $value['A'],
                            'barang_kode' => $value['B'],
                            'barang_nama' => $value['C'],
                            'harga_beli' => $value['D'],
                            'harga_jual' => $value['E'],
                            'created_at' => now(),
                        ];
                    }
                }

                if(count($insert) > 0){
                    // insert data ke database, jika data sudah ada, maka diabaikan
                    BarangModel::insertOrIgnore($insert);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diimport'
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data yang diimport'
                ]);
            }
        }
        return redirect('/');
    }

    public function export_excel()
{
    // ambil data barang yang akan di export
    $barang = BarangModel::select('kategori_id','barang_kode','barang_nama','harga_beli','harga_jual')
        ->orderBy('kategori_id')
        ->with('kategori')
        ->get();

    // load library excel
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif

    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Kode Barang');
    $sheet->setCellValue('C1', 'Nama Barang');
    $sheet->setCellValue('D1', 'Harga Beli');
    $sheet->setCellValue('E1', 'Harga Jual');
    $sheet->setCellValue('F1', 'Kategori');
    $sheet->getStyle('A1:F1')->getFont()->setBold(true); // bold header

    $no = 1;                  // nomor data dimulai dari 1
    $baris = 2;               // baris data dimulai dari baris ke 2
    foreach ($barang as $key => $value) {
        $sheet->setCellValue('A'.$baris, $no);
        $sheet->setCellValue('B'.$baris, $value->barang_kode);
        $sheet->setCellValue('C'.$baris, $value->barang_nama);
        $sheet->setCellValue('D'.$baris, $value->harga_beli);
        $sheet->setCellValue('E'.$baris, $value->harga_jual);
        $sheet->setCellValue('F'.$baris, $value->kategori->kategori_nama); // ambil nama kategori
        $baris++;
        $no++;
    }

    // set auto size untuk kolom
    foreach(range('A','F') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    $sheet->setTitle('Data Barang'); // set title sheet

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $filename = 'Data Barang '.date('Y-m-d H:i:s').'.xlsx';

    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment;filename=\"$filename\"");
    header("Cache-Control: max-age=0");
    header("Cache-Control: max-age=1");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
    header("Cache-Control: cache, must-revalidate");
    header("Pragma: public");

    $writer->save('php://output');
    exit;
} // end function export_excel

public function export_pdf()
{
    $barang = BarangModel::select('kategori_id','barang_kode','barang_nama','harga_beli','harga_jual')
        ->orderBy('kategori_id')
        ->orderBy('barang_kode')
        ->with('kategori')
        ->get();

    // use Barryvdh\DomPDF\Facade\Pdf;
    $pdf = Pdf::loadView('barang.export_pdf', ['barang' => $barang]);
    $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi
    $pdf->setOption('isRemoteEnabled', true); // set true jika ada gambar dari url
    $pdf->render();

    return $pdf->stream('Data Barang ' . date('Y-m-d H:i:s') . '.pdf');
}

}