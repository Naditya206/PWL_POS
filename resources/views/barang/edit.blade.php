@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools"></div>
    </div>

    <div class="card-body">
        @empty($barang)
            <div class="alert alert-danger alert-dismissible">
                <h5><i class="icon fas fa-ban"></i> Kesalahan!</h5>
                Data yang Anda cari tidak ditemukan.
            </div>
            <a href="{{ url('barang') }}" class="btn btn-sm btn-default mt-2">Kembali</a>
        @else
        <form action="{{ url('/barang/update/' . $barang->barang_id) }}" method="POST">
            @csrf
            @method('PUT')
    
            <div class="form-group mb-3">
                <label for="barang_kode">Kode Barang</label>
                <input type="text" name="barang_kode" value="{{ old('barang_kode', $barang->barang_kode) }}" class="form-control" required>
                @error('barang_kode')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
    
            <div class="form-group mb-3">
                <label for="barang_nama">Nama Barang</label>
                <input type="text" name="barang_nama" value="{{ old('barang_nama', $barang->barang_nama) }}" class="form-control" required>
                @error('barang_nama')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
    
            <div class="form-group mb-3">
                <label for="kategori_id">Kategori ID</label>
                <input type="number" name="kategori_id" value="{{ old('kategori_id', $barang->kategori_id) }}" class="form-control" required>
                @error('kategori_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
    
            <div class="form-group mb-3">
                <label for="harga_beli">Harga Beli</label>
                <input type="number" name="harga_beli" value="{{ old('harga_beli', $barang->harga_beli) }}" class="form-control" required>
                @error('harga_beli')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
    
            <div class="form-group mb-3">
                <label for="harga_jual">Harga Jual</label>
                <input type="number" name="harga_jual" value="{{ old('harga_jual', $barang->harga_jual) }}" class="form-control" required>
                @error('harga_jual')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
    
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ url('/barang') }}" class="btn btn-secondary">Kembali</a>
        </form>
        @endempty
    </div>
</div>
@endsection

@push('css')
@endpush

@push('js')
@endpush
